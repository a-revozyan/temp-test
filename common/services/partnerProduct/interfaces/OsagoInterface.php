<?php

namespace common\services\partnerProduct\interfaces;

use common\models\Osago;

interface OsagoInterface
{
    /**
     * @param Osago $osago
     * @return int|false|array
     */
    public static function osagoSave(Osago $osago) : int|false|array;

    /**
     * @param Osago $osago
     * @return array|false must consist of the following keys:
     *          policy_number, policy_pdf_url, begin_date, end_date
     *
     */
    public static function osagoConfirm(Osago $osago) : array|false;
}