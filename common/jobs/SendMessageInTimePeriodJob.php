<?php
namespace common\jobs;

use common\helpers\GeneralHelper;
use common\services\SMSService;
use yii\queue\RetryableJobInterface;
use yii\web\BadRequestHttpException;

class SendMessageInTimePeriodJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $phone;
    public $message;
    protected $attempt_times = 288;

    public function execute($queue)
    {
        $begin_date = GeneralHelper::env('car_inspection_begin_sms_time'). ':00';
        $end_date = GeneralHelper::env('car_inspection_end_sms_time'). ':00';

        $time = date('H:i:s');
        if ($begin_date > $time or $end_date < $time)
            throw new BadRequestHttpException('Time is not in given interval');

        SMSService::sendMessage($this->phone, $this->message);
    }

    public function getTtr()
    {
        return 300;
    }

    public function canRetry($attempt, $error)
    {
        return  $attempt < $this->attempt_times;
    }
}