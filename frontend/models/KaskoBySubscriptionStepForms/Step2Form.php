<?php

namespace frontend\models\KaskoBySubscriptionStepForms;

use common\models\KaskoBySubscription;
use common\services\partnerProduct\partnerProductService;
use thamtech\uuid\validators\UuidValidator;

class Step2Form extends \yii\base\Model
{
    public $kasko_by_subscription_uuid;
    public $autonumber;
    public $tech_pass_series;
    public $tech_pass_number;

    public function rules()
    {
        return [
            [['kasko_by_subscription_uuid', 'autonumber', 'tech_pass_series', 'tech_pass_number'], 'required'],
            [['kasko_by_subscription_uuid'], 'string', 'max' => 255],
            [['kasko_by_subscription_uuid'], UuidValidator::className()],
            [['kasko_by_subscription_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoBySubscription::className(), 'targetAttribute' => ['kasko_by_subscription_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['not in', 'status', [
                        KaskoBySubscription::STATUS['payed'], KaskoBySubscription::STATUS['canceled']
                    ]]);
            }],
        ];
    }

    public function save()
    {
        $kasko_by_subscription = KaskoBySubscription::findOne(['uuid' => $this->kasko_by_subscription_uuid]);

        $kasko_by_subscription->autonumber = $this->autonumber;
        $kasko_by_subscription->tech_pass_series = $this->tech_pass_series;
        $kasko_by_subscription->tech_pass_number = $this->tech_pass_number;
        $kasko_by_subscription->save();

        partnerProductService::checkCar($kasko_by_subscription);

        $kasko_by_subscription->status = KaskoBySubscription::STATUS['step2'];
        $kasko_by_subscription->save();

        return $kasko_by_subscription;
    }
}