<?php

namespace frontend\models\KaskoBySubscriptionStepForms;

use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\models\OsagoRequest;
use common\models\PaymeSubscribeRequest;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class Step5Form extends \yii\base\Model
{
    public $kasko_by_subscription_uuid;
    public $card_number;
    public $card_expiry;

    public function rules()
    {
        return [
            [['kasko_by_subscription_uuid', 'card_number', 'card_expiry'], 'required'],
            [['kasko_by_subscription_uuid'], 'string', 'max' => 255],
            [['kasko_by_subscription_uuid'], UuidValidator::className()],
            [['kasko_by_subscription_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoBySubscription::className(), 'targetAttribute' => ['kasko_by_subscription_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['f_user_id' => Yii::$app->user->id])
                    ->andWhere(['not in', 'status', [
                        KaskoBySubscription::STATUS['step1'], KaskoBySubscription::STATUS['step2'], KaskoBySubscription::STATUS['step3'], KaskoBySubscription::STATUS['payed'], KaskoBySubscription::STATUS['canceled']
                    ]]);
            }],
        ];
    }

    public function save()
    {
        $kasko_by_subscription = KaskoBySubscription::findOne(['uuid' => $this->kasko_by_subscription_uuid]);

        $payme_subscribe_request = PaymeSubscribeRequest::sendRequest(
                PaymeSubscribeRequest::METHODS['card_create'],
                [
                    'card' => ['number' => $this->card_number, 'expire' => $this->card_expiry],
                    'save' => true,
                ],
                KaskoBySubscription::className(),
                $kasko_by_subscription->id
            );

        $kasko_by_subscription->status = KaskoBySubscription::STATUS['step5'];
        $kasko_by_subscription->save();

        return $payme_subscribe_request;
    }
}