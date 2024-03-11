<?php
namespace common\jobs;

use common\models\Osago;
use common\services\TelegramService;
use Yii;
use yii\queue\RetryableJobInterface;

class BridgeCompanyOsagoRequestJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $osago_id;
    protected $attempt_times = 60;

    public function execute($queue)
    {
        $osago = Osago::findOne($this->osago_id);
        $osago->get_policy_from_partner();
    }

    public function getTtr()
    {
        return 120;
    }

    public function canRetry($attempt, $error)
    {
        $osago = Osago::findOne($this->osago_id);

        if ($attempt == 1 and $osago->status != Osago::STATUS['received_policy'])
        {
            $osago->status = Osago::STATUS['waiting_for_policy'];
            $osago->save();
            TelegramService::send($osago);
        }

        if ($attempt == $this->attempt_times)
        {
            $osago->status = Osago::STATUS['canceled'];
            $osago->save();
            Yii::$app->queue1->push(new NotifyBridgeCompanyJob(['osago_id' => $osago->id]));
            TelegramService::send($osago, true);
        }

        return  $attempt < $this->attempt_times and empty($osago->policy_pdf_url);
    }
}