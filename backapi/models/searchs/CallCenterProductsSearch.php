<?php

namespace backapi\models\searchs;

use common\models\Product;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;

class CallCenterProductsSearch extends Model
{
    public $search;
    public $region;
    public $payment_type;
    public $product_id;
    public $product;
    public $partner_id;
    public $status;
    public $agent_id;
    public $f_user_id;
    public $policy_generated_date;
    public $reason_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search', 'region', 'payment_type'], 'string'],
            [['product_id', 'partner_id', 'status', 'agent_id', 'f_user_id', 'product', 'reason_id'], 'integer'],
            [['policy_generated_date'], 'each', 'rule' => ['integer']],
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
        $products = Product::getProductsQuery();

        $providerConfig = [
            'query' => $products,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'policy_generated_date'
                ],
                'defaultOrder' => ['policy_generated_date' => SORT_DESC]
            ]
        ];
        if (is_null(\Yii::$app->request->get('page')))
            $providerConfig['pagination'] = false;

        $provider = new ActiveDataProvider($providerConfig);

        $this->setAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $products->where('0=1');
            return $provider;
        }

        $filter_arr = [
            'or',
            ['ilike', 'f_user_phone', $this->search],
            ['ilike', 'autonumber', $this->search],
        ];

        $products->andFilterWhere($filter_arr);
        $products->andFilterWhere(['and',
            [
                'region' => $this->region,
                'payment_type' => $this->payment_type,
                'product_id' => $this->product_id,
                'partner_id' => $this->partner_id,
                'status' => $this->status,
                'agent_id' => $this->agent_id,
                'f_user_id' => $this->f_user_id,
                'product' => $this->product,
                'reason_id' => $this->reason_id,
            ],
            ['>=', 'policy_generated_date', $this->policy_generated_date['gte'] ?? null],
            ['<=', 'policy_generated_date', $this->policy_generated_date['lte'] ?? null]
        ]);

        return $provider;
    }
}
