<?php

namespace common\services\partnerProduct;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\models\NeoRequest;
use common\models\Osago;
use common\services\partnerProduct\interfaces\KaskoBySubscriptionInterface;
use common\services\partnerProduct\interfaces\OsagoInterface;

class neoProductService implements KaskoBySubscriptionInterface, OsagoInterface
{
    public static function checkCar(KaskoBySubscription $kbs)
    {
        $response_arr = NeoRequest::sendKaskoBySubscriptionPolicyRequest(NeoRequest::URLS['kasko_by_subscription_check_car'], new KaskoBySubscriptionPolicy(), [
            "gos_number" => $kbs->autonumber,
            "tech_sery" => $kbs->tech_pass_series,
            "tech_number" => $kbs->tech_pass_number,
        ]);

        return true;
    }

    public static function checkPerson(KaskoBySubscription $kbs): false|int
    {
        $begin_date = date('d.m.Y', strtotime(' +1 day'));
        return self::kbsSaveBase($kbs, $begin_date, new KaskoBySubscriptionPolicy());
    }

    public static function kbsSave(KaskoBySubscriptionPolicy $kbsp, $begin_date): false|int
    {
        return self::kbsSaveBase($kbsp->kaskoBySubscription, $begin_date, $kbsp);
    }

    private static function kbsSaveBase(KaskoBySubscription $kbs, $begin_date, KaskoBySubscriptionPolicy $kbsp)
    {
        $response_arr = NeoRequest::sendKaskoBySubscriptionPolicyRequest(NeoRequest::URLS['kasko_by_subscription_save'], $kbsp, [
            "program_id" => KaskoBySubscription::NEO_PROGRAMS_BY_GROSS[$kbs->program_id],
            "gos_number" => $kbs->autonumber,
            "tech_sery" => $kbs->tech_pass_series,
            "tech_number" => $kbs->tech_pass_number,
            "phone_number" => $kbs->fUser->phone,
            "applicant_pass_seria" => $kbs->applicant_pass_series,
            "applicant_pass_number" => $kbs->applicant_pass_number,
            "applicant_pinfl" => $kbs->applicant_pinfl,
            "applicant_birthday" => DateHelper::date_format($kbs->applicant_birthday, 'Y-m-d', 'd.m.Y'),
            "begin_date" => $begin_date,
            "premium" => $kbs->amount_uzs,
            "liability" => $kbs->amount_avto,
        ]);

        if (is_array($response_arr) and array_key_exists('order_id', $response_arr))
            return $response_arr['order_id'];

        return false;
    }

    public static function kbsConfirm(KaskoBySubscriptionPolicy $kbsp): false|array
    {
        $response_arr = NeoRequest::sendKaskoBySubscriptionPolicyRequest(NeoRequest::URLS['kasko_by_subscription_confirm'], $kbsp, [
            'order_id' => $kbsp->order_id_in_gross,
        ]);

        return [
            'policy_url' => $response_arr['pdf_url'],
            'policy_number' => $response_arr['policy_seria'] . " " . $response_arr['policy_number']
        ];
    }

    public static function osagoSave(Osago $osago): int|false
    {
        $drivers = [];
        foreach ($osago->drivers as $driver) {
            $drivers[] = [
                'passport__seria' => $driver->passport_series,
                'passport__number' => $driver->passport_number,
                'driver_birthday' => date('d.m.Y', $driver->birthday),
                'driver_pinfl' => $driver->pinfl,
                'licenseNumber' => $driver->license_number,
                'licenseSeria' => $driver->license_series,
                'licenseIssueDate' => !empty($driver->license_given_date) ? DateHelper::date_format($driver->license_given_date, 'Y-m-d', 'd.m.Y') : null,
                'relative' => ($osago->region_id == Osago::REGION_TASHKENT_ID and $osago->number_drivers_id == Osago::NO_LIMIT_NUMBER_DRIVERS_ID and $driver->relationship_id == null) ? 1 : NeoRequest::RELATIVES[$driver->relationship_id],
            ];
        }
        $request_body = [
            "gos_number" => $osago->autonumber,
            "tech_sery" => $osago->insurer_tech_pass_series,
            "tech_number" => $osago->insurer_tech_pass_number,
            "period_id" => $osago->period_id,
            "number_drivers_id" => $osago->number_drivers_id,
            "applicant_info" => $osago->f_user_is_owner,
            "phone_number" => $osago->user->phone ?? GeneralHelper::env('default_phone_for_osago_gross'),
            "drivers" => $drivers,
            "amount_uzs" => $osago->getAmountUzsWithoutDiscount(),
            "applicant_is_driver" => ($osago->number_drivers_id == Osago::NO_LIMIT_NUMBER_DRIVERS_ID) ? false : $osago->applicant_is_driver,
            "applicant_license_seria" => $osago->insurer_license_series,
            "applicant_license_number" => $osago->insurer_license_number,
            "applicant_license_issue_date" => !empty($osago->insurer_license_given_date) ? DateHelper::date_format($osago->insurer_license_given_date, 'Y-m-d', 'd.m.Y') : null,

            "owner__pass_seria" => $osago->insurer_passport_series,
            "owner__pass_number" => $osago->insurer_passport_number,
            "owner_birthday" => !is_null($osago->insurer_birthday) ? date('d.m.Y', $osago->insurer_birthday) : "" ,
            "owner_pinfl" => $osago->insurer_pinfl,
        ];
        if (!empty($osago->begin_date))
            $request_body = array_merge($request_body, [
                'startDate' => DateHelper::date_format($osago->begin_date, 'Y-m-d', 'd.m.Y')
            ]);
        if (!$osago->f_user_is_owner or $osago->is_juridic){
            $request_body = array_merge($request_body, [
                "applicant__inn" => $osago->insurer_inn,
                "owner__inn" => $osago->insurer_inn,
                "applicant__pass_seria" => $osago->insurer_passport_series,
                "applicant__pass_number" => $osago->insurer_passport_number,
                "applicant__birthday" => date('d.m.Y', $osago->insurer_birthday),
                "applicant__pinfl" => $osago->insurer_pinfl,
            ]);
        }

        $response_arr = NeoRequest::sendOsagoRequest(NeoRequest::URLS['save_osago_police_url'], $osago, $request_body);
        if (is_array($response_arr) and array_key_exists('response', $response_arr) and isset($response_arr['response']->order_id))
            return $response_arr['response']->order_id;
        return false;
    }

    public static function osagoConfirm(Osago $osago): array|false
    {
        $response_arr = NeoRequest::sendOsagoRequest(NeoRequest::URLS['confirm_policy'], $osago, ['order_id' => $osago->order_id_in_gross], false);
        if (!(is_array($response_arr) and array_key_exists('result', $response_arr) and !empty($response_arr['result']->pdf_url)))
            $response_arr = NeoRequest::sendOsagoRequest(NeoRequest::URLS['get_policy_data'], $osago, ['order_id' => $osago->order_id_in_gross], false);

        if (is_array($response_arr) and array_key_exists('result', $response_arr) and !empty($response_arr['result']->pdf_url))
            return [
                'policy_number' => $response_arr['result']->policy_number,
                'policy_pdf_url' => $response_arr['result']->pdf_url,
                'begin_date' => $response_arr['result']->begin_date,
                'end_date' => $response_arr['result']->end_date,
            ];

        return  false;
    }
}