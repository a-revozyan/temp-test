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

class OsagoRequestJob extends \yii\base\BaseObject implements RetryableJobInterface
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
            Yii::$app->queue1->push(new SendMessageAllJob([
                'phone' => $osago->user->phone,
                'message' => Yii::t('app', "Sug'urta Bozor. POLIS OSAGO V OCHEREDI. Pri uspeshnom scenarii budet otpravlen v techenie 2 chasov."),
                'telegram_chat_ids' => $osago->user->telegram_chat_ids()
            ]));
            TelegramService::send($osago);
        }

        if ($attempt == $this->attempt_times)
        {
            if (!Osago::find()->where(['id' => $osago->id])->andWhere(['in', 'status', [Osago::STATUS['canceled']]])->exists())
            {
                $osago->status = Osago::STATUS['canceled'];
                $osago->save();
                Yii::$app->queue1->push(new PaymentCancelJob(['model_class' => Osago::className(), 'model_id' => $osago->id]));
            }

            Yii::$app->queue1->push(new SendMessageJob([
                'phone' => $osago->user->phone,
                'message' => "Sug'urta Bozor. Polis OSAGO " . $osago->autonumber . " NE OFORMLEN iz-za oshibki postavshika. OPLATA OTMENENA"
            ]));
            TelegramService::send($osago, true);
        }

        return  $attempt < $this->attempt_times and empty($osago->policy_pdf_url);
    }
}