<?php

namespace backapi\models\searchs;

use common\models\Reason;
use yii\data\ActiveDataProvider;

class ReasonSearch extends Reason
{
    public $name;
    public $status;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => Reason::STATUS],
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
        $query = Reason::find();

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'name', 'status'
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
        $query->andFilterWhere([
            'or',
            ['ilike', 'name', $this->name]
        ]);

        $query->andFilterWhere(['=', 'status', $this->status]);

        return $dataProvider;
    }
}
