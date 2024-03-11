<?php

namespace backapi\models\searchs;

use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use yii\data\ActiveDataProvider;

class KaskoBySubscriptionSearch extends KaskoBySubscription
{
    public $status;
    public $search;
    public $autonumber;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'each', 'rule' => ['integer']],
            [['search', 'autonumber'], 'string'],
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
        $query = KaskoBySubscription::find()
            ->with('promo')
            ->select([
                "kasko_by_subscription.*",
                "COALESCE(kasko_by_subscription_policy_count.count, 0) as policies_count",
                "COALESCE(kasko_by_subscription_policy_count.max_policy_id, 0) as max_policy_id"
            ])
            ->leftJoin([
                "kasko_by_subscription_policy_count" => KaskoBySubscriptionPolicy::find()->select([
                    "count(id) as count",
                    'kasko_by_subscription_id',
                    'max(kasko_by_subscription_policy.id) as max_policy_id',
                ])
                    ->where(['status' => KaskoBySubscriptionPolicy::STATUS['received_policy']])
                    ->groupBy('kasko_by_subscription_id')
            ],
                '"kasko_by_subscription_policy_count"."kasko_by_subscription_id" = "kasko_by_subscription"."id"')
            ->joinWith(['fUser'])->with(['lastKaskoBySubscriptionPolicy', 'savedCard']);

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'count', 'applicant_name', 'fUser.phone', 'autonumber', 'tech_pass_series', 'tech_pass_number', 'max_policy_id'
                ],
                'defaultOrder' => ['max_policy_id' => SORT_DESC]
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
            'kasko_by_subscription.status' => $this->status,
        ]);

        $filter_arr = [
            'or',
            ['ilike', 'f_user.phone', $this->search],
            ['ilike', 'applicant_name', $this->search],
        ];
        if (is_numeric($this->search) and (int)$this->search < 2147483647)
            $filter_arr = array_merge($filter_arr, [['=', 'kasko_by_subscription.id', (int)$this->search]]);
        
        $query->andFilterWhere($filter_arr);

        $query->andFilterWhere(['ilike', 'autonumber', $this->autonumber]);

        return $dataProvider;
    }
}
