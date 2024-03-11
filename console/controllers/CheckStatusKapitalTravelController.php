<?php

namespace console\controllers;

use common\models\KapitalSugurtaRequest;
use common\models\Osago;
use common\models\Partner;
use common\models\Travel;
use common\services\SMSService;
use common\services\TelegramService;
use Yii;
use yii\base\Controller;

class CheckStatusKapitalTravelController extends Controller
{
    public function actionRun()
    {
        /** @var Osago[] $osagos */
        $travels = Travel::find()
            ->where(['partner_id' => Partner::PARTNER['kapital']])
            ->andWhere(['status' => Travel::STATUSES['step3']])
            ->andWhere(['>=', 'created_at', strtotime("-1 days")])
            ->all();

        /** @var Travel $travel */
        foreach ($travels as $travel) {
            $response_arr = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['payment_check'], $travel, ['anketa_id' => $travel->order_id_in_gross], false, [], 'post');
            if (array_key_exists('POLICY_SERY', $response_arr) and array_key_exists('POLICY_NUMBER', $response_arr))
                $travel->policy_number = $response_arr['POLICY_SERY'] . $response_arr['POLICY_NUMBER'];
            if (array_key_exists('URL', $response_arr) and $response_arr['URL'] != "https://api.ksc.uz/p//")
                $travel->policy_pdf_url = $response_arr['URL'];
            if (array_key_exists('STATUS_POLICY', $response_arr) and $response_arr['STATUS_POLICY'] == 2)
            {
                $travel->status = Travel::STATUSES['received_policy'];
                $travel->payed_date = time();
            }
            $travel->save();

            if ($travel->status == Travel::STATUSES['received_policy'])
            {
                TelegramService::send($travel);
                SMSService::sendMessageAll($travel->user->phone, Yii::t('app', "Sug'urta Bozor TRAVEL polis: ") .  $travel->policy_pdf_url, $travel->user->telegram_chat_ids());
            }
        }
    }
}