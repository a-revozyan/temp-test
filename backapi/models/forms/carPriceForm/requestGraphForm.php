<?php
namespace backapi\models\forms\carPriceForm;

use Cassandra\Date;
use common\models\CarPriceRequest;
use common\models\Partner;
use DateInterval;
use DatePeriod;
use Yii;
use yii\base\DynamicModel;
use yii\base\Model;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;


class requestGraphForm extends Model
{

    public $from_date;
    public $till_date;
    public $interval;
    public $partner_id;

    public $sql;

    const DATE_FORMAT = [
        'day' => '\'YYYY-MM-DD\'',
        'month' => '\'YYYY-MM\'',
    ];

    const DATE_FORMAT_IN_PHP = [
        'day' => 'Y-m-d',
        'month' => 'Y-m',
    ];

    const INTERVAL = [
        'day' => 'P1D',
        'month' => 'P1M',
    ];

    const DEFAULT_GO_BACK = [
        'day' => "-30 days",
        'month' => "-1 year",
    ];

    const END_OF_INTERVAL = [
        'day' => " 23:59:59",
        'month' => "-t 23:59:59",
    ];

    const BEGIN_OF_INTERVAL = [
        'day' => " 00:00:00",
        'month' => "-01 00:00:00",
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_date', 'till_date'], 'safe'],
            [['interval'], 'in', 'range' => ['day', 'month']],
            [['interval'], 'default', 'value' => function ($model, $attribute) {
                return 'month';
            }],
            [['partner_id'], 'integer']
        ];

    }

    public function save()
    {
        $model = DynamicModel::validateData($this->attributes, [
            [['from_date', 'till_date'], 'date', 'format' => 'php:' . self::DATE_FORMAT_IN_PHP[$this->interval]],
            [['till_date'], 'default', 'value' => function ($model, $attribute) {
                return date(self::DATE_FORMAT_IN_PHP[$this->interval]);
            }]
        ]);

        if ($model->hasErrors()) {
            return Yii::$app->controller->sendFailedResponse($model->errors, 422);
        }

        $partner_ids = [$this->partner_id];
        if (empty($this->partner_id))
            $partner_ids = ArrayHelper::getColumn(Partner::find()->asArray()->all(), 'id');

        $till_date = date_create_from_format('Y-m-d H:i:s', date($model->till_date . self::END_OF_INTERVAL[$this->interval]));

        $from_date = $this->from_date;
        if (empty($from_date))
            $from_date = date(self::DATE_FORMAT_IN_PHP[$this->interval], strtotime(self::DEFAULT_GO_BACK[$this->interval], $till_date->getTimestamp()));

        $from_date = date_create_from_format('Y-m-d H:i:s', date($from_date . self::BEGIN_OF_INTERVAL[$this->interval]));

        $interval_in_sql = "to_char(created_at, " . self::DATE_FORMAT[$this->interval] . ")";
        $this->sql = (new \yii\db\Query())
            ->select(["count('*') as count", "$interval_in_sql as interval"])
            ->from('car_price_request')
            ->andWhere([
                'and',
                ['>=', 'created_at', $from_date->format('Y-m-d H:i:s')],
                ['<=', 'created_at', $till_date->format('Y-m-d H:i:s')],
                ['in', 'partner_id', $partner_ids]
            ])
            ->groupBy(new Expression($interval_in_sql))
            ->orderBy(new Expression($interval_in_sql));

        $period = new DatePeriod(
            $from_date,
            new DateInterval(self::INTERVAL[$this->interval]),
            $till_date
        );

        $partners = Partner::find()->where(['status' => 1, 'id' => $partner_ids])->all();
        $graph = [];
        foreach ($partners as $partner) {
            $graph[$partner->name] = $this->getArray($partner->id, $period);
        }

        $current_year = CarPriceRequest::find()
            ->select(['min(partner.name) as partner_name', 'count(car_price_request.id)'])
            ->leftJoin('partner', 'partner.id=car_price_request.partner_id')
            ->where(['>', 'car_price_request.created_at',  date('Y-01-01 00:00:00')])
            ->groupBy('partner_id')
            ->asArray()->all();

        $current_month = CarPriceRequest::find()
            ->select(['min(partner.name) as partner_name', 'count(car_price_request.id)'])
            ->leftJoin('partner', 'partner.id=car_price_request.partner_id')
            ->where(['>', 'car_price_request.created_at',  date('Y-m-01 00:00:00')])
            ->groupBy('partner_id')
            ->asArray()->all();

        $period_statistics = CarPriceRequest::find()
            ->select(['min(partner.name) as partner_name', 'count(car_price_request.id)'])
            ->leftJoin('partner', 'partner.id=car_price_request.partner_id');

        if (!empty($this->from_date))
            $period_statistics->andWhere(['>', 'car_price_request.created_at', date( $this->from_date . "-01 00:00:00")]);
        if (!empty($this->till_date))
            $period_statistics->andWhere(['<', 'car_price_request.created_at',  date($this->till_date . "-t 23:59:59")]);

        $period_statistics = $period_statistics->groupBy('partner_id')
            ->asArray()->all();

        $top_auto_models = CarPriceRequest::find()
            ->select(['min(partner_auto_model.name) as auto_model_name', 'count(car_price_request.id)'])
            ->leftJoin('partner_auto_model', 'partner_auto_model.id=car_price_request.model_id')
            ->groupBy('partner_auto_model.id')
            ->limit(5)
            ->asArray()->all();

        return [
            'graph' => $graph,
            'statistics' => [
                'current_year' => $current_year,
                'current_month' => $current_month,
                'period' => $period_statistics,
            ],
            'top_auto_models' => $top_auto_models
        ];
    }

    public function getArray($partner_id, $period, $filter = [])
    {
        $_graph = [];
        foreach ($period as $item) {
            $_graph[$item->format(self::DATE_FORMAT_IN_PHP[$this->interval])] = 0;
        }

        $products = clone $this->sql;
        $products->andWhere(['partner_id' => $partner_id]);
        if (!empty($filter))
            $products->andWhere($filter);

        $graph = $products->createCommand()->queryAll();

        foreach ($graph as $item) {
            $_graph[$item['interval']] = $item['count'];
        }

        $graph = [];
        foreach ($_graph as $day => $count) {
            $graph[] = [
                'interval' => $day,
                'count' => $count,
            ];
        }

        return $graph;
    }

}