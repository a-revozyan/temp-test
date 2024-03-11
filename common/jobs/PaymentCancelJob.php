<?php
namespace common\jobs;

use common\models\Osago;
use common\services\PaymentService;
use common\services\SMSService;
use common\services\TelegramService;
use Yii;
use yii\helpers\VarDumper;
use yii\queue\RetryableJobInterface;
use yii\web\BadRequestHttpException;

class PaymentCancelJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $model_id;
    public $model_class;
    protected $attempt_times = 72;

    public function execute($queue)
    {
        PaymentService::cancel($this->model_class, $this->model_id);
    }

    public function getTtr()
    {
        return 3600;
    }

    public function canRetry($attempt, $error)
    {
        return  $attempt < $this->attempt_times;
    }
}