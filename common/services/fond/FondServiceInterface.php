<?php

namespace common\services\fond;

use common\models\Osago;

interface FondServiceInterface
{
    /**
     * @param $insurer_tech_pass_series
     * @param $insurer_tech_pass_number
     * @param $autonumber
     * @param bool $throw_error
     * @param Osago $osago
     * @return array|false must consist of the following keys:
     *      ERROR, ERROR_MESSAGE
     *      MARKA_ID, MODEL_ID, MODEL_NAME, ORGNAME, VEHICLE_TYPE_ID, TECH_PASSPORT_ISSUE_DATE,
     *      ISSUE_YEAR, BODY_NUMBER, ENGINE_NUMBER, USE_TERRITORY, FY, LAST_NAME, FIRST_NAME, MIDDLE_NAME,
     *      PINFL, INN
     */
    public function getAutoInfo($insurer_tech_pass_series, $insurer_tech_pass_number, $autonumber, bool $throw_error = false, Osago $osago = (new Osago())) : array|false;

    /**
     * @param $pinfl
     * @param $insurer_passport_series
     * @param $insurer_passport_number
     * @param bool $throw_error
     * @param Osago $osago
     * @return array|false must consist of the following keys:
     *      ERROR_MESSAGE, ERROR, LAST_NAME_LATIN, FIRST_NAME_LATIN, MIDDLE_NAME_LATIN, LAST_NAME_ENG,
     *      FIRST_ENG, BIRTH_DATE, OBLAST, RAYON, ISPENSIONER, LICENSE_NUMBER, LICENSE_SERIA, ISSUE_DATE, ADDRESS
     */
    public function getDriverInfoByPinfl($pinfl, $insurer_passport_series, $insurer_passport_number, bool $throw_error = false, Osago $osago = (new Osago())) : array|false;

    /**
     * @param $pinfl
     * @param $insurer_passport_series
     * @param $insurer_passport_number
     * @param bool $throw_error
     * @param Osago $osago
     * @return array|false must consist of the following keys:
     *      ERROR_MESSAGE, ERROR, LICENSE_SERIA, LICENSE_NUMBER, ISSUE_DATE
     */
    public function getDriverLicenseInfoByPinfl($pinfl, $insurer_passport_series, $insurer_passport_number, bool $throw_error = false, Osago $osago = (new Osago())) : array|false;

    /**
     * @param $insurer_birthday
     * @param $insurer_passport_series
     * @param $insurer_passport_number
     * @param bool $throw_error
     * @param Osago $osago
     * @return array|false must consist of the following keys:
     *      ERROR_MESSAGE, ERROR, PINFL, LAST_NAME, FIRST_NAME, MIDDLE_NAME, LAST_NAME_ENG, FIRST_NAME_ENG,
     *      REGION_ID, DISTRICT_ID, ADDRESS
     */
    public function getDriverInfoByBirthdayPass($insurer_birthday, $insurer_passport_series, $insurer_passport_number, bool $throw_error = false, Osago $osago = (new Osago())) : array|false;

    /**
     * @param $vehicle
     * @param $use_territory
     * @param $period
     * @param $driver_limit
     * @param $discount
     * @param bool $throw_error
     * @param Osago $osago
     * @return array must consist of the following keys:
     *      prem
     */
    public function calc($vehicle, $use_territory, $period, $driver_limit, $discount = 1, bool $throw_error = false, Osago $osago = (new Osago())) : array;
}