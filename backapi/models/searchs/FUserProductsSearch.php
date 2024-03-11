<?php

namespace backapi\models\searchs;

use common\models\Autobrand;
use common\models\Autocomp;
use common\models\Kasko;
use common\models\Osago;
use common\models\Product;
use common\models\Travel;
use common\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class FUserProductsSearch extends Model
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
            [['id'], 'required'],
            [['from_date', 'till_date'], 'date', 'format' => 'php:Y-m-d'],
            [['id'], 'integer', 'max' => 2147483647],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id' => 'id'], 'filter' => function($query){
                return $query->where(['not', ['status' => User::STATUS_DELETED]])
                    ->andWhere(['role' => User::ROLES['user']]);
            }],
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
        $products = Product::getProductsQuery()
            ->andWhere(Product::getPayedWhere());

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
             $query->where('0=1');
            return $provider;
        }

        $products->andFilterWhere(['=', 'products.f_user_id', $this->id]);
        if (!empty($this->from_date))
            $products->andFilterWhere(['>', 'policy_generated_date', date_create_from_format('Y-m-d', $this->from_date)->getTimestamp()]);
        if (!empty($this->till_date))
            $products->andFilterWhere(['<', 'policy_generated_date', date_create_from_format('Y-m-d', $this->till_date)->getTimestamp()]);

        return $provider;
    }
}
