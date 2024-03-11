<?php

namespace common\services\partnerProduct;

use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\models\Osago;
use common\models\Partner;

class partnerProductService
{
    public static function checkCar(KaskoBySubscription $kbs)
    {
        return self::runKbsFunction('checkCar', $kbs);
    }

    public static function checkPerson(KaskoBySubscription $kbs)
    {
        return self::runKbsFunction('checkPerson', $kbs);
    }

    public static function kbsSave(KaskoBySubscriptionPolicy $kbsp, $begin_date)
    {
        return self::runPartnerServiceFunction($kbsp->kaskoBySubscription->partner_id, 'kbsSave', $kbsp, $begin_date);
    }

    public static function kbsConfirm(KaskoBySubscriptionPolicy $kbsp)
    {
        return self::runPartnerServiceFunction($kbsp->kaskoBySubscription->partner_id, 'kbsConfirm', $kbsp);
    }

    public static function osagoSave(Osago $osago)
    {
        return self::runPartnerServiceFunction($osago->partner_id, 'osagoSave', $osago);
    }

    public static function osagoConfirm(Osago $osago)
    {
        return self::runPartnerServiceFunction($osago->partner_id, 'osagoConfirm', $osago);
    }

    private static function runPartnerServiceFunction($partner_id, $function, ...$arg)
    {
        $partnerServices = [
            Partner::PARTNER['gross'] => grossProductService::class,
            Partner::PARTNER['neo'] => neoProductService::class,
            Partner::PARTNER['insonline'] => insonlineProductService::class,
            Partner::PARTNER['kapital'] => kapitalProductService::class,
        ];

        return $partnerServices[$partner_id]::$function(...$arg);
    }

    private static function runKbsFunction($function, ...$args)
    {
        $partner_services = [
//            grossProductService::class,
            neoProductService::class,
        ];

        $result = false;
        $e = false;

        foreach ($partner_services as $service) {
            try {
                if ($result = $service::$function(...$args))
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