<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\News;

/**
 * NewsSearch represents the model behind the search form of `common\models\News`.
 */
class NewsSearch extends News
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['title_ru', 'title_uz', 'title_en', 'image_ru', 'image_uz', 'image_en', 'short_info_ru', 'short_info_uz', 'short_info_en', 'body_ru', 'body_uz', 'body_en'], 'safe'],
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
        $query = News::find()->with(['tags']);

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'title_ru', $this->title_ru])
            ->andFilterWhere(['ilike', 'title_uz', $this->title_uz])
            ->andFilterWhere(['ilike', 'title_en', $this->title_en])
            ->andFilterWhere(['ilike', 'image_ru', $this->image_ru])
            ->andFilterWhere(['ilike', 'image_uz', $this->image_uz])
            ->andFilterWhere(['ilike', 'image_en', $this->image_en])
            ->andFilterWhere(['ilike', 'short_info_ru', $this->short_info_ru])
            ->andFilterWhere(['ilike', 'short_info_uz', $this->short_info_uz])
            ->andFilterWhere(['ilike', 'short_info_en', $this->short_info_en])
            ->andFilterWhere(['ilike', 'body_ru', $this->body_ru])
            ->andFilterWhere(['ilike', 'body_uz', $this->body_uz])
            ->andFilterWhere(['ilike', 'body_en', $this->body_en]);

        return $dataProvider;
    }
}
