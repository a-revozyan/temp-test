<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AccidentPartnerProgram;

/**
 * AccidentPartnerProgramSearch represents the model behind the search form of `common\models\AccidentPartnerProgram`.
 */
class AccidentPartnerProgramSearch extends AccidentPartnerProgram
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'partner_id'], 'integer'],
            [['insurance_amount_from', 'insurance_amount_to', 'percent'], 'number'],
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
        $query = AccidentPartnerProgram::find();

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
            'partner_id' => $this->partner_id,
            'insurance_amount_from' => $this->insurance_amount_from,
            'insurance_amount_to' => $this->insurance_amount_to,
            'percent' => $this->percent,
        ]);

        return $dataProvider;
    }
}
