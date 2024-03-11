<?php

namespace backapi\models\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SourceMessage;
use yii\helpers\VarDumper;

/**
 * CountrySearch represents the model behind the search form of `common\models\Country`.
 */
class TranslateSearch extends SourceMessage
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
    
        $query->joinWith(['messages', 'uz', 'ru', 'en']);

        // add conditions that should always apply here

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ];

        if (is_null(\Yii::$app->request->get('page')))
            $providerConfig['pagination'] = false;

        $dataProvider = new ActiveDataProvider($providerConfig);

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

        $this->setAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'source_message.id' => $this->id,
        ]);

        $query->andFilterWhere(['ilike', 'category', $this->category])
            ->andFilterWhere(['ilike', 'message', $this->message])
            ->andFilterWhere(['ilike', 'message.translation', $this->ru])
            ->andFilterWhere(['ilike', 'message.translation', $this->uz])
            ->andFilterWhere(['ilike', 'message.translation', $this->en]);

        return $dataProvider;
    }
}
