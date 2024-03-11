<?php

namespace common\services\fond;

use common\custom\exceptions\BadRequestHttpException;
use common\helpers\DateHelper;
use common\models\Osago;
use yii\helpers\VarDumper;

class FondService
{
    public static function getServices(): array
    {
        return [
            new InsonlineService(),
            new KapitalService(),
        ];
    }

    /**
     * @throws \Exception
     */
    public static function getAutoInfo($insurer_tech_pass_series, $insurer_tech_pass_number, $autonumber, bool $throw_error = false, $osago = (new Osago())): array|false
    {
        return self::runServiceFunction('getAutoInfo', $insurer_tech_pass_series, $insurer_tech_pass_number, $autonumber, $throw_error, $osago);
    }

    /**
     * @throws \Exception
     */
    public static function getDriverInfoByPinflOrBirthday($insurer_passport_series, $insurer_passport_number, $pinfl = null, $birthday = null, bool $throw_error = false, $osago = (new Osago())): array|false
    {
        if (!empty($pinfl))
        {
            $driver_data = self::getDriverInfoByPinfl($pinfl, $insurer_passport_series, $insurer_passport_number, $throw_error, $osago);
            if (is_array($driver_data))
                $driver_data = array_merge($driver_data, ['PINFL' => $pinfl]);

            return $driver_data;
        }
        elseif (!empty($birthday))
        {
            $driver_data = self::getDriverInfoByBirthdayPass($birthday, $insurer_passport_series, $insurer_passport_number, $throw_error, $osago);
            $result = [
                'BIRTH_DATE' => DateHelper::date_format($birthday, 'Y-m-d', 'd.m.Y'),
                'PINFL' => $driver_data['PINFL'] ?? null,
                'LAST_NAME_LATIN' => $driver_data['LAST_NAME'] ?? null,
                'FIRST_NAME_LATIN' => $driver_data['FIRST_NAME'] ?? null,
                'MIDDLE_NAME_LATIN' => $driver_data['MIDDLE_NAME'] ?? null,
                'LAST_NAME_ENG' => $driver_data['LAST_NAME_ENG'] ?? null,
                'FIRST_ENG' => $driver_data['FIRST_NAME_ENG'] ?? null,
                'OBLAST' => $driver_data['REGION_ID'] ?? null,
                'RAYON' => $driver_data['DISTRICT_ID'] ?? null,
            ];

            if (!is_array($driver_data) or !array_key_exists('PINFL', $driver_data) or empty($driver_data['PINFL']))
                return $result;
            $license_data = self::getDriverLicenseInfoByPinfl($driver_data['PINFL'], $insurer_passport_series, $insurer_passport_number, $throw_error, $osago);
            return array_merge($result, [
                'LICENSE_SERIA' => $license_data['LICENSE_SERIA']  ?? null,
                'LICENSE_NUMBER' => $license_data['LICENSE_NUMBER']  ?? null,
                'ISSUE_DATE' => $license_data['ISSUE_DATE']  ?? null,
            ]);
        }
        else
            throw new BadRequestHttpException('pinfl or birthday should not be empty');
    }

    /**
     * @throws \Exception
     */
    public static function getDriverInfoByPinfl($pinfl, $insurer_passport_series, $insurer_passport_number, bool $throw_error = false, $osago = (new Osago())): array|false
    {
        return self::runServiceFunction('getDriverInfoByPinfl', $pinfl, $insurer_passport_series, $insurer_passport_number, $throw_error, $osago);
    }

    /**
     * @throws \Exception
     */
    public static function getDriverLicenseInfoByPinfl($pinfl, $insurer_passport_series, $insurer_passport_number, bool $throw_error = false, $osago = (new Osago())): array|false
    {
        return self::runServiceFunction('getDriverLicenseInfoByPinfl', $pinfl, $insurer_passport_series, $insurer_passport_number, $throw_error, $osago);
    }

    /**
     * @throws \Exception
     */
    public static function getDriverInfoByBirthdayPass($insurer_birthday, $insurer_passport_series, $insurer_passport_number, bool $throw_error = false, $osago = (new Osago())): array|false
    {
        return self::runServiceFunction('getDriverInfoByBirthdayPass', $insurer_birthday, $insurer_passport_series, $insurer_passport_number, $throw_error, $osago);
    }

    /**
     * @throws \Exception
     */
    public static function calc($vehicle, $use_territory, $period, $driver_limit, $discount = 1, bool $throw_error = false, $osago = (new Osago())): array
    {
        return self::runServiceFunction('calc', $vehicle, $use_territory, $period, $driver_limit, $discount, $throw_error, $osago);
    }

    private static function runServiceFunction($function, ...$args)
    {
        $result = false;
        $e = false;
        /** @var FondServiceInterface $service */
        foreach (self::getServices() as $service) {
            try {
                if ($result = $service->$function(...$args))
                    return $result;
                $exception = null;
            }catch (\Exception $exception){
                $e = $exception;
            }
        }

        if (!empty($e))
            throw $e;

        return $result;
    }
}