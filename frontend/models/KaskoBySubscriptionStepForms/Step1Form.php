<?php

namespace frontend\models\KaskoBySubscriptionStepForms;

use common\models\BridgeCompany;
use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\models\OsagoRequest;
use thamtech\uuid\helpers\UuidHelper;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class Step1Form extends \yii\base\Model
{
    public $program_id;
    public $kasko_by_subscription_uuid;
    public $super_agent_key;

    public function rules()
    {
        return [
            [['program_id'], 'required'],
            [['program_id'], 'integer'],
            [['kasko_by_subscription_uuid', 'super_agent_key'], 'string', 'max' => 255],
            [['kasko_by_subscription_uuid'], UuidValidator::className()],
            [['kasko_by_subscription_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoBySubscription::className(), 'targetAttribute' => ['kasko_by_subscription_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['not in', 'status', [
                        KaskoBySubscription::STATUS['payed']
                    ]]);
            }],
        ];
    }

    public function save()
    {
        $program_ids = ArrayHelper::map(KaskoBySubscription::getPrograms(), 'id', 'id');
        if (!in_array($this->program_id, $program_ids))
            throw new BadRequestHttpException("incorrect program_id");

        $kasko_by_subscription = $this->kasko_by_subscription_uuid ? KaskoBySubscription::findOne(['uuid' => $this->kasko_by_subscription_uuid]) : new KaskoBySubscription();

        if ($bridge_company = BridgeCompany::findOne(['code' => $this->super_agent_key]))
            $kasko_by_subscription->bridge_company_id = $bridge_company->id;

        if (empty($kasko_by_subscription->created_at))
        {
            $kasko_by_subscription->created_at = date('Y-m-d H:i:s');
            $kasko_by_subscription->uuid = UuidHelper::uuid();
        }

        $kasko_by_subscription->calc_type = KaskoBySubscription::DEFAULT_CALC_TYPE;
        $kasko_by_subscription->count = KaskoBySubscription::DEFAULT_COUNT;
        $kasko_by_subscription->status = KaskoBySubscription::STATUS['step1'];
        $kasko_by_subscription->program_id = $this->program_id;

        $calc_result = OsagoRequest::sendKaskoBySubscriptionPolicyRequest(OsagoRequest::URLS['kasko_by_subscription_calc'], new KaskoBySubscriptionPolicy(), [
            "program_id" => $kasko_by_subscription->program_id,
            "calc_type" => $kasko_by_subscription->calc_type,
            "count" => $kasko_by_subscription->count,
        ])['response'];

        $kasko_by_subscription->amount_uzs = $calc_result->amount;
        $kasko_by_subscription->amount_avto = $calc_result->amount_avto;
        $kasko_by_subscription->save();

        return $kasko_by_subscription;
    }
}