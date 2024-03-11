<?php

namespace backapi\models\searchs;

use common\models\FUserStoryView;
use common\models\Story;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class StorySearch extends Story
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['period_status', 'status', 'type', 'view_condition'], 'integer'],
            [['name'], 'string', 'max' => 1000],
            [['status'], 'in', 'range' => Story::STATUS],
            [['type'], 'in', 'range' => Story::TYPE],
            [['view_condition'], 'in', 'range' => Story::VIEW_CONDITION],
            [['period_status'], 'in', 'range' => Story::PERIOD_STATUS],
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
        $today = date('Y-m-d');
        $query = Story::find()->select(["*", new Expression("COALESCE(views.count::integer, 0) as views_count"), new Expression("CASE WHEN '$today' >= begin_period and '$today' <= end_period THEN 1 ELSE 0 END as period_status")])
            ->leftJoin(
                ['views' =>
                    FUserStoryView::find()
                        ->select(["count('*') as count", "story_id"])
                        ->groupBy("story_id")
                ],
                "story.id = views.story_id"
            )->with('files');

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'name', 'views_count', 'period_status', 'status', 'priority', 'type'
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
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere([
            'or',
            ['ilike', 'name', $this->name],
        ]);
        if ($this->period_status == Story::PERIOD_STATUS['active'])
            $query->andWhere(['<=', 'begin_period', $today])->andWhere(['>=', 'end_period', $today]);
        else
            $query->andWhere([
                'or',
                ['>=', 'begin_period', $today],
                ['<=', 'end_period', $today]
            ]);

        $query->andFilterWhere(['type' => $this->type]);
        $query->andFilterWhere(['view_condition' => $this->view_condition]);

        return $dataProvider;
    }
}
