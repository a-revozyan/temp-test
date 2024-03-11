<?php

namespace common\models;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use Yii;
use yii\base\Exception;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

/**
 * This is the model class for table "agent".
 *
 * @property int $id
 * @property string|null $url
 * @property string|null $request_body
 * @property string|null $response_body
 * @property integer|null $send_date
 * @property integer|null $osago_id
 * @property integer|null $accident_id
 * @property integer|null $kasko_by_subscription_policy_id
 * @property integer|null $travel_id
 * @property integer|null $taken_time
 */
class OsagoRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'osago_requestes';
    }

    const URLS = [
        'check_osago_data_url' => 'https://gross.uz/ru/osago-gross/generate-policy',
        'save_osago_police_url' => 'https://gross.uz/ru/osago-gross/save-policy',
        'get_policy_data' => 'https://gross.uz/ru/osago-gross/get-policy-status',
        'get_policy_calc' => 'https://gross.uz/ru/osago-gross/get-calc-osago',
        'confirm_policy' => 'https://gross.uz/ru/osago-gross/confirm-policy',
        'is_juridic' => 'https://gross.uz/ru/osago-gross/osago-juridik',

        'health_calc_sb' => 'https://gross.uz/ru/accident-gross/health-calc-sb',
        'health_save_sb' => 'https://gross.uz/accident-gross/health-save-sb',
        'health_payment' => 'https://gross.uz/accident-gross/payment',
        'health_get_policy_pdf' => 'https://gross.uz/ru/accident-gross/policy-pdf',

        'kasko_by_subscription_programs' => 'https://gross.uz/ru/avto-limit-gross/programs',
        'kasko_by_subscription_calc' => 'https://gross.uz/ru/avto-limit-gross/calc',
        'kasko_by_subscription_save' => 'https://gross.uz/ru/avto-limit-gross/save',
        'kasko_by_subscription_confirm' => 'https://gross.uz/ru/avto-limit-gross/confirm',

        'travel_countries' => 'https://gross.uz/travelapi/country',
        'travel_programs' => 'https://gross.uz/travelapi/get-programs',
        'travel_purposes' => 'https://gross.uz/travelapi/purpose',
        'travel_calc_amount' => 'https://gross.uz/travelapi/calc-amount',
        'travel_calc_amount_by_multiple_program' => 'https://gross.uz/travelapi/calc-amount-sb',
        'travel_save_policy' => 'https://gross.uz/travelapi/save-polis',
        'travel_cance_policy' => 'https://gross.uz/travelapi/cancel-policy',
    ];

    public static function getAuthorization()
    {
        return 'Basic ' . base64_encode(GeneralHelper::env('gross_login') . ":" . GeneralHelper::env('gross_password1'));
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'request_body', 'response_body'], 'string'],
            [['osago_id', 'send_date', 'accident_id', 'taken_time'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'osago_id' => 'Osago Id',
            'request_body' => 'request_body',
            'response_body' => 'response_body',
            'url' => 'url',
            'send_date' => 'send_date',
        ];
    }

    public function getOsago()
    {
        return $this->hasOne(Osago::className(), ['id' => 'osago_id']);
    }

    public static function sendTravelRequest($url, $travel, $request_body, $throw_error = true)
    {
        $client = new Client();
        $start_time = microtime(true);

        try {
            $response = $client->post($url, json_encode($request_body), ['Authorization' => self::getAuthorization(), 'Content-Type' => 'application/json'])->send();
            $response_array = (array)json_decode($response->getContent());
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
        }

        self::create($url, $request_body, $response_array, null, null, null, $travel->id, $start_time);

        if ($throw_error and is_array($response_array) and array_key_exists('result', $response_array) and !$response_array['result'])
            self::throw_travel_error($response_array['error'] ?? 0, $response_array['message']);
        self::throw_unexpected_error($throw_error, $response_array);

        return $response_array;
    }


    public static function sendKaskoBySubscriptionPolicyRequest($url, $kasko_by_subscription_policy, $request_body, $throw_error = true)
    {
        $client = new Client();

        $method = "post";
        if ($url == self::URLS['kasko_by_subscription_programs'])
            $method = "get";

        $start_time = microtime(true);

        try {
            $response = $client->$method($url, json_encode($request_body), ['Authorization' => self::getAuthorization(), 'Content-Type' => 'application/json'])->send();
            $response_array = (array)json_decode($response->getContent());
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
        }

        self::create($url, $request_body, $response_array, null, null, $kasko_by_subscription_policy->id, null, $start_time);
        if ($throw_error and is_array($response_array) and array_key_exists('result', $response_array) and !$response_array['result'])
             self::throw_kasko_by_subscription_error($response_array['error'] ?? 0, $response_array['message']);

        self::throw_unexpected_error($throw_error, $response_array);

        return $response_array;
    }

    public static function sendAccidentRequest($url, $accident, $request_body)
    {
        $client = new Client();
        $start_time = microtime(true);

        try {
            $response = $client->post($url, json_encode($request_body), ['Authorization' => self::getAuthorization(), 'Content-Type' => 'application/json'])->send();
            $response_array = (array)json_decode($response->getContent());
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
        }

        self::create($url, $request_body, $response_array, null, $accident->id, null, null, $start_time);
        return $response_array;
    }

    public static function sendRequest($url, $osago, $request_body = [], $throw_error = true)
    {
        $client = new Client();

        $start_time = microtime(true);
        try {
            $response = $client->post($url, json_encode($request_body), ['Authorization' => self::getAuthorization(), 'Content-Type' => 'application/json'])->send();
            $response_array = (array)json_decode($response->getContent());
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
        }

        self::create($url, $request_body, $response_array, $osago->id, null, null, null, $start_time);
        if ($throw_error and is_array($response_array) and array_key_exists('result', $response_array) and !$response_array['result'])
            return self::throw_error($response_array['error'] ?? 0, $response_array['message'], $response_array);

        self::throw_unexpected_error($throw_error, $response_array);

        return $response_array;
    }

    public static function throw_unexpected_error($throw_error, $response_array)
    {
        if (
            $throw_error
            and
            (
                !is_array($response_array)
                or
                !array_key_exists('result', $response_array)
            )
        )
            throw new BadRequestHttpException(Yii::t('app', "gross API siga murojaatda error"));
    }

    public static function throw_error($error_code, $default, $response_array = [])
    {
        $message = $default;
        switch ($error_code)
        {
            case 1 :
                $message = Yii::t('app', 'fond_not_found auto info');
                break;
            case 2 :
                $message = Yii::t('app', 'fond_not_found inn');
                break;
            case 3 :
                $message = Yii::t('app', 'fond_not_found applicant info');
                break;
            case 4 :
                $key = $response_array['response']->key;
                $message = Yii::t('app', 'fond_not_found driver info {driver_key}', ['driver_key' => $key]);
                break;
            case 5 :
                $message = Yii::t('app', 'driver licenseNumber and licenseSeria not found in FOND');
                break;
            case 6 :
                $message = Yii::t('app', 'number_drivers_id is incorrect for Tashkent');
                break;
            case 7 :
                $message = Yii::t('app', 'juridic person does not add limited drivers');
                break;
            case 8 :
                $message = Yii::t('app', 'applicant driver licenseNumber and licenseSeria not found in FOND');
                break;
            case 9 :
                $message = Yii::t('app', 'owner passportSeria, passportNumber and birthday not found');
                break;
            case -9 :
                $message = Yii::t('app', 'fond_not_found owner info');
                break;
            case 10 :
                $message = Yii::t('app', 'owner pinfl do not match');
                break;
        }
        if (Yii::$app instanceof \yii\console\Application or  !in_array($error_code, [9, 10]))
            throw new BadRequestHttpException($message, $error_code);
    }

    public static function throw_kasko_by_subscription_error($error_code, $default)
    {
        $message = $default;
        switch ($error_code)
        {
            case 11 :
                $message = Yii::t('app', 'fond_not_found auto info');
                break;
            case 12 :
                $message = Yii::t('app', 'techpass owner data pinfl && passport_series && passport_number empty');
                break;
            case 16 :
                $message = Yii::t('app', 'fond not found applicant pass data');
                break;
            case 17 :
                $message = Yii::t('app', 'Auto-limit is not sold to legal entities');
                break;
            case 20 :
                $message = Yii::t('app', 'gross API siga murojaatda error');
                break;
            case 3 :
                $message = Yii::t('app', 'Incorrect program ID');
                break;
        }

        if (!in_array($error_code, [100]))
            throw new BadRequestHttpException($message, $error_code);
    }

    public static function throw_travel_error($error_code, $default)
    {
        $message = $default;
        switch ($error_code)
        {
            case 16 :
                $message = Yii::t('app', 'Period must be from 1 to 365 days');
                break;
            case 17 :
                $message = Yii::t('app', 'Max age 65 years for Family travel');
                break;
            case 18 :
                $message = Yii::t('app', 'The number of family members should be at least 3 people');
                break;
            case 19 :
                $message = Yii::t('app', 'Traveller save error');
                break;
            case 20 :
                $message = Yii::t('app', 'Policy not generated. Please try later');
                break;
        }

        throw new BadRequestHttpException($message, $error_code);
    }

    public static function create($url, $request_body, $response_array, $osago_id, $accident_id = null, $kasko_by_subscription_policy_id = null, $travel_id = null, $start_time = 0)
    {
        $osago_request = new OsagoRequest();
        $osago_request->url = $url;
        $osago_request->request_body = json_encode($request_body);
        $osago_request->response_body = is_array($response_array) ? json_encode($response_array) : $response_array;
        $osago_request->send_date = time();
        $osago_request->osago_id = $osago_id;
        $osago_request->accident_id = $accident_id;
        $osago_request->kasko_by_subscription_policy_id = $kasko_by_subscription_policy_id;
        $osago_request->travel_id = $travel_id;
        $osago_request->taken_time = floor(microtime(true) * 1000) - floor($start_time * 1000);
        $osago_request->save();
    }
}
