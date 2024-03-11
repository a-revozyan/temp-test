<?php

namespace backapi\models\searchs;

use common\models\Accident;
use common\models\Autocomp;
use common\models\Automodel;
use common\models\Kasko;
use common\models\KaskoBySubscriptionPolicy;
use common\models\Osago;
use common\models\Partner;
use common\models\Travel;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class HomeStatisticsSearch extends Model
{
    public $begin_date;
    public $end_date;
    public $partner_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['begin_date'], 'required'],
            [['partner_id'], 'integer'],
            [['end_date'], 'safe'],
        ];
    }

    public function search($params = [])
    {
        $this->setAttributes($params);
        if (!$this->validate()) {
            return [];
        }
        if (empty($this->end_date))
            $this->end_date = date('Y-m-d');

        $this->begin_date = $this->checkDate($this->begin_date, 1);
        $this->end_date = $this->checkDate($this->end_date, 't');

        $partner_ids = [$this->partner_id];
        if (empty($this->partner_id))
            $partner_ids = ArrayHelper::getColumn(Partner::find()->asArray()->all(), 'id');

        //policies_of_process
        $osago_in_progress = Osago::find()->where(['in', 'status', [
            Osago::STATUS['payed'],
            Osago::STATUS['waiting_for_policy'],
        ]])->andWhere(['in', 'partner_id', $partner_ids])->count();
        $accident_in_progress = Accident::find()->where(['in', 'status', [
            Accident::STATUS['payed'],
            Accident::STATUS['waiting_for_policy'],
        ]])->andWhere(['in', 'partner_id', $partner_ids])->count();
        $casco_in_progress = Kasko::find()->where(['in', 'status', [
            Kasko::STATUS['payed'],
            Kasko::STATUS['attached'],
        ]])->andWhere(['in', 'partner_id', $partner_ids])->count();
        $kbsp_in_progress = KaskoBySubscriptionPolicy::find()->where(['in', 'status', [
            KaskoBySubscriptionPolicy::STATUS['payed'],
            KaskoBySubscriptionPolicy::STATUS['waiting_for_policy'],
        ]])->andWhere(['in', 'partner_id', $partner_ids])->count();
        $travel_in_progress = Travel::find()->where(['in', 'status', [
            Travel::STATUSES['payed'],
            Travel::STATUSES['waiting_for_policy'],
        ]])->andWhere(['in', 'partner_id', $partner_ids])->count();
        $policies_of_process = $casco_in_progress + $osago_in_progress + $accident_in_progress + $kbsp_in_progress + $travel_in_progress;
        //policies_of_process

        $users_count = User::find()->count();

        //users increase
        $now = time();
        $last_monday = strtotime('-1 Monday');
        $last_week_today = strtotime('-7 days');
        $last_week_monday = strtotime('-2 Monday');
        $this_week_users_count = User::find()->where(['between', 'created_at', $last_monday, $now])->count();
        $last_week_users_count = User::find()->where(['between', 'created_at', $last_week_monday, $last_week_today])->count();

        if ($last_week_users_count == 0 and $this_week_users_count > 0)
            $weekly_increase_percent = Yii::t('app', 'cheksiz');
        elseif($last_week_users_count == 0 and $this_week_users_count == 0)
            $weekly_increase_percent = 0;
        else
            $weekly_increase_percent = ($this_week_users_count / $last_week_users_count - 1) * 100;
        //users increase

        //monthly_product_count
        $beginning_of_month = date_create_from_format('Y-m-d', $this->begin_date)->setTime(0, 0, 0)->getTimestamp();
        $ending_of_month = date_create_from_format('Y-m-d', $this->end_date)->setTime(23, 59, 59)->getTimestamp();

        $monthly_sold_casco_condition =[
            'and',
            ['in', 'status', [Kasko::STATUS['payed'], Kasko::STATUS['attached'], Kasko::STATUS['processed'], Kasko::STATUS['policy_generated']]],
            ['between', 'payed_date', $beginning_of_month, $ending_of_month],
            ['in', 'partner_id', $partner_ids],
        ];
        $monthly_sold_travel_condition = [
            'and',
            ['in', 'status', [Travel::STATUSES['payed'], Travel::STATUSES['waiting_for_policy'], Travel::STATUSES['received_policy']]],
            ['between', 'payed_date', $beginning_of_month, $ending_of_month],
            ['in', 'partner_id', $partner_ids],
        ];
        $monthly_sold_osago_condition = [
            'and',
            ['in', 'status', [
                Osago::STATUS['payed'],
                Osago::STATUS['waiting_for_policy'],
                Osago::STATUS['received_policy'],
            ]],
            ['between', 'payed_date', $beginning_of_month, $ending_of_month],
            ['in', 'partner_id', $partner_ids],
        ];
        $monthly_sold_accident_condition = [
            'and',
            ['in', 'status', [
                Accident::STATUS['payed'],
                Accident::STATUS['waiting_for_policy'],
                Accident::STATUS['received_policy'],
            ]],
            ['between', 'payed_date', date('Y-m-d H:i:s', $beginning_of_month), date('Y-m-d H:i:s', $ending_of_month)],
            ['in', 'partner_id', $partner_ids],
        ];
        $monthly_sold_kbsp_condition = [
            'and',
            ['in', 'kasko_by_subscription_policy.status', [
                KaskoBySubscriptionPolicy::STATUS['payed'],
                KaskoBySubscriptionPolicy::STATUS['waiting_for_policy'],
                KaskoBySubscriptionPolicy::STATUS['received_policy'],
            ]],
            ['between', 'kasko_by_subscription_policy.payed_date', date('Y-m-d H:i:s', $beginning_of_month), date('Y-m-d H:i:s', $ending_of_month)],
            ['in', 'partner_id', $partner_ids],
        ];

        $monthly_casco = Kasko::find()->where($monthly_sold_casco_condition)->count();
        $monthly_travel = Travel::find()->where($monthly_sold_travel_condition)->count();
        $monthly_osago = Osago::find()->where($monthly_sold_osago_condition)->count();
        $monthly_accident = Accident::find()->where($monthly_sold_accident_condition)->count();
        $monthly_kbsp = KaskoBySubscriptionPolicy::find()->where($monthly_sold_kbsp_condition)->count();
        $monthly_product_count = $monthly_casco + $monthly_travel + $monthly_osago + $monthly_accident + $monthly_kbsp;
        //monthly_product_count

        //average_check
        $monthly_casco_sum = Kasko::find()->where($monthly_sold_casco_condition)->sum('amount_uzs');
        $monthly_travel_sum = Travel::find()->where($monthly_sold_travel_condition)->sum('amount_uzs');
        $monthly_osago_sum = Osago::find()->where($monthly_sold_osago_condition)->sum('amount_uzs');
        $monthly_accident_sum = Accident::find()->where($monthly_sold_accident_condition)->sum('amount_uzs');
        $monthly_kbsp_sum = KaskoBySubscriptionPolicy::find()->where($monthly_sold_kbsp_condition)->sum('amount_uzs');

        $monthly_amount_sum = $monthly_casco_sum + $monthly_travel_sum + $monthly_osago_sum + $monthly_accident_sum + $monthly_kbsp_sum;
        $average_check = 0;
        if ($monthly_product_count != 0)
            $average_check = round($monthly_amount_sum / $monthly_product_count, -3);

        $average_osago_check = 0;
        if ($monthly_osago + $monthly_accident != 0)
            $average_osago_check = round(($monthly_osago_sum + $monthly_accident_sum) / ($monthly_osago + $monthly_accident), -3);
        //average_check

        //top partners
        $top_partners = Partner::find()
            ->select([
                'partner.name',
                'COALESCE(travel_by_partner.travel_count, 0) + COALESCE(kasko_by_partner.kasko_count, 0) + COALESCE(osago_by_partner.osago_count, 0) + COALESCE(accident_by_partner.accident_count, 0) + COALESCE(kbsp_by_partner.kbsp_count, 0) as product_count'])
            ->where(['status' => 1])
            ->andWhere(['in', 'partner.id', $partner_ids])
            ->leftJoin(
                [
                    'travel_by_partner' => Travel::find()->select(['count(id) as travel_count', 'partner_id'])->where($monthly_sold_travel_condition)
                        ->groupBy('partner_id')
                ],
                ' partner.id = travel_by_partner.partner_id'
            )
            ->leftJoin(
                [
                    'kasko_by_partner' => Kasko::find()->select(['count(id) as kasko_count', 'partner_id'])->where($monthly_sold_casco_condition)
                        ->groupBy('partner_id')
                ],
                'partner.id = kasko_by_partner.partner_id'
            )
            ->leftJoin(
                [
                    'osago_by_partner' => Osago::find()->select(['count(id) as osago_count', 'partner_id'])->where($monthly_sold_osago_condition)
                        ->groupBy('partner_id')
                ],
                'partner.id = osago_by_partner.partner_id'
            )
            ->leftJoin(
                [
                    'accident_by_partner' => Accident::find()->select(['count(id) as accident_count', 'partner_id'])->where($monthly_sold_accident_condition)
                        ->groupBy('partner_id')
                ],
                'partner.id = accident_by_partner.partner_id'
            )
            ->leftJoin(
                [
                    'kbsp_by_partner' => KaskoBySubscriptionPolicy::find()->select(['count(id) as kbsp_count', 'partner_id'])->where($monthly_sold_kbsp_condition)
                        ->groupBy('partner_id')
                ],
                'partner.id = accident_by_partner.partner_id'
            )
            ->orderBy('product_count desc')
            ->limit(5)
            ->createCommand()->queryAll();
        //top partners

        //top auto
        $top_auto = Osago::find()
            ->select(['count(osago.id) as count', 'gross_auto.name'])
            ->leftJoin('gross_auto', 'gross_auto.id=osago.gross_auto_id')
            ->where($monthly_sold_osago_condition)
            ->groupBy('gross_auto.name')
            ->orderBy('count desc')
            ->limit(5)
            ->createCommand()->queryAll();
        //top_auto

        $new_users = User::find()->where([
            'and',
            ['between', 'created_at', $beginning_of_month, $ending_of_month],
            ['=', 'status',  User::STATUS_ACTIVE],
        ])->count('id');

        return [
            'policies_of_process' => [
                'total' => $policies_of_process,
                'casco_in_progress' => $casco_in_progress,
                'osago_in_progress' => $osago_in_progress,
                'accident_in_progress' => $accident_in_progress,
                'kbsp_in_progress' => $kbsp_in_progress,
                'travel_in_progress' => $travel_in_progress,
            ],
            'users' => $users_count,
            'new_users' => $new_users,
            'weekly_increase_percent' => is_numeric($weekly_increase_percent) ? round($weekly_increase_percent) : $weekly_increase_percent,
            'product_count' => [
                'total' => $monthly_product_count,
                'osago' => $monthly_osago,
                'kasko' => $monthly_casco,
                'accident' => $monthly_accident,
                'kbsp' => $monthly_kbsp,
                'travel' => $monthly_travel,
            ],
            'amount_sum' => [
                'total' => $monthly_amount_sum,
                'osago' => $monthly_osago_sum,
                'kasko' => $monthly_casco_sum,
                'accident' => $monthly_accident_sum,
                'kbsp' => $monthly_kbsp_sum,
                'travel' => $monthly_travel_sum,
            ],
            'average_check' => $average_check,
            'average_osago_check' => $average_osago_check,
            'top_partners' => $top_partners,
            'top_auto' => $top_auto,
        ];
    }

    public function checkDate($date, $default_day)
    {
        $date_arr = explode('-', $date);

        if (count($date_arr) == 3)
            [$year, $month, $day] = $date_arr;
        elseif (count($date_arr) == 2)
            [$year, $month, $day] = [...$date_arr, 1];
        else
            throw new BadRequestHttpException('begin_date and end_date should be Y-m-d or Y-m format');

        if ((int)$month != $month or (int)$day != $day or (int)$year != $year or !checkdate($month, $day, $year))
            throw new BadRequestHttpException('begin_date and end_date should be Y-m-d or Y-m format');

        if (count($date_arr) == 3)
            return "$year-$month-$day";
        return date("Y-m-$default_day", strtotime("$year-$month-$day"));
    }
}
