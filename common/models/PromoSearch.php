<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Promo;

/**
 * PromoSearch represents the model behind the search form of `common\models\Promo`.
 */
class PromoSearch extends Promo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['begin_date', 'end_date', 'begin_date', 'end_date'], 'safe'],
            [['amount_type', 'status'], 'integer'],
            [['amount_type', 'status'], 'in', 'range' => [0,1]],
            [['amount'], 'number'],
            [['code'], 'string', 'max' => 255],
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
        $query = Promo::find();

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
            'amount_type' => $this->amount_type,
            'amount' => $this->amount,
            'begin_date' => $this->begin_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'code', $this->code]);

        return $dataProvider;
    }
}
