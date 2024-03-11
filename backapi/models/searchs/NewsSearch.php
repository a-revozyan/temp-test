<?php

namespace backapi\models\searchs;

use common\models\News;
use yii\data\ActiveDataProvider;

class NewsSearch extends News
{
    public $search;
    public $status;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search'], 'string'],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => News::STATUS],
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
        $query = News::find();

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'created_at', 'updated_at', 'status'
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
            ['ilike', 'title_uz', $this->search],
            ['ilike', 'title_ru', $this->search],
            ['ilike', 'title_en', $this->search],
            ['ilike', 'short_info_uz', $this->search],
            ['ilike', 'short_info_ru', $this->search],
            ['ilike', 'short_info_en', $this->search],
            ['ilike', 'body_uz', $this->search],
            ['ilike', 'body_ru', $this->search],
            ['ilike', 'body_en', $this->search],
        ]);
        $query->andFilterWhere(['status' => $this->status]);

        return $dataProvider;
    }
}
