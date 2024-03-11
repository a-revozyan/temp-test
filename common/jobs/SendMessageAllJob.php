<?php
namespace common\jobs;

use common\services\SMSService;
use yii\queue\RetryableJobInterface;
use yii\web\BadRequestHttpException;

class SendMessageAllJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $phone;
    public $telegram_chat_ids;
    public $message;
    protected $attempt_times = 5;

    /**
     * @throws BadRequestHttpException
     */
    public function execute($queue)
    {
        SMSService::sendMessageAll($this->phone, $this->message, $this->telegram_chat_ids);
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