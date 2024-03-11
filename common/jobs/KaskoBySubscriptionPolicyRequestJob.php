<?php
namespace common\jobs;

use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\models\Osago;
use common\services\PaymentService;
use common\services\SMSService;
use common\services\TelegramService;
use Yii;
use yii\queue\RetryableJobInterface;

class KaskoBySubscriptionPolicyRequestJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $kasko_by_subscription_policy_id;
    public $message;
    protected $attempt_times = 60;

    public function execute($queue)
    {
        $kasko_by_subscription_policy = KaskoBySubscriptionPolicy::findOne($this->kasko_by_subscription_policy_id);
        $kasko_by_subscription_policy->send_save_to_partner($this->message);
    }

    public function getTtr()
    {
        return 120;
    }

    public function canRetry($attempt, $error)
    {
        $kbsp = KaskoBySubscriptionPolicy::findOne($this->kasko_by_subscription_policy_id);

        if ($attempt == 1)
        {
            $kbsp->status = KaskoBySubscriptionPolicy::STATUS['waiting_for_policy'];
            $kbsp->save();
            SMSService::sendMessageAll($kbsp->kaskoBySubscription->fUser->phone, Yii::t('app', "Sug'urta Bozor. POLIS KASKO PO PODPISKE V OCHEREDI. Pri uspeshnom scenarii budet otpravlen v techenie 2 chasov.") , $kbsp->kaskoBySubscription->fUser->telegram_chat_ids());
            TelegramService::send($kbsp);
        }

        if ($attempt == $this->attempt_times)
        {
            if (!KaskoBySubscriptionPolicy::find()->where(['id' => $kbsp->id])->andWhere(['in', 'status', [KaskoBySubscriptionPolicy::STATUS['canceled']]])->exists())
            {
                $kbsp->status = Osago::STATUS['canceled'];
                $kbsp->save();
                $kbs = KaskoBySubscription::findOne($kbsp->kasko_by_subscription_id);
                $kbs->status = KaskoBySubscription::STATUS['canceled'];
                $kbs->save();

                Yii::$app->queue1->push(new PaymentCancelJob(['model_class' => KaskoBySubscriptionPolicy::className(), 'model_id' => $kbsp->id]));
            }

            SMSService::sendMessage($kbsp->kaskoBySubscription->fUser->phone, "Sug'urta Bozor. Polis KASKO PO PODPISKE " . $kbsp->kaskoBySubscription->autonumber . " NE OFORMLEN iz-za oshibki postavshika. OPLATA OTMENENA");
            TelegramService::send($kbsp, true);
        }

        return  $attempt < $this->attempt_times and empty($kbsp->policy_pdf_url);
    }
}