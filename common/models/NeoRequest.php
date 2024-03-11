<?php

namespace common\models;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use Yii;
use yii\base\Exception;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "agent".
 *
 * @property int $id
 * @property string|null $url
 * @property string|null $request_body
 * @property string|null $response_body
 * @property string|null $send_date
 * @property integer|null $osago_id
 * @property integer|null $accident_id
 * @property integer|null $taken_time
 * @property integer|null $kasko_by_subscription_policy_id
 */
class NeoRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'neo_request';
    }

    const URLS = [
        'save_osago_police_url' => 'https://api.neoinsurance.uz/api/osago-neo/save-policy/v2',
        'get_policy_data' => 'https://api.neoinsurance.uz/api/osago-neo/confirm-check',
        'confirm_policy' => 'https://api.neoinsurance.uz/api/osago-neo/confirm-policy',

        'save_accident_police_url' => 'https://api.neoinsurance.uz/api/accident_neo/save/v2',
        'get_policy_data_accident' => 'https://api.neoinsurance.uz/api/accident_neo/check',
        'confirm_accident_policy' => 'https://api.neoinsurance.uz/api/accident_neo/generate',

        'kasko_by_subscription_check_car' => 'https://api.neoinsurance.uz/api/minikasko_neo/checkCar',
        'kasko_by_subscription_save' => 'https://api.neoinsurance.uz/api/minikasko_neo/saqlash',
        'kasko_by_subscription_confirm' => 'https://api.neoinsurance.uz/api/minikasko_neo/confirmPolicy',
    ];

    public static function getAuthorization()
    {
        return 'Basic ' . base64_encode(GeneralHelper::env('neo_login') . ":" . GeneralHelper::env('neo_password'));
    }

    const RELATIVES = [
        0 => 0,
        1 => 1, // o'zimizdagi id => neo id
        4 => 4,
        9 => 9,
        10 => 10,
        2 => 7,
        3 => 8,
        5 => 2,
        6 => 3,
        7 => 5,
        8 => 6,
        '' => null,
    ];


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'request_body', 'response_body', 'send_date'], 'string'],
            [['osago_id', 'taken_time', 'accident_id'], 'integer']
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

    public static function sendOsagoRequest($url, $osago, $request_body = [], $throw_error = true)
    {
        $client = new Client();

        $start_time = microtime(true);
        try {
            $response = $client->post($url, json_encode($request_body), ['Authorization' => self::getAuthorization(), 'Content-Type' => 'application/json'])->send();
            $response_array = (array)json_decode($response->getContent());
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
        }

        self::create($url, $request_body, $response_array, $osago->id, null, null, $start_time);
        if ($throw_error and is_array($response_array) and array_key_exists('result', $response_array) and !$response_array['result'])
            self::throw_error($response_array['error'] ?? 0, $response_array['message'], $response_array);

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

        self::create($url, $request_body, $response_array, null, $accident->id, null, $start_time);
        return $response_array;
    }

    public static function sendKaskoBySubscriptionPolicyRequest($url, $kasko_by_subscription_policy, $request_body, $method = 'post', $throw_error = true)
    {
        $client = new Client();

        $start_time = microtime(true);

        try {
            $response = $client->$method($url, json_encode($request_body), ['Authorization' => self::getAuthorization(), 'Content-Type' => 'application/json'])->send();
            $response_array = (array)json_decode($response->getContent());
            $status_code = $response->getStatusCode();
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
            $status_code = -1;
        }

        self::create($url, $request_body, $response_array, null, null, $kasko_by_subscription_policy->id, $start_time);
        if ($throw_error and is_array($response_array) and array_key_exists('error', $response_array) and $response_array['error'] != 0)
            self::throw_kasko_by_subscription_error($response_array['error'] ?? -99999, $response_array['message']);

        self::throw_unexpected_kasko_by_subscription_error($throw_error, $response_array, $status_code);

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
            throw new BadRequestHttpException(Yii::t('app', "NEO API siga murojaatda error"));
    }

    public static function throw_unexpected_kasko_by_subscription_error($throw_error, $response_array, $status_code)
    {
        if (
            $throw_error
            and
            (
                $status_code != 200
                or
                !is_array($response_array)
                or
                (
                    array_key_exists('result', $response_array)
                    and
                    !$response_array['result']
                )
            )
        )
            throw new BadRequestHttpException(Yii::t('app', "NEO API siga murojaatda error"));
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
        if (Yii::$app instanceof \yii\console\Application)
            throw new BadRequestHttpException($message, $error_code);
    }

    public static function throw_kasko_by_subscription_error($error_code, $default)
    {
        $message = $default;
        switch ($error_code)
        {
            case 1:
                $message = Yii::t('app', 'fond_not_found auto info');
                break;
            case 9:
                $message = Yii::t('app', 'fond not found applicant pass data');
                break;
        }

        throw new BadRequestHttpException($message, $error_code);
    }


    public static function create($url, $request_body, $response_array, $osago_id, $accident_id, $kasko_by_subscription_policy_id, $start_time = 0)
    {
        $osago_request = new NeoRequest();
        $osago_request->url = $url;
        $osago_request->request_body = json_encode($request_body);
        $osago_request->response_body = is_array($response_array) ? json_encode($response_array) : $response_array;
        $osago_request->send_date = date('Y-m-d H:i:s');
        $osago_request->osago_id = $osago_id;
        $osago_request->accident_id = $accident_id;
        $osago_request->kasko_by_subscription_policy_id = $kasko_by_subscription_policy_id;
        $osago_request->taken_time = floor(microtime(true) * 1000) - floor($start_time * 1000);
        $osago_request->save();
    }
}
