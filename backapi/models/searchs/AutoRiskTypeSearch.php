<?php

namespace backapi\models\searchs;

use common\models\Autocomp;
use common\models\AutoRiskType;
use common\models\Kasko;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class AutoRiskTypeSearch extends AutoRiskType
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
        $query = AutoRiskType::find();

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

        $query->andFilterWhere([
            'or',
            ['ilike', new Expression( 'id::text'), $this->search],
            ['ilike', 'name', $this->search]
        ]);

        return $dataProvider;
    }
}
