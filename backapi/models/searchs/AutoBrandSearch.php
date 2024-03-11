<?php

namespace backapi\models\searchs;

use common\models\Autobrand;
use common\models\Autocomp;
use yii\data\ActiveDataProvider;

class AutoBrandSearch extends Autobrand
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
            [['status'], 'in', 'range' => Autobrand::status],
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
        $query = Autobrand::find();

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'name', 'order', 'status'
                ],
                'defaultOrder' => ['order' => SORT_ASC]
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
        $query->andFilterWhere(['status' => $this->status]);

        return $dataProvider;
    }
}
