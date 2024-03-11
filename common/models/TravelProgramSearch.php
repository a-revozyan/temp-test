<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TravelProgram;

/**
 * TravelProgramSearch represents the model behind the search form of `common\models\TravelProgram`.
 */
class TravelProgramSearch extends TravelProgram
{
    public $partner;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'partner_id'], 'integer'],
            [['name', 'partner'], 'safe'],
            [['status', 'has_covid'], 'boolean'],
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
        $query = TravelProgram::find();
    
        $query->joinWith(['partner']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['partner'] = [
            'asc' => ['partner.name' => SORT_ASC],
            'desc' => ['partner.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'partner_id' => $this->partner_id,
            'status' => $this->status,
            'has_covid' => $this->has_covid, 
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['like', 'partner.name', $this->partner]);

        return $dataProvider;
    }
}
