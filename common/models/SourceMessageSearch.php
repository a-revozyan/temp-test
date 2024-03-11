<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SourceMessage;

/**
 * CountrySearch represents the model behind the search form of `common\models\Country`.
 */
class SourceMessageSearch extends SourceMessage
{
    /**
     * {@inheritdoc}
     */
    public $ru;
    public $uz;
    public $en;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['category', 'message', 'ru', 'uz', 'en'], 'safe'],
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
        $query = SourceMessage::find();
    
        $query->joinWith(['ru', 'uz', 'en']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['ru'] = [
            'asc' => ['message.translation' => SORT_ASC],
            'desc' => ['message.translation' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['uz'] = [
            'asc' => ['message.translation' => SORT_ASC],
            'desc' => ['message.translation' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['en'] = [
            'asc' => ['message.translation' => SORT_ASC],
            'desc' => ['message.translation' => SORT_DESC],
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
        ]);

        $query->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['ilike', 'message', $this->message])
            ->andFilterWhere(['like', 'message.translation', $this->ru])
            ->andFilterWhere(['like', 'message.translation', $this->uz])
            ->andFilterWhere(['like', 'message.translation', $this->en]);

        return $dataProvider;
    }
}
