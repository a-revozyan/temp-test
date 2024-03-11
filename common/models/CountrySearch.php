<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Country;

/**
 * CountrySearch represents the model behind the search form of `common\models\Country`.
 */
class CountrySearch extends Country
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id'], 'integer'],
            [['name_ru', 'name_uz', 'name_en', 'code', 'image'], 'safe'],
            [['schengen'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = Country::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'schengen' => $this->schengen,
            'parent_id' => $this->parent_id,
        ]);

        $query->andFilterWhere(['ilike', 'name_ru', $this->name_ru])
            ->andFilterWhere(['ilike', 'name_uz', $this->name_uz])
            ->andFilterWhere(['ilike', 'name_en', $this->name_en])
            ->andFilterWhere(['ilike', 'code', $this->code])
            ->andFilterWhere(['ilike', 'image', $this->image]);

        return $dataProvider;
    }
}
