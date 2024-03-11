<?php

namespace frontend\models\OsagoStepForms;

use common\helpers\DateHelper;
use common\models\AccidentType;
use common\models\Osago;
use common\models\OsagoDriver;
use common\models\Relationship;
use common\services\fond\FondService;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use common\custom\exceptions\BadRequestHttpException;

class CheckLicenseIsExistForm extends \yii\base\Model
{
    public $osago_uuid;
    public $birthday;
    public $pinfl;
    public $passport_series;
    public $passport_number;
    public $license_series;
    public $license_number;
    public $license_given_date;
    public $relationship_id;

    public function rules()
    {
        return [
            [['passport_series', 'passport_number', 'birthday'], 'required'],
            [['osago_uuid', 'pinfl'], 'string', 'max' => 255],
            [['osago_uuid'], UuidValidator::className()],
            [['birthday'], 'date', 'format' => 'php:Y-m-d'],
            [['relationship_id'], 'integer'],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid']],
            [['relationship_id'],'exist', 'skipOnError' => true, 'targetClass' => Relationship::className(), 'targetAttribute' => ['relationship_id' => 'id']],
            [['license_series', 'license_number', 'license_given_date'], 'safe'],
            [['license_given_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'osago_uuid' => Yii::t('app', 'osago_uuid'),
            'birthday' => Yii::t('app', 'birthday'),
            'passport_series' => Yii::t('app', 'passport_series'),
            'passport_number' => Yii::t('app', 'passport_number'),
        ];
    }

    public function check($index = 0, $with_accident = false, $status = OsagoDriver::STATUS['created'])
    {
        if (empty($this->osago_uuid))
            $osago = new Osago();
        else
            $osago = Osago::findOne(['uuid' => $this->osago_uuid]);

        $this->passport_series = strtoupper($this->passport_series);

        if (!empty($this->birthday))
            $this->birthday = DateHelper::date_format($this->birthday, 'Y-m-d', 'd.m.Y');
        $driver_info = FondService::getDriverInfoByPinflOrBirthday($this->passport_series, $this->passport_number, $this->pinfl,  $this->birthday,false, $osago);

        if (!is_array($driver_info)
            or empty($driver_info['BIRTH_DATE'])
            or empty($driver_info['PINFL'])
            or empty($driver_info['LAST_NAME_LATIN'])
            or empty($driver_info['FIRST_NAME_LATIN'])
            or empty($driver_info['MIDDLE_NAME_LATIN'])
        )
            throw new BadRequestHttpException(Yii::t('app', 'fond_not_found driver info {driver_key}', ['driver_key' => $index]), Osago::FRONT_ERROR_CODE['driver_info_not_found_in_fond'], ['driver_key' => $index]);


        if (!empty($driver_info['ISSUE_DATE']))
            $driver_info['ISSUE_DATE'] = DateHelper::date_format($driver_info['ISSUE_DATE'], 'd.m.Y', 'Y-m-d');
        if (
           !empty($this->license_series) and !empty($this->license_number)  and !empty($this->license_given_date)
        )
            $driver_info = array_merge($driver_info, [
                'LICENSE_SERIA' => $this->license_series,
                'LICENSE_NUMBER' => $this->license_number,
                'ISSUE_DATE' => $this->license_given_date,
            ]);

        if (empty($driver_info['LICENSE_SERIA']) or empty($driver_info['LICENSE_NUMBER']) or empty($driver_info['ISSUE_DATE']))
            throw new BadRequestHttpException(Yii::t('app', 'driver licenseNumber and licenseSeria not found in FOND {driver_key}', ['driver_key' => $index]), Osago::FRONT_ERROR_CODE['driver_license_not_found_in_fond'], ['driver_key' => $index]);

        $osago_drive = new OsagoDriver();
        $osago_drive->relationship_id = $this->relationship_id;
        $osago_drive->pinfl = $driver_info['PINFL'];
        $osago_drive->first_name = $driver_info['FIRST_NAME_LATIN'];
        $osago_drive->last_name = $driver_info['LAST_NAME_LATIN'];
        $osago_drive->middle_name = $driver_info['MIDDLE_NAME_LATIN'];
        $osago_drive->passport_series = $this->passport_series;
        $osago_drive->passport_number = $this->passport_number;
        $osago_drive->license_series = $driver_info['LICENSE_SERIA'];
        $osago_drive->license_number = $driver_info['LICENSE_NUMBER'];
        $osago_drive->birthday = date_create_from_format('d.m.Y', $driver_info['BIRTH_DATE'])->setTime(0, 0, 0)->getTimestamp();
        $osago_drive->license_given_date = $driver_info['ISSUE_DATE'];
        $osago_drive->status = $status;
        $osago_drive->osago_id = $osago->id;
        $osago_drive->created_at = date('Y-m-d H:i:s');
        if (
            empty($osago->id)
            or
            ($partner = $osago->partner and $partner->accidentType and $partner->accidentType->id == AccidentType::ACCIDENT_TYPE['life'])
        )
            $osago_drive->with_accident = $with_accident;
        $osago_drive->save();

        return true;
    }
}