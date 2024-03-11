<?php

namespace backapi\models\searchs;

use common\models\CarInspection;
use common\models\Partner;
use common\models\PartnerAccount;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class CarInspectionPartnerSearch extends Partner
{
    public $begin_date;
    public $end_date;
    public $id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer', 'max' => 2147483647, 'min' => 1],
            [['begin_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
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
        $query = Partner::find()
            ->select([
                'partner.id',
                'name',
                'service_amount',
                new Expression('coalesce((coalesce(total_amount,0) - coalesce(used_amount, 0))/partner.service_amount, 0) as available_car_inspection_count'),
                new Expression('coalesce(done_car_inspections.done_car_inspection_count, 0) as done_car_inspection_count'),
                'partner.status as status',
                'coalesce(partner_accounts.count, 0) as partner_accounts_count',
            ])
            ->leftJoin('f_user', 'partner.f_user_id = f_user.id')
            ->leftJoin([
                "accounts" => PartnerAccount::find()->select([
                    "sum(amount) as total_amount",
                    'partner_id'
                ])
                    ->groupBy('partner_id')
            ],
                '"accounts"."partner_id" = "partner"."id"')
            ->leftJoin([
                "done_car_inspections" => CarInspection::find()->select([
                    "sum(service_amount) as used_amount",
                    "count(id) as done_car_inspection_count",
                    'partner_id'
                ])
                    ->where(['in', 'status', [CarInspection::STATUS['verified_by_client']]])
                    ->groupBy('partner_id')
            ], '"done_car_inspections"."partner_id" = "partner"."id"')
            ->leftJoin([
                "partner_accounts" => PartnerAccount::find()->select([
                    "count(id) as count",
                    'partner_id'
                ])
                    ->groupBy('partner_id')
            ], '"partner_accounts"."partner_id" = "partner"."id"')
            ->andWhere(['f_user.role' => User::ROLES['partner']]);


        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'partner.id', 'name', 'service_amount', 'available_car_inspection_count', 'done_car_inspection_count', 'status', 'partner_accounts_count'
                ],
                'defaultOrder' => ['partner.id' => SORT_DESC]
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

        if (!empty($this->begin_date))
            $this->begin_date = $this->begin_date . " " . "00:00:00";
        if (!empty($this->end_date))
            $this->end_date = $this->end_date . " " . "23:59:59";


        $query->andFilterWhere(['>', 'car_inspection.created_at', $this->begin_date]);
        $query->andFilterWhere(['<', 'car_inspection.created_at', $this->end_date]);
        $query->andFilterWhere(['partner.id' => $this->id]);

        return $dataProvider;
    }
}
