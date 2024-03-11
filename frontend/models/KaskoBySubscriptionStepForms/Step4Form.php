<?php

namespace frontend\models\KaskoBySubscriptionStepForms;

use common\models\KaskoBySubscription;
use common\models\Partner;
use thamtech\uuid\validators\UuidValidator;
use Yii;

class Step4Form extends \yii\base\Model
{
    public $kasko_by_subscription_uuid;
    public $partner_id;

    public function rules()
    {
        return [
            [['kasko_by_subscription_uuid', 'partner_id'], 'required'],
            [['partner_id'], 'integer'],
            [['partner_id'], 'in', 'range' => [Partner::PARTNER['gross'], Partner::PARTNER['neo']]],
            [['kasko_by_subscription_uuid'], 'string', 'max' => 255],
            [['kasko_by_subscription_uuid'], UuidValidator::className()],
            [['kasko_by_subscription_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoBySubscription::className(), 'targetAttribute' => ['kasko_by_subscription_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['f_user_id' => Yii::$app->user->id])
                    ->andWhere(['not in', 'status', [
                        KaskoBySubscription::STATUS['step1'], KaskoBySubscription::STATUS['step2'], KaskoBySubscription::STATUS['payed'], KaskoBySubscription::STATUS['canceled']
                    ]]);
            }],
        ];
    }

    public function save()
    {
        $kasko_by_subscription = KaskoBySubscription::findOne(['uuid' => $this->kasko_by_subscription_uuid]);
        $kasko_by_subscription->partner_id = $this->partner_id;
        $kasko_by_subscription->status = KaskoBySubscription::STATUS['step4'];
        $kasko_by_subscription->save();

        return $kasko_by_subscription;
    }
}