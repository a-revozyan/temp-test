<?php
namespace common\jobs;

use common\models\Accident;
use common\services\SMSService;
use common\services\TelegramService;
use Yii;
use yii\queue\RetryableJobInterface;

class AccidentRequestJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $accident_id;
    protected $attempt_times = 60;

    public function execute($queue)
    {
        $accident = Accident::findOne($this->accident_id);
        $accident->get_policy_from_partner($accident->osago, true);
    }

    public function getTtr()
    {
        return 120;
    }

    public function canRetry($attempt, $error)
    {
        $accident = Accident::findOne($this->accident_id);

        if ($attempt == 1)
        {
            $accident->status = Accident::STATUS['waiting_for_policy'];
            $accident->save();
            SMSService::sendMessageAll($accident->fUser->phone, Yii::t("app", "Sug'urta Bozor. POLIS NECHAST V OCHEREDI. Pri uspeshnom scenarii budet otpravlen v techenie 2 chasov."), $accident->fUser->telegram_chat_ids());
            TelegramService::send($accident);
        }

        if ($attempt == $this->attempt_times)
        {
            $accident->status = Accident::STATUS['canceled'];
            $accident->save();
            TelegramService::send($accident, true);
        }

        return  $attempt < $this->attempt_times and empty($accident->policy_pdf_url);
    }
}