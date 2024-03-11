<?php

namespace backapi\models\searchs;

use common\models\KaskoRisk;
use yii\data\ActiveDataProvider;

class KaskoRiskSearch extends KaskoRisk
{
    public $category_id;
    public $search;
    public $name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id'], 'integer'],
            [['search', 'name'], 'string'],
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
        $query = KaskoRisk::find()->joinWith('category');

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'name_ru', 'name_uz', 'name_en', 'amount'
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
        if (!is_null($this->category_id))
            $query->andFilterWhere([
                'category_id' => $this->category_id,
            ]);

        if (!is_null($this->search))
            $query->andFilterWhere([
                'or',
                ['kasko_risk.id' => (int)$this->search],
                ['ilike', 'name_uz', $this->search],
                ['ilike', 'name_ru', $this->search],
                ['ilike', 'name_en', $this->search],
                ['ilike', 'description_en', $this->search],
                ['ilike', 'description_uz', $this->search],
                ['ilike', 'description_ru', $this->search],
                ['ilike', 'kasko_risk_category.name', $this->search],
                ['amount' => (float)$this->search],
            ]);

        if (!is_null($this->name))
            $query->andFilterWhere([
                'or',
                ['ilike', 'name_uz', $this->name],
                ['ilike', 'name_ru', $this->name],
                ['ilike', 'name_en', $this->name],
            ]);

        return $dataProvider;
    }
}
