<?php

namespace backapi\models\searchs;

use common\models\CarInspection;
use common\models\Partner;
use common\models\PartnerAccount;
use common\models\User;
use yii\base\Model;
use yii\db\Expression;

class CarInspectionPartnerStatisticsSearch extends Model
{
    public $begin_date;
    public $end_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['begin_date'], 'required'],
            [['begin_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params = [])
    {
        $this->setAttributes($params);
        if (!$this->validate()) {
            return [];
        }
        if (empty($this->end_date))
            $this->end_date = date('Y-m-d');

        if (!empty($this->begin_date))
            $this->begin_date = $this->begin_date . " " . "00:00:00";
        if (!empty($this->end_date))
            $this->end_date = $this->end_date . " " . "23:59:59";

        $available_car_inspection_count = Partner::find()
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
                "car_inspections" => CarInspection::find()->select([
                    "sum(service_amount) as used_amount",
                    'partner_id'
                ])
                    ->groupBy('partner_id')
            ],
                '"car_inspections"."partner_id" = "partner"."id"')
            ->andWhere(['f_user.role' => User::ROLES['partner']])
            ->sum('((total_amount - coalesce(used_amount, 0))/partner.service_amount)');

        $done_car_inspection_count = CarInspection::find()
            ->leftJoin('partner', 'car_inspection.partner_id = partner.id')
            ->leftJoin('f_user', 'partner.f_user_id = f_user.id')
            ->andWhere(['f_user.role' => User::ROLES['partner']])
            ->andWhere(['in', 'car_inspection.status', [CarInspection::STATUS['created']]])
            ->andFilterWhere(['>', 'car_inspection.created_at', $this->begin_date])
            ->andFilterWhere(['<', 'car_inspection.created_at', $this->end_date])
            ->count();

        return [
            'available_car_inspection_count' => $available_car_inspection_count,
            'done_car_inspection_count' => $done_car_inspection_count,
        ];
    }
}
