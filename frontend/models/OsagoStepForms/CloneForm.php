<?php

namespace frontend\models\OsagoStepForms;

use common\helpers\DateHelper;
use common\models\Osago;
use common\models\OsagoDriver;
use common\models\UniqueCode;
use thamtech\uuid\helpers\UuidHelper;
use Yii;
use yii\helpers\ArrayHelper;
use common\custom\exceptions\BadRequestHttpException;

class CloneForm extends \yii\base\Model
{
    public $code;
    public $insurer_passport_series;
    public $insurer_passport_number;
    public $insurer_birthday;
    public $insurer_pinfl;
    public $drivers;

    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'exist', 'skipOnError' => true, 'targetClass' => UniqueCode::className(), 'targetAttribute' => ['code' => 'code']],
            [['insurer_passport_series', 'insurer_passport_number', 'insurer_pinfl'], 'string', 'max' => 255],
            [['insurer_birthday'], 'date', 'format' => 'php:d.m.Y'],
            [['drivers'], 'checkIsArray'],
            [['drivers'], 'filter', 'filter' => function ($value) {
                try {
                    $data = $value;
                    $dynamicModel = (new \yii\base\DynamicModel(['pinfl', 'id']))
                        ->addRule(['id', 'pinfl'], 'required')
                        ->addRule(['id'], 'integer')
                        ->addRule(['pinfl'], 'string', ['max' => 255]);
                    if (empty($data))
                        $data = [];
                    foreach ($data as $item) {
                        $dynamicModel->setAttributes((array)$item);
                        $dynamicModel->validate();
                        if ($dynamicModel->hasErrors()) {
                            $this->addError('drivers', $dynamicModel->getErrors());
                            return null;
                        }
                    }

                    return $value;
                } catch (\Exception $e) {
                    $this->addError('drivers', $e->getMessage());
                    return null;
                }
            }]
        ];
    }

    public function checkIsArray(){
        if(!is_array($this->drivers)){
            $this->addError('drivers','drivers is not array!');
        }
    }

    public function save()
    {
        $unique_code = UniqueCode::findOne(['code' => $this->code]);

        if (Osago::find()->where(['unique_code_id' => $unique_code->id])->andWhere(['in', 'status', [
            Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy'],
        ]])->exists())
            throw new BadRequestHttpException(Yii::t('app', "Kechirasiz, bu kod link orqali allaqachon chegirma olingan"));

        $osago = Osago::findOne($unique_code->clonable_id);

        $new_osago = new Osago();
        $attrs = $osago->getAttributes(null, [
            'id', 'policy_pdf_url', 'policy_number', 'begin_date', 'end_date', 'uuid', 'trans_id', 'payed_date', 'created_at'
        ]);
        $new_osago->setAttributes($attrs);
        $new_osago->uuid = UuidHelper::uuid();
        $new_osago->f_user_id = Yii::$app->user->identity->id;
        $new_osago->unique_code_id = $unique_code->id;
        $new_osago->status = Osago::STATUS['step3'];
        $new_osago->applicant_is_driver = $osago->applicant_is_driver;
        $new_osago->created_at = time();
        $new_osago->save();
        $new_osago->getAutoAndOwnerInfo(true);
        $new_osago->amount_uzs = $new_osago->getAmountUzs(false);
        $new_osago->save();

        if (!empty($this->insurer_passport_number))
        {
            $step1 = new Step1Form();
            $step1->insurer_tech_pass_series = $new_osago->insurer_tech_pass_series;
            $step1->insurer_tech_pass_number = $new_osago->insurer_tech_pass_number;
            $step1->autonumber = $new_osago->autonumber;
            $step1->osago_uuid = $new_osago->uuid;
            $step1->insurer_passport_series = $this->insurer_passport_series;
            $step1->insurer_passport_number = $this->insurer_passport_number;
            $step1->insurer_pinfl = $this->insurer_pinfl;
            $step1->insurer_birthday = $this->insurer_birthday;
            $step1->insurer_inn = $new_osago->insurer_inn;
            $new_osago = $step1->save();
        }

        $form_drivers = ArrayHelper::index($this->drivers ?? [], 'id');
        $drivers = $osago->drivers;

        foreach ($drivers as $driver) {

            $pinfl = $form_drivers[$driver->id]->pinfl ?? null;
            if (empty($pinfl))
                $pinfl = $driver->pinfl;

            if (empty($pinfl))
                throw new BadRequestHttpException(Yii::t('app', 'pinfl is required'), Osago::FRONT_ERROR_CODE['pinfl_required'], ['driver_key' => $driver->id]);

            $this->save_driver($driver, $new_osago, $driver->id, $driver->with_accident ?? false, $pinfl);
        }

        $new_osago->setAccidentAmount(true);
        return $new_osago;
    }

    public function save_driver($driver, $osago, $index, $with_accident, $pinfl)
    {
        $check_license_is_exist = new CheckLicenseIsExistForm();
        $check_license_is_exist->osago_uuid = $osago->uuid;
        $check_license_is_exist->birthday = !empty($driver->birthday) ? DateHelper::date_format($driver->birthday, 'd.m.Y', 'Y-m-d') : null;
        $check_license_is_exist->pinfl = $pinfl;
        $check_license_is_exist->passport_series = $driver->passport_series;
        $check_license_is_exist->passport_number = $driver->passport_number;
        $check_license_is_exist->license_series = $driver->license_series ?? null;
        $check_license_is_exist->license_number = $driver->license_number ?? null;
        $check_license_is_exist->license_given_date = !empty($driver->license_given_date) ? DateHelper::date_format($driver->license_given_date, 'd.m.Y', 'Y-m-d') : null;
        $check_license_is_exist->relationship_id = $driver->relationship_id ?? null;

        $check_license_is_exist->check($index, $with_accident, OsagoDriver::STATUS['verified']);
    }
}
