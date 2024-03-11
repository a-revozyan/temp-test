<?php

namespace common\services\fond;

use common\models\KapitalSugurtaRequest;
use common\models\Osago;
use Yii;
use yii\web\BadRequestHttpException;

class KapitalService implements FondServiceInterface
{
    public function getAutoInfo($insurer_tech_pass_series, $insurer_tech_pass_number, $autonumber, bool $throw_error = false, Osago $osago = (new Osago())): array|false
    {
        $auto_info = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['get_auto_info'], $osago, [
            'techPassportSeria' => $insurer_tech_pass_series,
            'techPassportNumber' => $insurer_tech_pass_number,
            'govNumber' => $autonumber,
        ], $throw_error, [], 'post');

        if ($throw_error and (empty($auto_info) or !is_array($auto_info)))
            throw new BadRequestHttpException(Yii::t('app', 'kapital sugurta API siga murojaatda error'));

        if (!(is_array($auto_info) and array_key_exists('ERROR', $auto_info) and $auto_info['ERROR'] == 0))
            return false;

        return $auto_info;
    }

    public function getDriverInfoByPinfl($pinfl, $insurer_passport_series, $insurer_passport_number, bool $throw_error = false, Osago $osago = (new Osago())): array|false
    {
        $driver_info = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['driver_info_by_pinfl'], $osago, [
            'pinfl' => $pinfl,
            'passportSeries' => $insurer_passport_series,
            'passportNumber' => $insurer_passport_number,
        ], $throw_error, [], 'post');

        if ($throw_error and (empty($driver_info) or !is_array($driver_info)))
            throw new BadRequestHttpException(Yii::t('app', 'kapital sugurta API siga murojaatda error'));

        if (!(is_array($driver_info) and array_key_exists('ERROR', $driver_info) and $driver_info['ERROR'] == 0))
            return false;

        return $driver_info;
    }

    public function getDriverLicenseInfoByPinfl($pinfl, $insurer_passport_series, $insurer_passport_number, bool $throw_error = false, Osago $osago = (new Osago())): array|false
    {
        $driver_info = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['driver_license_info_by_pinfl'], $osago, [
            'pinfl' => $pinfl,
            'passportSeries' => $insurer_passport_series,
            'passportNumber' => $insurer_passport_number,
        ], $throw_error, [], 'post');

        if ($throw_error and (empty($driver_info) or !is_array($driver_info)))
            throw new BadRequestHttpException(Yii::t('app', 'kapital sugurta API siga murojaatda error'));

        if (!(is_array($driver_info) and array_key_exists('ERROR', $driver_info) and $driver_info['ERROR'] == 0))
            return false;

        return $driver_info;
    }

    public function getDriverInfoByBirthdayPass($insurer_birthday, $insurer_passport_series, $insurer_passport_number, bool $throw_error = false, Osago $osago = (new Osago())): array|false
    {
        $driver_info = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['driver_info_by_birthday_pass'], $osago, [
            'birthDate' => $insurer_birthday,
            'passportSeries' => $insurer_passport_series,
            'passportNumber' => $insurer_passport_number,
        ], $throw_error, [], 'post');

        if ($throw_error and (empty($driver_info) or !is_array($driver_info)))
            throw new BadRequestHttpException(Yii::t('app', 'kapital sugurta API siga murojaatda error'));

        if (!(is_array($driver_info) and array_key_exists('ERROR', $driver_info) and $driver_info['ERROR'] == 0))
            return false;

        return $driver_info;
    }

    public function calc($vehicle, $use_territory, $period, $driver_limit, $discount = 1, bool $throw_error = false, Osago $osago = (new Osago())): array
    {
        $result = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['calc'], $osago, [
            'vehicle' => $vehicle,
            'use_territory' => $use_territory,
            'period' => $period,
            'driver_limit' => $driver_limit,
            'discount' => $discount,
        ],$throw_error, [], 'post');

        if ($throw_error and (empty($result) or !is_array($result) or !array_key_exists('prem', $result)))
            throw new BadRequestHttpException(Yii::t('app', 'kapital sugurta API siga murojaatda error'));

        return $result;
    }
}