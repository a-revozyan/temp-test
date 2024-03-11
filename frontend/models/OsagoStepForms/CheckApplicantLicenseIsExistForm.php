<?php

namespace frontend\models\OsagoStepForms;

use common\helpers\DateHelper;
use common\models\Osago;
use common\services\fond\FondService;
use Yii;
use yii\web\BadRequestHttpException;

class CheckApplicantLicenseIsExistForm extends \yii\base\Model
{
    public $osago_uuid;

    public function rules()
    {
        return [
            [['osago_uuid'], 'required'],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'osago_uuid' => Yii::t('app', 'osago_uuid'),
        ];
    }

    public function check()
    {
        $osago = Osago::findOne(['uuid' => $this->osago_uuid]);

        $driver_info = FondService::getDriverInfoByPinfl($osago->insurer_pinfl, $osago->insurer_passport_series, $osago->insurer_passport_number, false, $osago);

        if (!is_array($driver_info) or empty($driver_info['LICENSE_SERIA']) or empty($driver_info['LICENSE_NUMBER']) or empty($driver_info['ISSUE_DATE']))
            throw new BadRequestHttpException(Yii::t('app', 'applicant driver licenseNumber and licenseSeria not found in FOND'), Osago::FRONT_ERROR_CODE['applicant_license_not_found_in_fond']);

        $osago->insurer_license_series = $driver_info['LICENSE_SERIA'];
        $osago->insurer_license_number = $driver_info['LICENSE_NUMBER'];
        $osago->insurer_license_given_date = !empty($driver_info['ISSUE_DATE']) ? DateHelper::date_format($driver_info['ISSUE_DATE'], 'd.m.Y', 'Y-m-d') : null;
        $osago->save();

        return true;
    }
}