<?php

namespace frontend\models\KaskoBySubscriptionStepForms;

use common\models\KaskoBySubscription;
use common\services\fond\FondService;
use thamtech\uuid\validators\UuidValidator;
use Yii;

class Step3Form extends \yii\base\Model
{
    public $kasko_by_subscription_uuid;
    public $applicant_name;
    public $applicant_pass_series;
    public $applicant_pass_number;
    public $applicant_birthday;
    public $applicant_pinfl;

    public function rules()
    {
        return [
            [['kasko_by_subscription_uuid', 'applicant_pass_series', 'applicant_pass_number', 'applicant_pinfl'], 'required'],
            [['kasko_by_subscription_uuid', 'applicant_pinfl'], 'string', 'max' => 255],
            [['kasko_by_subscription_uuid'], UuidValidator::className()],
            [['kasko_by_subscription_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoBySubscription::className(), 'targetAttribute' => ['kasko_by_subscription_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['not in', 'status', [
                        KaskoBySubscription::STATUS['payed'], KaskoBySubscription::STATUS['canceled'], KaskoBySubscription::STATUS['step1']
                    ]]);
            }],
            [['applicant_birthday'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function save()
    {
        $kasko_by_subscription = KaskoBySubscription::findOne(['uuid' => $this->kasko_by_subscription_uuid]);

        $kasko_by_subscription->applicant_pass_series = $this->applicant_pass_series;
        $kasko_by_subscription->applicant_pass_number = $this->applicant_pass_number;
        $kasko_by_subscription->applicant_pinfl = $this->applicant_pinfl;
        $kasko_by_subscription->f_user_id = Yii::$app->user->id;
        $kasko_by_subscription->save();

//        partnerProductService::checkPerson($kasko_by_subscription);

        $person_info = FondService::getDriverInfoByPinfl(
            $this->applicant_pinfl,
            $kasko_by_subscription->applicant_pass_series, $kasko_by_subscription->applicant_pass_number, true
        );

        if (is_array($person_info))
            $kasko_by_subscription->applicant_name = ($person_info['LAST_NAME'] ?? "") . " " . ($person_info['FIRST_NAME'] ?? "") . " " . ($person_info['MIDDLE_NAME'] ?? "");

        $kasko_by_subscription->status = KaskoBySubscription::STATUS['step3'];
        $kasko_by_subscription->save();

        return $kasko_by_subscription;
    }
}