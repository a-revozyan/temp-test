<?php
namespace backapi\models\forms\fUserForms;

use common\models\Accident;
use common\models\Kasko;
use common\models\KaskoBySubscriptionPolicy;
use common\models\Osago;
use common\models\Travel;
use common\models\User;
use yii\base\Model;

class GetById extends Model
{
    public $id;
    public $from_date;
    public $till_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_date', 'till_date', 'id'], 'required'],
            [['from_date', 'till_date'], 'date', 'format' => 'php:Y-m-d'],
            [['id'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id' => 'id'], 'filter' => function($query){
                return $query->where(['not', ['status' => User::STATUS_DELETED]])
                    ->andWhere(['role' => User::ROLES['user']]);
            }],
        ];
    }

    public function save()
    {
        $time_period_where_for_timestemp = [
            'and',
            ['>', 'payed_date', date_create_from_format('Y-m-d H:i:s', date($this->from_date . " 00:00:00"))->getTimestamp()],
            ['<', 'payed_date', date_create_from_format('Y-m-d H:i:s', date($this->till_date . " 23:59:59"))->getTimestamp()]
        ];

        $time_period_where_for_format = [
            'and',
            ['>', 'payed_date', $this->from_date . " 00:00:00"],
            ['<', 'payed_date', $this->till_date . " 23:59:59"]
        ];

        $user = User::findOne(['id' => $this->id]);

        $travel = Travel::find()->where(['in', 'status', [
            Travel::STATUSES['payed'],
            Travel::STATUSES['waiting_for_policy'],
            Travel::STATUSES['received_policy'],
        ]])
            ->andWhere($time_period_where_for_timestemp)
            ->andWhere(['f_user_id' => $user->id]);
        $kasko = Kasko::find()->where(['in', 'status', [
            Kasko::STATUS['payed'],
            Kasko::STATUS['attached'],
            Kasko::STATUS['processed'],
            Kasko::STATUS['policy_generated'],
        ]])
            ->andWhere($time_period_where_for_timestemp)
            ->andWhere(['f_user_id' => $user->id]);
        $osago = Osago::find()->where(['in', 'status', [
            Osago::STATUS['payed'],
            Osago::STATUS['waiting_for_policy'],
            Osago::STATUS['received_policy'],
        ]])
            ->andWhere($time_period_where_for_timestemp)
            ->andWhere(['f_user_id' => $user->id]);
        $accident = Accident::find()->where(['in', 'status', [
            Accident::STATUS['payed'],
            Accident::STATUS['waiting_for_policy'],
            Accident::STATUS['received_policy'],
        ]])
            ->andWhere($time_period_where_for_format)
            ->andWhere(['f_user_id' => $user->id]);
        $kbsp = KaskoBySubscriptionPolicy::find()->where(['in', 'kasko_by_subscription_policy.status', [
            KaskoBySubscriptionPolicy::STATUS['payed'],
            KaskoBySubscriptionPolicy::STATUS['waiting_for_policy'],
            KaskoBySubscriptionPolicy::STATUS['received_policy'],
        ]])->leftJoin('kasko_by_subscription', 'kasko_by_subscription.id = kasko_by_subscription_policy.kasko_by_subscription_id')
            ->andWhere(['kasko_by_subscription.f_user_id' => $user->id])
            ->andWhere($time_period_where_for_format);

        $autos = [];
        $kasko_for_autos = (clone $kasko)->with('autocomp.automodel.autobrand')->all();
        foreach ($kasko_for_autos as $key => $kasko_for_auto) {
            $autos[] = ($kasko_for_auto->autocomp->automodel->autobrand->name ?? "")
                . " " . ($kasko_for_auto->autocomp->automodel->name ?? "")
                . " " . ($kasko_for_auto->autocomp->name ?? "");
        }
        $autos = implode(', ', array_unique($autos));

        $countries = [];
        $travel_for_countries = (clone $travel)->with('tCountries')->all();
        foreach ($travel_for_countries as $key => $travel_for_country) {
            $countries = array_merge($countries, array_map(
                fn ($country) => $country->name_ru,
                $travel_for_country->tCountries
            ));
        }
        $countries = implode(', ', array_unique($countries));

        return [
            'id' => $this->id,
            'name' => $user->first_name . " " . $user->last_name,
            'phone' => $user->phone,
            'created_at' => date('d.m.Y', $user->created_at),
            'age' => is_null($user->birthday) ? null : date_create($user->birthday)->diff(date_create())->y + 1,
            'gender' => $user->gender,
            'policy_count' => $travel->count() + $kasko->count() + $osago->count() + $accident->count() + $kbsp->count(),
            'policy_amount_uzs' => $travel->sum('amount_uzs') + $kasko->sum('amount_uzs')  + $osago->sum('amount_uzs') + $accident->sum('amount_uzs') + $kbsp->sum('kasko_by_subscription_policy.amount_uzs'),
            "autos" => $autos,
            "countries" => $countries,
            "comment" => $user->comment,
        ];
    }

}