<?php
namespace common\jobs;

use common\models\Accident;
use common\models\KapitalSugurtaRequest;
use common\services\SMSService;
use common\services\TelegramService;
use Yii;
use yii\queue\RetryableJobInterface;

class CheckStatusKapitalAccidentJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $accident_id;
    protected $attempt_times = 60;

    public function execute($queue)
    {
        $accident = Accident::findOne($this->accident_id);

        $response_arr = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['payment_check'], $accident, ['anketa_id' => $accident->order_id_in_gross], true, [], 'post');

        if ($response_arr['STATUS_PAYMENT'] == 2)
        {
            $accident->policy_number = $response_arr['POLICY_SERY'] . ' ' . $response_arr['POLICY_NUMBER'];
            $accident->policy_pdf_url = $response_arr['URL'];
            $accident->status = Accident::STATUS['received_policy'];
            $accident->payed_date = date('Y-m-d H:i:s');
            TelegramService::send($accident);
            SMSService::sendMessageAll($accident->fUser->phone, Yii::t('app', "Sug'urta Bozor ACCIDENT polis: ") .  $accident->policy_pdf_url, $accident->fUser->telegram_chat_ids());
        }
        if ($response_arr['STATUS_PAYMENT'] == 3)
            $accident->status = Accident::STATUS['canceled'];

        $accident->save();
    }

    public function getTtr()
    {
        return 120;
    }

    public function canRetry($attempt, $error)
    {
        return  $attempt < $this->attempt_times;
    }
}