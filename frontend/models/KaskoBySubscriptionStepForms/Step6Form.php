<?php

namespace frontend\models\KaskoBySubscriptionStepForms;

use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\models\PaymeSubscribeRequest;
use common\models\SavedCard;
use frontend\controllers\PaymeController;
use thamtech\uuid\validators\UuidValidator;
use Yii;

class Step6Form extends \yii\base\Model
{
    public $kasko_by_subscription_uuid;
    public $saved_card_id;
    public $verifycode;

    public function rules()
    {
        return [
            [['kasko_by_subscription_uuid', 'saved_card_id', 'verifycode'], 'required'],
            [['saved_card_id', 'verifycode'], 'integer'],
            [['kasko_by_subscription_uuid'], 'string', 'max' => 255],
            [['kasko_by_subscription_uuid'], UuidValidator::className()],
            [['kasko_by_subscription_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoBySubscription::className(), 'targetAttribute' => ['kasko_by_subscription_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['f_user_id' => Yii::$app->user->id])
                    ->andWhere(['in', 'status', [
                        KaskoBySubscription::STATUS['step5'], KaskoBySubscription::STATUS['step6']
                    ]]);
            }],
            [['saved_card_id'], 'exist', 'skipOnError' => true, 'targetClass' => SavedCard::className(), 'targetAttribute' => ['saved_card_id' => 'id']],
            [['verifycode'], 'filter', 'filter' => 'trim'],
        ];
    }

    public function save()
    {
        $order = KaskoBySubscription::findOne(['uuid' => $this->kasko_by_subscription_uuid]);

        $saved_card = SavedCard::findOne($this->saved_card_id);

        PaymeSubscribeRequest::sendRequest(
            PaymeSubscribeRequest::METHODS['check_sms_code'],
            [
                'token' => $saved_card->card_id,
                'code' => (string)$this->verifycode,
            ],
            KaskoBySubscription::className(),
            $this->kasko_by_subscription_uuid,
        );

        $order->status = KaskoBySubscription::STATUS['step6'];
        $order->saved_card_id = $this->saved_card_id;
        $order->save();

        $kasko_by_subscription_policy = new KaskoBySubscriptionPolicy();
        $kasko_by_subscription_policy->partner_id = $order->partner_id;
        $kasko_by_subscription_policy->kasko_by_subscription_id = $order->id;
        $kasko_by_subscription_policy->saved_card_id = $this->saved_card_id;
        $kasko_by_subscription_policy->status = KaskoBySubscriptionPolicy::STATUS['created'];
        $kasko_by_subscription_policy->created_at = date('Y-m-d H:i:s');
        $kasko_by_subscription_policy->amount_uzs = $order->amount_uzs;
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

        PaymeSubscribeRequest::sendRequest(
            PaymeSubscribeRequest::METHODS['receipt_pay'],
            [
                'id' => $trans_no,
                'token' => $saved_card->card_id,
            ],
            KaskoBySubscriptionPolicy::className(),
            $kasko_by_subscription_policy->id,
        );

        return $kasko_by_subscription_policy;
    }
}