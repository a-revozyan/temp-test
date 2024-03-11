<?php
namespace common\jobs;

use common\models\Accident;
use common\services\TelegramService;
use Yii;
use yii\queue\RetryableJobInterface;

class BridgeCompanyAccidentRequestJob extends \yii\base\BaseObject implements RetryableJobInterface
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
            TelegramService::send($accident);
        }

        if ($attempt == $this->attempt_times)
        {
            $accident->status = Accident::STATUS['canceled'];
            $accident->save();
            Yii::$app->queue1->push(new NotifyBridgeCompanyJob(['accident_id' => $accident->id]));
            TelegramService::send($accident, true);
        }

        return  $attempt < $this->attempt_times and empty($accident->policy_pdf_url);
    }
}