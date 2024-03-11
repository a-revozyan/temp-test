<?php

namespace frontend\models\KaskoBySubscriptionStepForms;

use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\models\OsagoRequest;
use common\models\PaymeSubscribeRequest;
use frontend\controllers\PaymeController;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class ChangeStatusForm extends \yii\base\Model
{
    public $kasko_by_subscription_uuid;
    public $status;

    public function rules()
    {
        return [
            [['kasko_by_subscription_uuid', 'status'], 'required'],
            [['status'], 'in', 'range' => [KaskoBySubscription::STATUS['payed'], KaskoBySubscription::STATUS['canceled']]],
            [['kasko_by_subscription_uuid'], 'string', 'max' => 255],
            [['kasko_by_subscription_uuid'], UuidValidator::className()],
            [['kasko_by_subscription_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoBySubscription::className(), 'targetAttribute' => ['kasko_by_subscription_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['f_user_id' => Yii::$app->user->id])
                    ->andWhere(['in', 'status', [
                        KaskoBySubscription::STATUS['payed'],  KaskoBySubscription::STATUS['canceled']
                    ]]);
            }],
        ];
    }

    public function save()
    {
        $kasko_by_subscription = KaskoBySubscription::findOne(['uuid' => $this->kasko_by_subscription_uuid]);

        $is_paying = false;

        if (
            $kasko_by_subscription->status == KaskoBySubscription::STATUS['canceled']
            and $this->status == KaskoBySubscription::STATUS['payed']
            and (
                is_null($kasko_by_subscription->lastKaskoBySubscriptionPolicy)
                or $kasko_by_subscription->lastKaskoBySubscriptionPolicy->end_date <= date('Y-m-d 23:59:59')
            )
        )
        {
            $is_paying = true;
            $kasko_by_subscription_policy = new KaskoBySubscriptionPolicy();
            $kasko_by_subscription_policy->partner_id = KaskoBySubscriptionPolicy::DEFAULT_PARTNER_ID;
            $kasko_by_subscription_policy->kasko_by_subscription_uuid = $kasko_by_subscription->id;
            $kasko_by_subscription_policy->saved_card_id = $kasko_by_subscription->saved_card_id;
            $kasko_by_subscription_policy->status = KaskoBySubscriptionPolicy::STATUS['created'];
            $kasko_by_subscription_policy->created_at = date('Y-m-d H:i:s');
            $kasko_by_subscription_policy->save();

            $trans_no = PaymeSubscribeRequest::sendRequest(
                PaymeSubscribeRequest::METHODS['receipt_create'],
                [
                    'amount' => $kasko_by_subscription->amount_uzs * 100,
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

            PaymeSubscribeRequest::sendRequest(
                PaymeSubscribeRequest::METHODS['receipt_pay'],
                [
                    'id' => $trans_no,
                    'token' => $kasko_by_subscription->savedCard->card_id,
                ],
                KaskoBySubscriptionPolicy::className(),
                $kasko_by_subscription_policy->id,
            );
        }

        if (!$is_paying)
            $kasko_by_subscription->status = $this->status;

        $kasko_by_subscription->save();

        return $kasko_by_subscription;
    }
}