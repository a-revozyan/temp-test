<?php
namespace common\jobs;

use common\services\SMSService;
use yii\queue\RetryableJobInterface;

class SendMessageJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $phone;
    public $message;
    protected $attempt_times = 5;

    public function execute($queue)
    {
        SMSService::sendMessage($this->phone, $this->message);
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