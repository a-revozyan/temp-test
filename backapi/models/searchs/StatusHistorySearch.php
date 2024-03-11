<?php

namespace backapi\models\searchs;

use common\models\StatusHistory;
use yii\data\ActiveDataProvider;

class StatusHistorySearch extends StatusHistory
{
    public $model_class;
    public $model_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_class', 'model_id'], 'required'],
            [['model_class'], 'string'],
            [['model_class'], 'in', 'range' => ['Osago', 'KaskoBySubscription', 'Kasko', 'Travel', 'CarInspection']],
            [['model_id'], 'integer'],
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
        $query = StatusHistory::find();

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 20,
            ],
            'sort' => [
                'attributes' => [
                    'id'
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
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['model_class' => "common\\models\\" . $this->model_class]);
        $query->andFilterWhere(['model_id' => $this->model_id]);

        return $dataProvider;
    }
}
