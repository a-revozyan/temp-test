<?php

namespace backapi\models\searchs;

use common\models\Autobrand;
use common\models\Autocomp;
use common\models\Qa;
use yii\data\ActiveDataProvider;

class QaSearch extends Qa
{
    public $search;
    public $status;
    public $page;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search'], 'string'],
            [['status', 'page'], 'integer'],
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
        $query = Qa::find();

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'question_uz', 'question_ru', 'answer_uz', 'answer_ru', 'page'
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
            ['ilike', 'question_uz', $this->search],
            ['ilike', 'question_ru', $this->search],
            ['ilike', 'question_en', $this->search],
            ['ilike', 'answer_uz', $this->search],
            ['ilike', 'answer_ru', $this->search],
            ['ilike', 'answer_en', $this->search],
        ]);

        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['page' => $this->page]);

        return $dataProvider;
    }
}
