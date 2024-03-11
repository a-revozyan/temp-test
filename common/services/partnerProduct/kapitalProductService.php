<?php

namespace common\services\partnerProduct;

use common\helpers\DateHelper;
use common\jobs\CheckStatusKapitalAccidentJob;
use common\models\KapitalSugurtaRequest;
use common\models\Osago;
use common\models\OsagoFondData;
use common\services\fond\FondService;
use common\services\partnerProduct\interfaces\OsagoInterface;
use Yii;

class kapitalProductService implements OsagoInterface
{
    public static function osagoSave(Osago $osago): int|false|array
    {
        /** @var OsagoFondData $osago_fond_data */
        $osago_fond_data = OsagoFondData::find()->where(['osago_id' => $osago->id])->one();

        $request_body = [
            'renumber' => $osago->autonumber,
            'texpsery' => $osago->insurer_tech_pass_series,
            'texpnumber' => $osago->insurer_tech_pass_number,
            'marka' => $osago_fond_data->marka_id,
            'model' => $osago_fond_data->model_id,
            'vmodel' => $osago_fond_data->model_name,
            'type' => $osago_fond_data->vehicle_type_id,
            'texpdate' => $osago_fond_data->tech_passport_issue_date,
            'year' => $osago_fond_data->issue_year,
            'kuzov' => $osago_fond_data->body_number,
            'dvigatel' => $osago_fond_data->engine_number,
            'use_territory' => $osago_fond_data->use_territory,
            'owner_fy' => $osago_fond_data->fy,
            'owner_pinfl' => $osago->insurer_pinfl,
            'owner_birthdate' => !empty($osago->insurer_birthday) ? date('d.m.Y', $osago->insurer_birthday) : null,
            'owner_pasp_sery' => $osago->insurer_passport_series,
            'owner_pasp_num' => $osago->insurer_passport_number,
            'owner_surname' => $osago_fond_data->last_name_latin,
            'owner_name' => $osago_fond_data->first_name_latin,
            'owner_patronym' => $osago_fond_data->middle_name_latin,
            'owner_isdriver' => $osago->applicant_is_driver,
            'owner_oblast' => is_null($osago_fond_data->oblast) ? 10 : $osago_fond_data->oblast,
            'owner_rayon' => is_null($osago_fond_data->rayon) ? 1001 : $osago_fond_data->rayon,
            'has_benefit' => 1,
            'owner_inn' => $osago->insurer_inn,
            'owner_orgname' => $osago_fond_data->orgname,
            'applicant_isowner' => 1,
            'owner_phone' => $osago->user->phone,
            'driver_limit' => Osago::KAPITAL_SUGURTA_NUMBER_DRIVERS_ID[$osago->number_drivers_id],
            'prem' => $osago->amount_uzs,
            'opl_type' => 1,
            'contract_begin' => !empty($osago->begin_date) ? DateHelper::date_format($osago->begin_date, 'Y-m-d', 'd.m.Y') : date('d.m.Y'),
            'with_doc_ins' => (int)$osago->owner_with_accident,
        ];

        $drivers = [];
        if (!empty($osago->applicant_is_driver) and $osago->number_drivers_id != Osago::NO_LIMIT_NUMBER_DRIVERS_ID)
            $drivers[] = [
                'datebirth' => !empty($osago->insurer_birthday) ? date('d.m.Y', $osago->insurer_birthday) : null,
                'paspsery' => $osago->insurer_passport_series,
                'paspnumber' => $osago->insurer_passport_number,
                'pinfl' => $osago->insurer_pinfl,
                'surname' => $osago_fond_data->last_name_latin,
                'name' => $osago_fond_data->first_name_latin,
                'patronym' => $osago_fond_data->middle_name_latin,
                'licsery' => $osago->insurer_license_series,
                'licnumber' => $osago->insurer_license_number,
                'licdate' => DateHelper::date_format($osago->insurer_license_given_date, 'Y-m-d', 'd.m.Y'),
                'relative' => KapitalSugurtaRequest::RELATIVES[0],
                'resident' => 1,
            ];

        foreach ($osago->drivers as $driver) {

            $drivers[] = [
                'datebirth' => date('d.m.Y', $driver->birthday),
                'paspsery' => $driver->passport_series,
                'paspnumber' => $driver->passport_number,
                'pinfl' => $driver->pinfl,
                'surname' => $driver->last_name,
                'name' => $driver->first_name,
                'patronym' => $driver->middle_name,
                'licsery' => $driver->license_series,
                'licnumber' => $driver->license_number,
                'licdate' => DateHelper::date_format($driver->license_given_date, 'Y-m-d', 'd.m.Y'),
                'relative' => KapitalSugurtaRequest::RELATIVES[$driver->relationship_id],
                'resident' => 1,
            ];
        }

        $request_body = array_merge($request_body, ['drivers' => $drivers]);

        $anketa = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['create_osago'], $osago, $request_body, true, [], 'post');
        if (is_array($anketa) and array_key_exists('anketa_id', $anketa))
            return $anketa;

        return false;
    }

    public static function osagoConfirm(Osago $osago): array|false
    {
        $response_arr = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['get_anketa'], $osago, ['anketa_id' => $osago->order_id_in_gross], false, [], 'post');

        if ($response_arr['POLIS_STATUS'] == 2)
        {
            if ($accident = $osago->accident)
                Yii::$app->queue1->push(new CheckStatusKapitalAccidentJob(['accident_id' => $accident->id]));

            $osago->status = Osago::STATUS['received_policy'];
            $osago->payed_date = time();
        }
        if ($response_arr['POLIS_STATUS'] == 3)
            $osago->status = Osago::STATUS['canceled'];

        $osago->save();

        if (is_array($response_arr) and array_key_exists('UUID', $response_arr) and !empty($response_arr['UUID']))
            return [
                'policy_number' => $response_arr['POLIS_SERY'] . ' ' . $response_arr['POLIS_NUMBER'],
                'policy_pdf_url' => !is_null($response_arr['UUID']) ? 'http://polis.e-osgo.uz/site/export-to-pdf?id=' . $response_arr['UUID'] : "",
                'begin_date' => !empty($response_arr['CONTRACT_BEGIN']) ? DateHelper::date_format($response_arr['CONTRACT_BEGIN'], 'd.m.Y', 'Y-m-d') :  $osago->begin_date,
                'end_date' => !empty($response_arr['CONTRACT_END']) ? DateHelper::date_format($response_arr['CONTRACT_END'], 'd.m.Y', 'Y-m-d') : $osago->end_date,
            ];

        return false;
    }
}