<?php

namespace backapi\models\searchs;

use common\models\Autobrand;
use common\models\Autocomp;
use common\models\CarAccessory;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class CarAccessorySearch extends CarAccessory
{
    public $name;
    public $search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
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
        $query = CarAccessory::find();

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'name_uz', 'name_ru','name_en', 'description_ru', 'description_uz', 'description_en',
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
            ['ilike', 'name_ru', $this->name],
            ['ilike', 'name_uz', $this->name],
            ['ilike', 'name_en', $this->name],
        ]);
        $query->andFilterWhere([
            'or',
            ['ilike', 'name_ru', $this->search],
            ['ilike', 'name_uz', $this->search],
            ['ilike', 'name_en', $this->search],
            ['ilike', 'description_ru', $this->search],
            ['ilike', 'description_uz', $this->search],
            ['ilike', 'description_en', $this->search],
            ['ilike', new Expression( 'id::text'), $this->search],
        ]);

        return $dataProvider;
    }
}
