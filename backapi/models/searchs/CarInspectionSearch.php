<?php

namespace backapi\models\searchs;

use common\models\Autobrand;
use common\models\CarInspection;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;

class CarInspectionSearch extends CarInspection
{
    public $search;
    public $status;
    public $partner_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search'], 'string'],
            [['status'], 'each', 'rule' => ['integer']],
            [['partner_id'], 'integer']
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
        $query = CarInspection::find()->joinWith(['autoModel.autoBrand', 'client']);

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'created_at', 'client.phone', 'autonumber', 'status'
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
            ['ilike', 'autonumber', $this->search],
            ['ilike', 'client.phone', $this->search],
        ];
        if (is_numeric($this->search) and (int)$this->search < 2147483647)
            $filter_arr = array_merge($filter_arr, [['=', 'car_inspection.id', (int)$this->search]]);
        $query->andFilterWhere($filter_arr);

        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['partner_id' => $this->partner_id]);

        return $dataProvider;
    }
}
