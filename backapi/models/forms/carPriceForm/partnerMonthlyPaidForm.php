<?php
namespace backapi\models\forms\carPriceForm;

use common\models\CarPriceRequest;
use common\models\PartnerMonthCarPricePay;
use DateInterval;
use DatePeriod;
use yii\base\Model;
use yii\db\Expression;
use yii\helpers\ArrayHelper;


class partnerMonthlyPaidForm extends Model
{
    public $partner_id;
    public $from_date;
    public $till_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id'], 'required'],
            [['partner_id'], 'integer'],
            [['from_date', 'till_date'], 'date', 'format' => 'php:Y-m'],
        ];

    }

    public function save()
    {

        if (empty($this->from_date))
            $this->from_date = $this->getCreatedMonth();
        if (empty($this->till_date))
            $this->till_date = $this->getCreatedMonth('desc');

        $from_date = date_create_from_format('Y-m-d H:i:s', $this->from_date . '-01 00:00:00');
        $till_date = date_create_from_format('Y-m-d H:i:s', date($this->till_date . '-t 23:59:59'));

        $period = new DatePeriod(
            $from_date,
            new DateInterval('P1M'),
            $till_date
        );

        $request_graph_form = new requestGraphForm();
        $interval_in_sql = "to_char(created_at, 'YYYY-MM')";
        $request_graph_form->interval = 'month';
        $request_graph_form->sql = (new \yii\db\Query())
            ->select(["count('*') as count", "$interval_in_sql as interval"])
            ->from('car_price_request')
            ->andWhere([
                'and',
                ['>=', 'created_at', date($this->from_date . '-01 00:00:00')],
                ['<=', 'created_at', date($this->till_date . '-t 23:59:59')],
            ])
            ->groupBy(new Expression($interval_in_sql))
            ->orderBy(new Expression($interval_in_sql));

        $car_price_requests = $request_graph_form->getArray($this->partner_id, $period);
        $car_price_requests =  ArrayHelper::map($car_price_requests, 'interval', 'count');

        $monthly_pay = PartnerMonthCarPricePay::find()
            ->where(['partner_id' => $this->partner_id])
            ->asArray()->all();
        $monthly_pay = ArrayHelper::map($monthly_pay, 'month', 'is_paid');

        $result = [];
        foreach ($car_price_requests as $key => $car_price_request) {
            $result[] = [
                'month' => $key,
                'count' => $car_price_request,
                'is_paid' => $monthly_pay[$key] ?? false,
            ];
        }

        return $result;

    }

    public function getCreatedMonth($order_type = '')
    {
        $created_at = CarPriceRequest::find()
            ->where(['partner_id' => $this->partner_id])
            ->orderBy("created_at $order_type")->limit(1)->one()->created_at ?? "";

        if (!empty($created_at))
            $created_at = date_create_from_format('Y-m-d H:i:s', $created_at)->format('Y-m');

        return $created_at;
    }
}