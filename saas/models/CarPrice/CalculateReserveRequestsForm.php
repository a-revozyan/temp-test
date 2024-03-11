<?php
namespace saas\models\CarPrice;

use common\helpers\DateHelper;
use common\models\CarPriceRequest;
use common\models\Setting;
use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class CalculateReserveRequestsForm extends Model
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    public function save()
    {
        $period = Setting::findOne(Setting::SETTING_ID['car_price_requests_period'])->value ?? null;
        $limit = Setting::findOne(Setting::SETTING_ID['car_price_requests_limit'])->value ?? null;

        if (is_null($period) or is_null($limit))
            throw new NotFoundHttpException('period or limit does not exist in setting table');

        $date_condition = [];
        if ($period == Setting::PERIOD['day'])
            $date_condition = [
                'and',
                ['>', 'created_at', date('Y-m-d 00:00:00')],
                ['<', 'created_at', date('Y-m-d 23:59:59')],
            ];
        elseif ($period == Setting::PERIOD['week'])
        {
            $day = date('w');
            $week_start = date('Y-m-d 00:00:00', strtotime('-'.$day.' days'));
            $week_end = date('Y-m-d 23:59:59', strtotime('+'.(6-$day).' days'));
            $date_condition = [
                'and',
                ['>', 'created_at', $week_start],
                ['<', 'created_at', $week_end],
            ];
        }
        elseif ($period == Setting::PERIOD['month'])
        {
            $date_condition = [
                'and',
                ['>', 'created_at', date('Y-m-1 00:00:00')],
                ['<', 'created_at', date('Y-m-t 23:59:59')],
            ];
        }
        elseif ($period == Setting::PERIOD['quarter'])
        {
            ['start' => $quarter_start, 'end' => $quarter_end] = DateHelper::get_dates_of_quarter('current', null, 'Y-m-d H:i:s');
            $date_condition = [
                'and',
                ['>', 'created_at', $quarter_start],
                ['<', 'created_at', $quarter_end],
            ];
        }
        elseif ($period == Setting::PERIOD['year'])
        {
            $date_condition = [
                'and',
                ['>', 'created_at', date('Y-1-1 00:00:00')],
                ['<', 'created_at', date('Y-12-t 23:59:59')],
            ];
        }

        $sent_requests = CarPriceRequest::find()
            ->where(['fuser_id' => Yii::$app->user->identity->getId()])
            ->andWhere($date_condition)
            ->count();


        return ["reserve_requests" => $limit - $sent_requests];
    }
}