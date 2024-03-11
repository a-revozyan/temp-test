<?php

namespace backapi\models\searchs;

use common\models\Autobrand;
use common\models\Autocomp;
use common\models\Automodel;
use common\models\Warehouse;
use yii\data\ActiveDataProvider;

class WarehouseSearch extends Autobrand
{
    public $search;
    public $partner_id;
    public $status;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'status'], 'integer'],
            [['search'], 'string'],
            [['status'], 'in', 'range' => Warehouse::STATUS],
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
        $query = Warehouse::find();

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'partner_id', 'status'
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
        $query->andFilterWhere(['partner_id' => $this->partner_id]);
        $query->andFilterWhere(['ilike', 'concat(series, number)', $this->search]);
        $query->andFilterWhere(['status' => $this->status]);

        return $dataProvider;
    }
}
