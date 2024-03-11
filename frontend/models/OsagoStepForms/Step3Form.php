<?php

namespace frontend\models\OsagoStepForms;

use common\helpers\DateHelper;
use common\models\Osago;
use common\models\OsagoDriver;
use common\models\Partner;
use common\models\Relationship;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class Step3Form extends \yii\base\Model
{
    public $osago_uuid;
    public $drivers;
    public $applicant_is_driver;
    public $insurer_license_series;
    public $insurer_license_number;
    public $insurer_license_given_date;
    public $change_status;

    public $owner_with_accident;

    public $insurer_birthday;
    public $insurer_passport_series;
    public $insurer_passport_number;

    protected $is_with_restriction = null; // c agranicheniyami

    public function rules()
    {
        return [
            [['insurer_license_series', 'insurer_license_number', 'insurer_license_given_date',  'insurer_birthday', 'insurer_passport_series', 'insurer_passport_number'], 'safe'],
            [['osago_uuid'], 'required'],
            [['osago_uuid'], 'string', 'max' => 255],
            [['osago_uuid'], UuidValidator::className()],
            [['insurer_birthday', 'insurer_license_given_date'], 'date', 'format' => 'php:d.m.Y'],
            [['applicant_is_driver', 'owner_with_accident'], 'boolean'],
            [['owner_with_accident'], 'default', 'value' => false],
            [['applicant_is_driver'], 'required', 'when' => function($model){
                return is_null($model->is_with_restriction) ? false : $model->is_with_restriction;
            }],
            [['drivers'], 'required', 'when' => function($model){
                return is_null($model->is_with_restriction) ? false : ($model->is_with_restriction and !$this->applicant_is_driver);
            }],
            [['drivers'], 'checkIsArray'],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['is_juridic' => 0])
                    ->andWhere(['not in', 'status', [
                        Osago::STATUS['step1'], Osago::STATUS['step2'], Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']
                    ]]);
            }],
            [['change_status'], 'integer'],
            [['change_status'], 'default', 'value' => 1],
            [['drivers'], 'filter', 'filter' => function ($value) {
                try {
                    $form = $this;
                    $data = $value;
                    $dynamicModel = (new \yii\base\DynamicModel([
                        'relationship_id', 'birthday', 'license_given_date', 'passport_series', 'passport_number', 'license_series', 'license_number', 'with_accident', 'pinfl'
                    ]))->addRule(['birthday', 'passport_series', 'passport_number'], 'required')
                        ->addRule(['relationship_id'], 'required', ['when' => function($model) use ($form) {
                            return is_null($form->is_with_restriction) ? false : !$form->is_with_restriction;
                        }])
                        ->addRule(['license_series', 'license_number', 'license_given_date'], 'safe')
                        ->addRule(['relationship_id'], 'integer')
                        ->addRule(['relationship_id'],'exist', ['skipOnError' => true, 'targetClass' => Relationship::className(), 'targetAttribute' => ['relationship_id' => 'id']])
                        ->addRule(['birthday', 'license_given_date'], 'date', ['format' => 'php:d.m.Y'])
                        ->addRule(['passport_series'], 'string', ['max' => 2])
                        ->addRule(['passport_number', 'pinfl'], 'string', ['max' => 9999999])
                        ->addRule(['with_accident'], 'boolean')
                        ->addRule(['with_accident'], 'default', ['value' => false]);
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
            }],
        ];
    }

    public function checkIsArray(){
        if(!is_array($this->drivers)){
            $this->addError('drivers','drivers is not array!');
        }
    }

    public function attributeLabels()
    {
        return [
            'osago_uuid' => Yii::t('app', 'osago_uuid'),
            'drivers' => Yii::t('app', 'drivers'),
            'applicant_is_driver' => Yii::t('app', 'applicant_is_driver'),
        ];
    }

    public function save()
    {
        $osago = Osago::findOne(['uuid' => $this->osago_uuid]);
        $osago->applicant_is_driver = $this->applicant_is_driver;

        if ($this->applicant_is_driver and empty($osago->insurer_license_given_date) and empty($this->insurer_license_given_date))
            throw new BadRequestHttpException(Yii::t('app', 'applicant driver licenseNumber and licenseSeria not found in FOND'), Osago::FRONT_ERROR_CODE['applicant_license_not_found_in_fond']);

        $osago->f_user_id = Yii::$app->user->id;
        if ($osago->number_drivers_id == Osago::WITH_RESTRICTION_NUMBER_DRIVERS_ID) //c agranicheniya
            $this->is_with_restriction = true;
        else
            $this->is_with_restriction = false;

        if (!$this->validate(array_keys($this->attributes)))
            return Yii::$app->controller->sendFailedResponse($this->errors, 422);

        $osago_old_drivers = OsagoDriver::find()->where(['osago_id' => $osago->id])->all();
        foreach ($this->drivers as $index => $driver) {
            $driver->passport_series = strtoupper($driver->passport_series);

            $condition = ['passport_series' => $driver->passport_series, 'passport_number' => $driver->passport_number, 'birthday' => date_create_from_format('d.m.Y', $driver->birthday)->setTime(0, 0, 0)->getTimestamp()];
            $exist_driver = OsagoDriver::find()->where(array_merge(['osago_id' => $osago->id], $condition))->one();
            if (empty($exist_driver))
                $exist_driver = OsagoDriver::find()->where(array_merge(['osago_id' => null], $condition))->one();

            if (!empty($exist_driver))
            {
                $new_driver = new OsagoDriver();
                $new_driver->attributes = $exist_driver->attributes;
                if (!empty($driver->license_series))
                    $new_driver->license_series = $driver->license_series;
                if (!empty($driver->license_number))
                    $new_driver->license_number = $driver->license_number;
                if (!empty($driver->license_given_date))
                    $new_driver->license_given_date = DateHelper::date_format($driver->license_given_date, 'd.m.Y', 'Y-m-d');
                if (!empty($driver->relationship_id))
                    $new_driver->relationship_id = $driver->relationship_id;
                $new_driver->status = OsagoDriver::STATUS['verified'];
                $new_driver->osago_id = $osago->id;
                $new_driver->with_accident = $driver->with_accident;
                $new_driver->save();
            }else
                $this->save_driver($driver, $osago, $index, $driver->with_accident ?? false);
        }

        OsagoDriver::deleteAll(['in', 'id', ArrayHelper::getColumn($osago_old_drivers, 'id')]);

        if (in_array($osago->partner_id, [Partner::PARTNER['gross'], Partner::PARTNER['neo']]) and $osago->number_drivers_id == Osago::WITH_RESTRICTION_NUMBER_DRIVERS_ID)
        {
            $osago->owner_with_accident = $this->owner_with_accident;
            if (!$osago->applicant_is_driver)
                $osago->owner_with_accident = false;
        }

        if (!empty($this->insurer_license_series))
            $osago->insurer_license_series = $this->insurer_license_series;
        if (!empty($this->insurer_license_number))
            $osago->insurer_license_number = $this->insurer_license_number;
        if (!empty($this->insurer_license_given_date))
            $osago->insurer_license_given_date = DateHelper::date_format($this->insurer_license_given_date, 'd.m.Y', 'Y-m-d');
        if (!empty($this->insurer_passport_series))
            $osago->insurer_passport_series = $this->insurer_passport_series;
        if (!empty($this->insurer_passport_number))
            $osago->insurer_passport_number = $this->insurer_passport_number;
        if (!empty($this->insurer_birthday))
            $osago->insurer_birthday = !is_null($this->insurer_birthday) ? date_create_from_format('d.m.Y', $this->insurer_birthday)->getTimestamp() : null;
        $osago->save();

        if ($this->change_status)
            $osago->status = Osago::STATUS['step3'];

        $osago->save();
        $osago->setAccidentAmount();
        return $osago;
    }

    public function save_driver($driver, $osago, $index, $with_accident)
    {
        $check_license_is_exist = new CheckLicenseIsExistForm();
        $check_license_is_exist->osago_uuid = $osago->uuid;
        $check_license_is_exist->birthday = !empty($driver->birthday) ? DateHelper::date_format($driver->birthday, 'd.m.Y', 'Y-m-d') : null;
        $check_license_is_exist->pinfl = $driver->pinfl ?? null;
        $check_license_is_exist->passport_series = $driver->passport_series;
        $check_license_is_exist->passport_number = $driver->passport_number;
        $check_license_is_exist->license_series = $driver->license_series ?? null;
        $check_license_is_exist->license_number = $driver->license_number ?? null;
        $check_license_is_exist->license_given_date = !empty($driver->license_given_date) ? DateHelper::date_format($driver->license_given_date, 'd.m.Y', 'Y-m-d') : null;
        $check_license_is_exist->relationship_id = $driver->relationship_id ?? null;

        $check_license_is_exist->check($index, $with_accident, OsagoDriver::STATUS['verified']);
    }
}
