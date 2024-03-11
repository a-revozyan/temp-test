<?php
namespace backapi\models\forms\homeForms;

use common\models\Accident;
use common\models\Agent;
use common\models\AgentFile;
use common\models\Kasko;
use common\models\KaskoBySubscriptionPolicy;
use common\models\Osago;
use common\models\Partner;
use common\models\Product;
use common\models\Travel;
use common\models\User;
use DateInterval;
use DatePeriod;
use Yii;
use yii\base\DynamicModel;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use function DeepCopy\deep_copy;


class salesGraphForm extends Model
{
    public const TYPE = [
        'count' => 0,
        'amount_uzs' => 1,
    ];

    public $from_date;
    public $till_date;
    public $interval;
    public $type;
    public $partner_id;

    public $product_sql;

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

    const TYPE_GRAPH_KEY = [
        self::TYPE['count'] => "count('*') as count",
        self::TYPE['amount_uzs'] => "sum(amount_uzs) as count",
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_date', 'till_date'], 'safe'],
            [['type'], 'integer'],
            [['type'], 'in', 'range' => self::TYPE],
            [['type'], 'default', 'value' => self::TYPE['count']],
            [['interval'], 'in', 'range' => ['day', 'month']],
            [['interval'], 'default', 'value' => function ($model, $attribute) {
                return 'day';
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

        $interval_in_sql = "to_char(to_timestamp(policy_generated_date), " . self::DATE_FORMAT[$this->interval] . ")";
        $this->product_sql = Product::products()
            ->select([self::TYPE_GRAPH_KEY[$this->type], "$interval_in_sql as interval"])
            ->where([
                'or',
                [
                    'and',
                    ['product' => Product::products['osago']],
                    ['in', 'status', [Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']]],
                ],
                [
                    'and',
                    ['product' => Product::products['kasko']],
                    ['in', 'status', [Kasko::STATUS['payed'], Kasko::STATUS['attached'], Kasko::STATUS['processed'], Kasko::STATUS['policy_generated']]],
                ],
                [
                    'and',
                    ['product' => Product::products['accident']],
                    ['in', 'status', [Accident::STATUS['payed'], Accident::STATUS['waiting_for_policy'], Accident::STATUS['received_policy']]],
                ],
                [
                    'and',
                    ['product' => Product::products['kasko-by-subscription']],
                    ['in', 'status', [KaskoBySubscriptionPolicy::STATUS['payed'], KaskoBySubscriptionPolicy::STATUS['waiting_for_policy'], KaskoBySubscriptionPolicy::STATUS['received_policy']]],
                ],
                [
                    'and',
                    ['product' => Product::products['travel']],
                    ['in', 'status', [Travel::STATUSES['payed'], Travel::STATUSES['waiting_for_policy'], Travel::STATUSES['received_policy']]],
                ],
            ])
            ->andWhere([
                'and',
                ['>=', 'policy_generated_date', $from_date->getTimestamp()],
                ['<=', 'policy_generated_date', $till_date->getTimestamp()],
                ['in', 'partner_id', $partner_ids]
            ])
            ->groupBy(new Expression($interval_in_sql))
            ->orderBy(new Expression($interval_in_sql));

        $period = new DatePeriod(
            $from_date,
            new DateInterval(self::INTERVAL[$this->interval]),
            $till_date
        );

        $hour = 'to_char(to_timestamp(payed_date), \'HH24\')';
        $osago_buy_hours = Osago::find()
            ->select(['count(id)', $hour . " as hour"])
            ->where(['in', 'status', [
                Osago::STATUS['payed'],
                Osago::STATUS['waiting_for_policy'],
                Osago::STATUS['received_policy'],
            ]])
            ->andWhere(['between', 'payed_date', $from_date->getTimestamp(), $till_date->getTimestamp()])
            ->andWhere(['in', 'partner_id', $partner_ids])
            ->groupBy([$hour])->orderBy([$hour."::integer" => 'desc'])->asArray()->all();

        return [
            'graph' => [
                'osago' => [
                    'limited' => $this->getArray(Product::products['osago'], $period, ['number_drivers_id' => Osago::TILL_5_NUMBER_DRIVERS_ID]),
                    'no_limit' => $this->getArray(Product::products['osago'], $period, ['number_drivers_id' => Osago::NO_LIMIT_NUMBER_DRIVERS_ID]),
                ],
                'kasko' => $this->getArray(Product::products['kasko'], $period),
                'accident' => $this->getArray(Product::products['accident'], $period),
                'kbsp' => $this->getArray(Product::products['kasko-by-subscription'], $period),
                'travel' => $this->getArray(Product::products['travel'], $period),
            ],
            'osago_by_hours' => $osago_buy_hours,
        ];
    }

    private function getArray($product, $period, $filter = [])
    {
        $_graph = [];
        foreach ($period as $item) {
            $_graph[$item->format(self::DATE_FORMAT_IN_PHP[$this->interval])] = 0;
        }

        $products = clone $this->product_sql;
        $products->andWhere(['product' => $product]);
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