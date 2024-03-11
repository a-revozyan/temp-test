<?php

namespace frontend\models\OsagoStepForms;

use Yii;

class SaveForm extends \yii\base\Model
{
    public $insurer_tech_pass_series;
    public $insurer_tech_pass_number;
    public $autonumber;

    public $insurer_passport_series;
    public $insurer_passport_number;

    public $insurer_birthday;
    public $insurer_pinfl;
    public $insurer_inn;

    public $super_agent_key;

    public $number_drivers_id;
    public $period_id;
    public $begin_date;

    public $partner_id;

    public $drivers;
    public $applicant_is_driver;
    public $insurer_license_series;
    public $insurer_license_number;
    public $insurer_license_given_date;

    public $owner_with_accident;

    public function rules()
    {
        return [[
            [
                'insurer_tech_pass_series', 'insurer_tech_pass_number', 'autonumber', 'insurer_passport_series',
                'insurer_passport_number', 'insurer_birthday', 'insurer_pinfl', 'insurer_inn', 'super_agent_key', 'number_drivers_id',
                'period_id', 'begin_date', 'partner_id', 'drivers', 'applicant_is_driver', 'insurer_license_series',
                'insurer_license_number', 'insurer_license_given_date', 'owner_with_accident'
            ], 'safe'
        ]];
    }

    public function save()
    {
        $model = new Step1Form();
        $model->setAttributes($this->attributes);
        if (!$model->validate())
            return Yii::$app->controller->sendFailedResponse($model->getErrors(), 422);
        $osago = $model->save();

        $model = new Step1Form();
        $model->setAttributes(array_merge(['osago_uuid' => $osago->uuid], $this->attributes));
        if (!$model->validate())
            return Yii::$app->controller->sendFailedResponse($model->getErrors(), 422);
        $osago = $model->save();

        $model = new Step2Form();
        $model->setAttributes(array_merge(['osago_uuid' => $osago->uuid, 'change_status' => 1], $this->attributes));
        if (!$model->validate())
            return Yii::$app->controller->sendFailedResponse($model->getErrors(), 422);
        $osago = $model->save();

        $model = new Step11Form();
        $model->setAttributes(array_merge(['osago_uuid' => $osago->uuid], $this->attributes));
        if (!$model->validate())
            return Yii::$app->controller->sendFailedResponse($model->getErrors(), 422);
        $osago = $model->save();
        if ($osago->is_juridic)
            return $osago;

        $model = new Step3Form();
        $model->setAttributes(array_merge(['osago_uuid' => $osago->uuid], $this->attributes));
        if (!$model->validate())
            return Yii::$app->controller->sendFailedResponse($model->getErrors(), 422);
        $response = $model->save();
        if (is_array($response))
            return $response;
        $osago = $response;

        return $osago;
    }
}
