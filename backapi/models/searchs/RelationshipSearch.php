<?php

namespace backapi\models\searchs;

use common\helpers\GeneralHelper;
use common\models\Relationship;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;

class RelationshipSearch extends Relationship
{
    public $search;
    public $for_select;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search'], 'string'],
            [['for_select'], 'boolean'],
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

        $query = Relationship::find();
        if ($this->for_select)
            $query->select(["id", "name_ru as name"]);

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'name_ru', 'name_en', 'name_uz'
                ],
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ];

        if (is_null(\Yii::$app->request->get('page')))
            $providerConfig['pagination'] = false;

        $dataProvider = new ActiveDataProvider($providerConfig);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
//             $query->where('0=1');
             return $dataProvider;
        }

        $query->andFilterWhere([
            'or',
            ['ilike', 'name_ru', $this->search],
            ['ilike', 'name_uz', $this->search],
            ['ilike', 'name_en', $this->search],
        ]);

        return $dataProvider;
    }
}
