<?php

namespace backapi\models\searchs;

use common\models\Agent;
use common\models\AgentProductCoeff;
use common\models\Kasko;
use common\models\Osago;
use common\models\Travel;
use common\models\User;
use yii\data\SqlDataProvider;

class AgentSearch extends Agent
{
    public $product_id;
    public $status;
    public $search;
    public $agent_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'status', 'agent_id'], 'integer'],
            [['search'], 'string'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return SqlDataProvider
     */
    public function search($params)
    {
        $query = Agent::find()
            ->leftJoin('f_user', '"agent"."f_user_id" = "f_user"."id"')
            ->leftJoin(
                [
                    'agent_product_coeff' => AgentProductCoeff::find()->select(["agent_product_coeff.agent_id", "string_agg(product_id::text, ','  ORDER BY product_id) as product_ids"])
                        ->groupBy("agent_id")
                ], '"agent"."id" = "agent_product_coeff"."agent_id"')
            ->select([
                "agent.id as id",
                "f_user.first_name as first_name",
                "f_user.last_name as last_name",
                'f_user.created_at as created_at',
                "agent.inn as inn",
                "agent_product_coeff.product_ids as product_ids",
                "(coalesce(kasko_count, 0) + coalesce(travel_count,0)) as policy_count",
                "(coalesce(agent_kasko_amount, 0)+coalesce(agent_travel_amount, 0)) as policy_amount",
                "(coalesce(kasko_amount, 0)+coalesce(travel_amount, 0)) as product_policy_amount_uzs",
                "status",
                "contract_number",
                "logo",
                "phone",
            ])
            ->leftJoin(
                [
                    "kasko" => Kasko::find()->select([
                        "count(kasko.id) as kasko_count",
                        'f_user_id',
                        'sum(kasko.agent_amount) as agent_kasko_amount',
                        'sum(kasko.amount_uzs) as kasko_amount',
                    ])
                        ->andWhere('status in (' . implode(',', [Kasko::STATUS['payed'], Kasko::STATUS['attached'], Kasko::STATUS['processed'], Kasko::STATUS['policy_generated']]) . ")")
                        ->groupBy('f_user_id')
                ],
                '"kasko"."f_user_id" = "agent"."f_user_id"'
            )
            ->leftJoin(
                [
                    "travel" => Travel::find()->select([
                        "count(travel.id) as travel_count",
                        'f_user_id',
                        'sum(travel.agent_amount) as agent_travel_amount',
                        'sum(travel.amount_uzs) as travel_amount',
                    ])
                        ->andWhere('status in (' . implode(',', [Travel::STATUSES['payed'], Travel::STATUSES['waiting_for_policy'], Travel::STATUSES['received_policy']]) . ")")
                        ->groupBy('f_user_id')
                ],
                '"travel"."f_user_id" = "agent"."f_user_id"'
            )
            ->andWhere('f_user.status != :status_deleted');

        $providerConfig = [
            'sql' => $query->createCommand()->sql,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'created_at', 'inn', 'policy_count', 'policy_amount'
                ],
                'defaultOrder' => ['created_at' => SORT_DESC]
            ]
        ];
        if (is_null(\Yii::$app->request->get('page')))
            $providerConfig['pagination'] = false;

        $dataProvider = new SqlDataProvider($providerConfig);

        $this->setAttributes($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
             return $dataProvider;
        }

        $params['status_deleted'] = User::STATUS_DELETED;
        if (!is_null($this->product_id))
        {
            $params['product_id'] = "%" . $this->product_id . "%";
            $query->andWhere('product_ids ilike :product_id');
        }

        if (!is_null($this->status))
        {
            $params['status'] = $this->status;
            $query->andWhere('status=:status');
        }

        if (!is_null($this->agent_id))
        {
            $params['agent_id'] = $this->agent_id;
            $query->andWhere('agent.id = :agent_id');
        }

        if (!is_null($this->search))
        {
            $params['search'] = "%" . $this->search . "%";
            $query->andWhere([
                'or',
                'first_name ilike :search',
                'last_name ilike :search',
                'inn ilike :search',
            ]);
        }

        $dataProvider->sql = $query->createCommand()->sql;
        $dataProvider->params = $params;

        return $dataProvider;
    }
}
