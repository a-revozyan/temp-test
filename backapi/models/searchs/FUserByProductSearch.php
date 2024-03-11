<?php

namespace backapi\models\searchs;

use common\models\Autobrand;
use common\models\Kasko;
use common\models\Osago;
use common\models\Product;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\BadRequestHttpException;

class FUserByProductSearch extends Autobrand
{
    public $from_date;
    public $till_date;
    public $product;
    public $type;

    public const TYPE = [
        'sb' => 0,
        'stranger' => 1,
    ];

    public const statuses = [
        Product::products['osago'] => [
            self::TYPE['sb'] => [Osago::STATUS['received_policy']],
            self::TYPE['stranger'] => [Osago::STATUS['stranger']],
        ],
        Product::products['kasko'] => [
            self::TYPE['sb'] => [Kasko::STATUS['policy_generated']],
            self::TYPE['stranger'] => [Kasko::STATUS['stranger']],
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_date', 'till_date', 'product', 'type'], 'required'],
            [['from_date', 'till_date'], 'date', 'format' => 'php:Y-m-d'],
            [['product'], 'in', 'range' => [Product::products['osago'], Product::products['kasko']]],
            [['type'], 'in', 'range' => self::TYPE],
        ];
    }

    public static function getFUserSelect()
    {
        return [
            'id',
            "first_name",
            "last_name",
            "phone",
            "gender",
            "comment",
            'products.products_string as products'
        ];
    }

    public static function getPayedAndStrangerWhere()
    {
        return array_merge(Product::getPayedWhere(), [
           [
               'and',
               ['in', 'products.status', [Osago::STATUS['stranger']]],
               ['product' => Product::products['osago']]
           ],
            [
                'and',
                ['in', 'products.status', [Kasko::STATUS['stranger']]],
                ['product' => Product::products['kasko']]
            ]
        ]);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->setAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return Yii::$app->controller->sendFailedResponse($this->errors, 422);
        }

        $time_period_where = [
            'and',
            ['>=', 'end_date', $this->from_date],
            ['<=', 'end_date', $this->till_date]
        ];

        $table_name = Product::models[$this->product]::tableName();
        $statuses = self::statuses[$this->product][$this->type];

        $query = \common\models\User::find()
            ->select(array_merge(self::getFUserSelect()))
            ->rightJoin([
                $table_name => Product::models[$this->product]::find()
                    ->select(["f_user_id"])
                    ->rightJoin([
                        "max_end_date_table" => Product::models[$this->product]::find()->select([
                            "max(end_date) as max_end_date",
                            'autonumber',
                        ])
                            ->where(['status' => $statuses])
                            ->groupBy('autonumber')
                    ],
                        '"max_end_date_table"."autonumber" = "' . $table_name . '"."autonumber" and "max_end_date_table"."max_end_date" = "' . $table_name . '"."end_date"')
                    ->where($time_period_where)
                    ->andWhere([$table_name . '.status' => $statuses])
                    ->groupBy($table_name . ".f_user_id")
            ], $table_name . '.f_user_id = f_user.id')
            ->leftJoin([
                'products' => Product::getProductsQuery()
                    ->select(['products.f_user_id', new Expression("string_agg(distinct product::text, ', ') as products_string")])
                    ->where(self::getPayedAndStrangerWhere())
                    ->groupBy('products.f_user_id')
            ], 'products.f_user_id = f_user.id');

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ];
        if (is_null(\Yii::$app->request->get('page')))
            $providerConfig['pagination'] = false;

        $dataProvider = new ActiveDataProvider($providerConfig);

        return $dataProvider;
    }
}
