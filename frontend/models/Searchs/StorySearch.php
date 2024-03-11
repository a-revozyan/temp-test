<?php

namespace frontend\models\Searchs;

use common\models\FUserStoryView;
use common\models\Product;
use common\models\Story;
use common\models\User;
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
            [['type'], 'integer'],
            [['type'], 'in', 'range' => Story::TYPE],
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
        $query->andWhere(['status' => Story::STATUS['ready']]);
        $query->andWhere(['<=', 'begin_period', $today])->andWhere(['>=', 'end_period', $today]);

        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $product_count = Product::getProductsQuery()
            ->andWhere(Product::getPayedWhere())
            ->andWhere(['products.f_user_id' => $user->id])
            ->count('id');

        $view_conditions = [];

        if ($user->created_at >= strtotime("-3 days"))
            $view_conditions[] = Story::VIEW_CONDITION['new_users'];

        if ($product_count == 1)
            $view_conditions[] = Story::VIEW_CONDITION['bought_only_1_policy'];

        if ($product_count > 1)
            $view_conditions[] = Story::VIEW_CONDITION['bought_several_policy'];

        if ($user->created_at <= strtotime("-1 month") and $product_count == 0)
            $view_conditions[] = Story::VIEW_CONDITION['old_user_but_never_bought'];

        $query->andWhere([
            'or',
            ['in', 'view_condition', $view_conditions],
            ['view_condition' => null],
        ]);

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

//        $query->andFilterWhere([
//            'or',
//            ['ilike', 'name', $this->name],
//        ]);

        $query->andFilterWhere(['type' => $this->type]);

        return $dataProvider;
    }
}
