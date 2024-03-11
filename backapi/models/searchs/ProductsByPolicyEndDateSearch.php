<?php

namespace backapi\models\searchs;

use common\models\Kasko;
use common\models\Osago;
use common\models\Product;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductsByPolicyEndDateSearch extends Model
{
    public $from_date;
    public $till_date;
    public $product;

    public const statuses = [
        Product::products['osago'] => [Osago::STATUS['received_policy']],
        Product::products['kasko'] => [Kasko::STATUS['policy_generated']],
    ];

    public const relations = [
        Product::products['osago'] => ['user', 'partner'],
        Product::products['kasko'] => ['fUser', 'partner'],
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_date', 'till_date', 'product'], 'required'],
            [['from_date', 'till_date'], 'date', 'format' => 'php:Y-m-d'],
            [['product'], 'in', 'range' => [Product::products['osago'], Product::products['kasko']]],
        ];
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
        $statuses = self::statuses[$this->product];
        $relations = self::relations[$this->product];

        $query = Product::models[$this->product]::find()
            ->joinWith($relations)
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
            ->andWhere([$table_name . '.status' => $statuses]);

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
