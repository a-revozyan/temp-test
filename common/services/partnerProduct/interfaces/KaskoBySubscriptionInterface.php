<?php

namespace common\services\partnerProduct\interfaces;

use common\models\KaskoBySubscriptionPolicy;

interface KaskoBySubscriptionInterface
{
    /**
     * @param KaskoBySubscriptionPolicy $kbsp
     * @param $begin_date
     * @return mixed
     */
    public static function kbsSave(KaskoBySubscriptionPolicy $kbsp, $begin_date) : int|false;

    /**
     * @param KaskoBySubscriptionPolicy $kbsp
     * @return array|false must consist of the following keys:
     *          policy_url, policy_number
     *
     */
    public static function kbsConfirm(KaskoBySubscriptionPolicy $kbsp) : array|false;
}