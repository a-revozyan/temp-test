<?php
namespace common\jobs;

use common\models\Accident;
use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\models\PaymeSubscribeRequest;
use common\services\SMSService;
use common\services\TelegramService;
use frontend\controllers\PaymeController;
use Yii;
use yii\queue\RetryableJobInterface;
use yii\web\BadRequestHttpException;

class PayKaskoBySubscriptionJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $kbs_id;
    public $card_id;
    protected $attempt_times = 5;

    public function execute($queue)
    {
        $kbs = KaskoBySubscription::findOne($this->kbs_id);

        if ($kbs->lastKaskoBySubscriptionPolicy->end_date > date('Y-m-d 23:59:59'))
            return true;

        $kasko_by_subscription_policy = new KaskoBySubscriptionPolicy();
        $kasko_by_subscription_policy->partner_id = KaskoBySubscriptionPolicy::DEFAULT_PARTNER_ID;
        $kasko_by_subscription_policy->kasko_by_subscription_id = $kbs->id;
        $kasko_by_subscription_policy->saved_card_id = $kbs->saved_card_id;
        $kasko_by_subscription_policy->status = KaskoBySubscriptionPolicy::STATUS['created'];
        $kasko_by_subscription_policy->created_at = date('Y-m-d H:i:s');
        $kasko_by_subscription_policy->amount_uzs = $kbs->amount_uzs + $kbs->promo_amount;
        $kasko_by_subscription_policy->begin_date = date('Y-m-d 00:00:00');
        $kasko_by_subscription_policy->end_date = date('Y-m-d 23:59:59', strtotime(' + 29 days'));
        $kasko_by_subscription_policy->save();

        $trans_no = PaymeSubscribeRequest::sendRequest(
            PaymeSubscribeRequest::METHODS['receipt_create'],
            [
                'amount' => $kasko_by_subscription_policy->amount_uzs * 100,
                'account' => [
                    'order_id' => $kasko_by_subscription_policy->id,
                    'type' => PaymeController::TYPE[strtolower(explode('\\', KaskoBySubscriptionPolicy::className())[2])],
                ],
            ],
            KaskoBySubscriptionPolicy::className(),
            $kasko_by_subscription_policy->id,
        );
        $kasko_by_subscription_policy->trans_id = $trans_no;
        $kasko_by_subscription_policy->save();

        $payme_response = PaymeSubscribeRequest::sendRequest(
            PaymeSubscribeRequest::METHODS['receipt_pay'],
            [
                'id' => $trans_no,
                'token' => $this->card_id,
            ],
            KaskoBySubscriptionPolicy::className(),
            $kasko_by_subscription_policy->id,
            false
        );

        if (!is_array($payme_response))
            throw new BadRequestHttpException('Payme ga murojaatda xatolik');
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