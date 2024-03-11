<?php

namespace console\controllers;

use common\helpers\GeneralHelper;
use common\models\Osago;
use common\models\Token;
use common\models\User;
use common\services\SMSService;
use Yii;
use yii\base\Controller;
use yii\helpers\VarDumper;

class SendPromoBySmsAfterLoginController extends Controller
{
    public function actionRun()
    {
        $minutes_after_login = GeneralHelper::env('minutes_after_login_send_promo_sms');
        $promocode = GeneralHelper::env('sending_promocode_after_login_send');
        $promo_percent = GeneralHelper::env('sending_promo_percent_after_login_send');

        $users = Token::find()
            ->select(['f_user.phone as phone'])
            ->where(['<=', 'verified_at', date('Y-m-d H:i:s', strtotime("-$minutes_after_login minutes"))])
            ->andWhere(['not', ['verified_at' => null]])
            ->andWhere(['sent_sms_promo_at' => null])
            ->andWhere([
                'or',
                ['osagos.payed_osagos_count' => null],
                ['osagos.payed_osagos_count' => 0]
            ])
            ->groupBy('f_user.phone')
            ->leftJoin('f_user', 'f_user.id=token.f_user_id')
            ->leftJoin([
                'osagos' => Osago::find()
                    ->select(['count(osago.id) as payed_osagos_count', 'f_user_id'])
                    ->where(['>=', 'created_at', strtotime("-$minutes_after_login minutes")])
                    ->andWhere(['in', 'status', [Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']]])
                    ->groupBy('osago.f_user_id')
            ], 'token.f_user_id=osagos.f_user_id')
            ->asArray()->all();

        foreach ($users as $user) {
            $user_id = User::find()->where(['phone' => $user['phone']])->one()->id;
            $last_osago = Osago::find()->where(['f_user_id' => $user_id])->orderBy('id desc')->one();
            $short_url = "https://sugurtabozor.uz/osago";
            if (!empty($last_osago) and in_array($last_osago->status, [Osago::STATUS['step1'], Osago::STATUS['step2'], Osago::STATUS['step3'], Osago::STATUS['step4']]))
            {
                $status = $last_osago->status;
                if ($status == Osago::STATUS['step4'])
                    $status = Osago::STATUS['step3'];

                $short_url .= "/" . $last_osago->uuid . "/" . $status . "-step";
            }

            Token::updateAll(['sent_sms_promo_at' => date('Y-m-d H:i:s')], ['f_user_id' => $user_id]);
            SMSService::sendMessage($user['phone'], "Skidka $promo_percent ot Sug'urta Bozor na E-OSAGO na sayte $short_url Vvedite promokod \"$promocode\". Deystvuyet 30 minut!");
        }
    }
}