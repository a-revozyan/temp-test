<?php

namespace common\services\partnerProduct;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\models\NeoRequest;
use common\models\Osago;
use common\models\OsagoRequest;
use common\services\partnerProduct\interfaces\KaskoBySubscriptionInterface;
use common\services\partnerProduct\interfaces\OsagoInterface;
use Http\Message\Authentication\Bearer;

class grossProductService implements KaskoBySubscriptionInterface, OsagoInterface
{
    public static function checkCar(KaskoBySubscription $kbs)
    {
        $response_array = OsagoRequest::sendKaskoBySubscriptionPolicyRequest(OsagoRequest::URLS['kasko_by_subscription_save'], new KaskoBySubscriptionPolicy(), [
            "program_id" => $kbs->program_id,
            "calc_type" => $kbs->calc_type,
            "count" => $kbs->count,
            "gos_number" => $kbs->autonumber,
            "tech_sery" => $kbs->tech_pass_series,
            "tech_number" => $kbs->tech_pass_number,
            "phone_number" => GeneralHelper::env('default_phone_for_osago_gross'),
            "owner_is_applicant" => false,
            "begin_date" => date('d.m.Y', strtotime(' +1 day'))
        ], false);

        if (is_array($response_array) and array_key_exists('result', $response_array) and !$response_array['result'] and $response_array['error'] == 11)
            OsagoRequest::throw_kasko_by_subscription_error($response_array['error'], $response_array['message']);
        OsagoRequest::throw_unexpected_error(true, $response_array);
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
        $response_arr = OsagoRequest::sendKaskoBySubscriptionPolicyRequest(OsagoRequest::URLS['kasko_by_subscription_save'], $kbsp, [
            "program_id" => $kbs->program_id,
            "calc_type" => $kbs->calc_type,
            "count" => $kbs->count,
            "gos_number" => $kbs->autonumber,
            "tech_sery" => $kbs->tech_pass_series,
            "tech_number" => $kbs->tech_pass_number,
            "phone_number" => $kbs->fUser->phone,
            "owner_is_applicant" => false,
            "applicant__pass_seria" => $kbs->applicant_pass_series,
            "applicant__pass_number" => $kbs->applicant_pass_number,
            "applicant__pinfl" => $kbs->applicant_pinfl,
            "applicant__birthday" => DateHelper::date_format($kbsp->kaskoBySubscription->applicant_birthday, 'Y-m-d', 'd.m.Y'),
            "begin_date" => $begin_date
        ]);

        if (is_array($response_arr) and array_key_exists('response', $response_arr) and isset($response_arr['response']->order_id))
            return $response_arr['response']->order_id;

        return false;
    }

    public static function kbsConfirm(KaskoBySubscriptionPolicy $kbsp): false|array
    {
        $response_arr = OsagoRequest::sendKaskoBySubscriptionPolicyRequest(OsagoRequest::URLS['kasko_by_subscription_confirm'], $kbsp, [
            'order_id' => $kbsp->order_id_in_gross,
            'trans_no' => "sb-gross-avto-limit",
        ]);

        return [
            'policy_url' => $response_arr['response']->url,
            'policy_number' => $response_arr['response']->policy
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
                'relative' => ($osago->region_id == Osago::REGION_TASHKENT_ID and $osago->number_drivers_id == Osago::NO_LIMIT_NUMBER_DRIVERS_ID and $driver->relationship_id == null) ? 1 : $driver->relationship_id,
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
                'start_date' => DateHelper::date_format($osago->begin_date, 'Y-m-d', 'd.m.Y')
            ]);
        if (!$osago->f_user_is_owner or $osago->is_juridic){
            $request_body = array_merge($request_body, [
                "applicant_inn" => $osago->insurer_inn,
                "owner_inn" => $osago->insurer_inn,
                "applicant__pass_seria" => $osago->insurer_passport_series,
                "applicant__pass_number" => $osago->insurer_passport_number,
                "applicant__birthday" => date('d.m.Y', $osago->insurer_birthday),
                "applicant__pinfl" => $osago->insurer_pinfl,
            ]);
        }

        $response_arr = OsagoRequest::sendRequest(OsagoRequest::URLS['save_osago_police_url'], $osago, $request_body);
        if (is_array($response_arr) and array_key_exists('response', $response_arr) and isset($response_arr['response']->order_id))
            return $response_arr['response']->order_id;

        return false;
    }

    public static function osagoConfirm(Osago $osago): array|false
    {
        $response_arr = OsagoRequest::sendRequest(OsagoRequest::URLS['confirm_policy'], $osago, ['order_id' => $osago->order_id_in_gross], false);
        if (!(is_array($response_arr) and array_key_exists('result', $response_arr) and !empty($response_arr['result']->pdf_url)))
            $response_arr = OsagoRequest::sendRequest(OsagoRequest::URLS['get_policy_data'], $osago, ['order_id' => $osago->order_id_in_gross], false);

        if (is_array($response_arr) and array_key_exists('result', $response_arr) and !empty($response_arr['result']->pdf_url))
            return [
                'policy_number' => $response_arr['result']->policy_number,
                'policy_pdf_url' => $response_arr['result']->pdf_url,
                'begin_date' => $response_arr['result']->begin_date,
                'end_date' => $response_arr['result']->end_date,
            ];

        return false;
    }
}