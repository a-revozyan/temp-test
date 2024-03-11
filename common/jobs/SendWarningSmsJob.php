<?php
namespace common\jobs;

use common\helpers\GeneralHelper;
use common\models\Osago;
use common\models\UniqueCode;
use common\services\SMSService;
use yii\queue\RetryableJobInterface;
use yii\web\BadRequestHttpException;

class SendWarningSmsJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $day;
    public $code;
    public $phone;
    public $message;
    protected $attempt_times = 5;

    /**
     * @throws BadRequestHttpException
     */
    public function execute($queue)
    {
        $unique_code = UniqueCode::findOne(['code' => $this->code]);
        $osago = Osago::findOne($unique_code->clonable_id);

        $transaction = \Yii::$app->db->beginTransaction();
        $osago->getAutoAndOwnerInfo(true);
        $transaction->rollBack();

        SMSService::sendMessage($this->phone, $this->message);
    }

    public function getTtr()
    {
        return 120;
    }

    public function canRetry($attempt, $error)
    {
        if ($attempt == $this->attempt_times)
        {
            $unique_code = UniqueCode::findOne(['code' => $this->code]);
            $osago = Osago::findOne($unique_code->clonable_id);

            switch ($this->day)
            {
                case 3 :
                    $message = "Sug'urta Bozor. Polis OSAGO dlya ". $osago->autonumber ." istekaet cherez 3 dnya. Kupit' polis: " . GeneralHelper::env('front_website_url') . "/osago";
                    break;
                case 2 :
                    $message = "Sug'urta Bozor. Polis OSAGO dlya ". $osago->autonumber ." istekaet cherez 2 dnya. Kupit' polis: " . GeneralHelper::env('front_website_url') . "/osago";
                    break;
                case 1 :
                    $message = "Sug'urta Bozor. Vajno! Polis OSAGO dlya ". $osago['autonumber'] ." istekaet. Obnovit' polis: " . GeneralHelper::env('front_website_url') . "/osago";
                    break;
            }

            SMSService::sendMessage($this->phone, $message);
        }
        return  $attempt < $this->attempt_times;
    }
}