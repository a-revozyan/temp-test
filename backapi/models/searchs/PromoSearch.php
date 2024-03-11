<?php

namespace backapi\models\searchs;

use common\models\Autobrand;
use common\models\Autocomp;
use common\models\Automodel;
use common\models\Promo;
use yii\data\ActiveDataProvider;

class PromoSearch extends Promo
{
    public $search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search'], 'string'],
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
        $query = Promo::find()->with('products');

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'code', 'amount', 'begin_date', 'end_date', 'amount_type', 'status', 'number'
                ],
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ];
        if (is_null(\Yii::$app->request->get('page')))
            $providerConfig['pagination'] = false;

        $dataProvider = new ActiveDataProvider($providerConfig);

        $this->setAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $filter_arr = [
            'or',
            ['ilike', 'code', $this->search],
        ];
        if (is_numeric($this->search) and (int)$this->search < 2147483647)
            $filter_arr = array_merge($filter_arr, [
                ['=', 'id', (int)$this->search],
                ['=', 'amount', abs((int)$this->search) * -1],
            ]);

        $query->andFilterWhere($filter_arr);
        $query->andWhere(['type' => [Promo::TYPE['simple'], null]]);

        return $dataProvider;
    }
}
