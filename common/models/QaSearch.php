<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Qa;

/**
 * QaSearch represents the model behind the search form of `common\models\Qa`.
 */
class QaSearch extends Qa
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['question_uz', 'question_en', 'question_ru', 'answer_uz', 'answer_en', 'answer_ru'], 'safe'],
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
        $query = Qa::find();

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
        ]);

        $query->andFilterWhere(['ilike', 'question_uz', $this->question_uz])
            ->andFilterWhere(['ilike', 'question_en', $this->question_en])
            ->andFilterWhere(['ilike', 'question_ru', $this->question_ru])
            ->andFilterWhere(['ilike', 'answer_uz', $this->answer_uz])
            ->andFilterWhere(['ilike', 'answer_en', $this->answer_en])
            ->andFilterWhere(['ilike', 'answer_ru', $this->answer_ru]);

        return $dataProvider;
    }
}
