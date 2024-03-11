<?php

namespace console\controllers;

use common\helpers\GeneralHelper;
use common\jobs\SendMessageJob;
use common\jobs\SendWarningSmsJob;
use common\models\Kasko;
use common\models\OldOsago;
use common\models\Osago;
use common\models\UniqueCode;
use Yii;
use yii\base\Controller;
use yii\db\Expression;

class SendPolicyExpireWarningMessage3Controller extends Controller
{
    public function actionRun()
    {
        $osagos_query = Osago::find()
            ->select(["osago.id as id", "f_user.phone as phone", "osago.autonumber", "osago.end_date", "osago.autonumber", "osago.insurer_tech_pass_series", "osago.insurer_tech_pass_number"])
            ->rightJoin([
                "max_end_date_table" => Osago::find()->select([
                    "max(end_date) as max_end_date",
                    'autonumber',
                ])
                    ->where(['status' => Osago::STATUS['received_policy']])
                    ->groupBy('autonumber')
            ],
                '"max_end_date_table"."autonumber" = "osago"."autonumber" and "max_end_date_table"."max_end_date" = "osago"."end_date"')
            ->leftJoin('f_user', 'f_user.id = osago.f_user_id');

        $day3_osagos = (clone $osagos_query)->where(['end_date' => date('Y-m-d', strtotime("+1 days"))])->asArray()->all();

        foreach ($day3_osagos as $osago) {
            ['url' => $short_url, 'code' => $code] = $this->getShortLink($osago['id']);
            Yii::$app->queue1->push(new SendWarningSmsJob([
                'day' => 3,
                'code' => $code,
                'phone' => $osago['phone'],
                'message' => "Sug'urta Bozor. Polis OSAGO dlya ". $osago['autonumber'] ." istekaet cherez 3 dnya. Kupit' polis: $short_url",
            ]));
        }

        $this->sendOldOsago();
//        $this->sendStrangerOsagoPolicy();
        $this->sendStrangerKaskoPolicy();
    }

    public function getShortLink($osago_id)
    {
        $code = GeneralHelper::generateRandomString([UniqueCode::className(), 'code'], 10);
        $unique_code = new UniqueCode();
        $unique_code->code = $code;
        $unique_code->clonable_id = $osago_id;
        $unique_code->discount_percent = -5;
        $unique_code->save();

        return [
            'url' => "https://sugurtabozor.uz/osago/quick-buy?unique_code=$code",
            'code' => $code,
        ];
    }

    public function sendOldOsago()
    {
        $osagos_query = OldOsago::find()->andWhere(['status' => 1]);

        $day3_osagos = (clone $osagos_query)->andWhere([
            'or',
            [
                'and',
                ['=', new Expression('created_at::timestamp::date'), date('Y-m-d', strtotime("-6 months, +3 days"))],
                ['in', 'amount_uzs', [28000, 84000, 117600]]
            ],
            [
                'and',
                ['=', new Expression('created_at::timestamp::date'), date('Y-m-d', strtotime("-12 months, +3 days"))],
                ['in', 'amount_uzs', [40000, 120000, 168000]]
            ]
        ])->asArray()->all();

        $short_url = "https://sugurtabozor.uz/osago";

        foreach ($day3_osagos as $osago) {
            Yii::$app->queue1->push(new SendMessageJob([
                'phone' => $osago['insurer_phone_number'],
                'message' => "Sug'urta Bozor. Polis OSAGO ". $osago['policy_number'] ." istekaet cherez 3 dnya. Kupit' polis: $short_url",
            ]));
        }

    }

    public function sendStrangerPolicy()
    {
        $short_url = "https://sugurtabozor.uz/osago";

        $stranger_osagos = Osago::find()->select(['autonumber', 'f_user.phone as phone_number'])
            ->leftJoin('f_user', 'f_user.id=osago.f_user_id')
            ->where(['osago.status' => Osago::STATUS['stranger']]);
        $day3_osagos = (clone $stranger_osagos)->andWhere(['end_date' => date('Y-m-d', strtotime("+3 days"))])->asArray()->all();

        foreach ($day3_osagos as $osago) {
            Yii::$app->queue1->push(new SendMessageJob([
                'phone' => $osago['phone_number'],
                'message' => "Sug'urta Bozor. Polis OSAGO na mashinu " . $osago['autonumber'] . " istekaet cherez 3 dnya. Kupit' polis:  $short_url",
            ]));
        }
    }

    public function sendStrangerKaskoPolicy()
    {
        $short_url = "https://sugurtabozor.uz/kasko";

        $stranger_kaskos = Kasko::find()->select(['autonumber', 'f_user.phone as phone_number'])
            ->leftJoin('f_user', 'f_user.id=kasko.f_user_id')
            ->where(['kasko.status' => Kasko::STATUS['stranger']]);
        $day3_kaskos = (clone $stranger_kaskos)->andWhere(['end_date' => date('Y-m-d', strtotime("+3 days"))])->asArray()->all();

        foreach ($day3_kaskos as $kasko) {
            Yii::$app->queue1->push(new SendMessageJob([
                'phone' => $kasko['phone_number'],
                'message' => "Sug'urta Bozor. Polis KASKO na mashinu " . $kasko['autonumber'] . " istekaet cherez 3 dnya. Kupit' polis:  $short_url",
            ]));
        }
    }
}