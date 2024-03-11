<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;
use yii\base\Exception;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "kapital_sugurta_request".
 *
 * @property int $id
 * @property string|null $url
 * @property string|null $request_body
 * @property string|null $response_body
 * @property string|null $send_date
 * @property int|null $osago_id
 * @property int|null $travel_id
 * @property int|null $accident_id
 * @property string|null $token
 * @property integer|null $taken_time
 */
class KapitalSugurtaRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kapital_sugurta_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_body', 'response_body', 'token'], 'string'],
            [['send_date'], 'safe'],
            [['osago_id', 'travel_id', 'accident_id', 'taken_time'], 'default', 'value' => null],
            [['osago_id', 'travel_id', 'accident_id', 'taken_time'], 'integer'],
            [['url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'Url',
            'request_body' => 'Request Body',
            'response_body' => 'Response Body',
            'send_date' => 'Send Date',
            'osago_id' => 'Osago ID',
            'travel_id' => 'Travel ID',
            'accident_id' => 'Accident ID',
            'token' => 'Token',
        ];
    }

    const URLS = [
        'get_token' => 'https://api.ksc.uz/auth/login',
        'get_doc_types' => 'https://api.ksc.uz/product/accident/doc-type',
        'get_auto_info' => 'https://api.ksc.uz/osgo/provider/vehicle',
        'create_osago' => 'https://api.ksc.uz/osgo/create/create',
        'payment_osago' => 'https://api.ksc.uz/osgo/check/payment',
        'person_info_by_pinfl' => 'https://api.goodsign.biz/v1/profile/',
        'driver_info_by_pinfl' => 'https://api.ksc.uz/osgo/provider/driver-summary',
        'driver_license_info_by_pinfl' => 'https://api.ksc.uz/osgo/provider/driver-license',
        'driver_info_by_birthday_pass' => 'https://api.ksc.uz/osgo/provider/passport-birth-date',
        'calc' => 'https://api.ksc.uz/osgo/provider/calc-prem',
        'get_anketa' => 'https://api.ksc.uz/osgo/get/anketa',
        'payment_check' => 'https://api.ksc.uz/payments/check',

        'create_accident' => 'https://api.ksc.uz/product/accident/create',

        'payment_info' => 'https://api.ksc.uz/payments/merchant-info',

        'travel_countries' => 'https://api.ksc.uz/travel/reference/abroad-country',
        'travel_programs' => 'https://api.ksc.uz/travel/reference/abroad-program',
        'travel_calc_amount' => 'https://api.ksc.uz/travel/price/total',
        'travel_save' => 'https://api.ksc.uz/travel/save/create',
        'travel_member_info' => 'https://api.ksc.uz/travel/provider/passport-birth-date',
    ];

    const RELATIVES = [
        0 => 0,
        1 => 1, // o'zimizdagi id => kapitaldagi id
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

    const PERIOD = [
        1 => 2,
        2 => 1,
    ];

    const DRIVER_LIMIT = [
        1 => 0,
        4 => 1,
    ];

    const TRAVEL_PURPOSE = [
        1 => 0,
        2 => 3,
        3 => 2,
        4 => 1,
    ];

    const TRAVEL_GROUP = [
        'family' => 1,
        'individual' => 0,
    ];

    const TRAVEL_TYPE = [
        'one_time' => 0,
        'multi_time' => 1,
    ];

    public static function getAuthorization()
    {
        return 'Basic ' . base64_encode(GeneralHelper::env('kapital_sugurta_login') . ":" . GeneralHelper::env('kapital_sugurta_password'));
    }

    public function getOsago()
    {
        return $this->hasOne(Osago::className(), ['id' => 'osago_id']);
    }

    public static function sendRequest($url, $model, $request_body = [], $throw_error = true, $headers = [], $method = 'get')
    {
        $client = new Client();

        if (empty($headers))
        {
            $token = self::find()->where(['not', ['token' => null]])->orderBy('id desc')->one()->token ?? null;
            $headers = ['Authorization' =>  'Bearer ' . $token];
        }

        $start_time = microtime(true);
        try {
            $response = $client->$method($url, json_encode($request_body), array_merge($headers, ['Content-Type' => 'application/json', 'accept-language' => 'ru']))->send();
            $response_array = (array)json_decode($response->getContent());
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
        }

        self::create($url, $request_body, $response_array, $model, $start_time);

        if ($throw_error and is_array($response_array) and (
                (array_key_exists('result', $response_array) and !in_array($response_array['result'], [0, -1, true]))
                or
                (array_key_exists('ERROR', $response_array) and $response_array['ERROR'] != 0)
                or
                array_key_exists('error_ora', $response_array)
            )
        )
            self::throw_error($response_array, $url);

        if ($throw_error and (!is_array($response_array) or $response_array == [false]))
            throw new BadRequestHttpException(Yii::t('app', "kapital sugurta API siga murojaatda error"));

        if (is_array($response_array) and array_key_exists('result', $response_array) and $response_array['result'] == -1 and $url != self::URLS['get_token'])
        {
            self::sendRequest(self::URLS['get_token'], $model, $request_body, $throw_error, ['Authorization' => self::getAuthorization()]);
            $response_array = self::sendRequest($url, $model, $request_body, $throw_error);
        }

        return $response_array;
    }

    public static function sendTravelRequest($url, $model, $request_body = [], $throw_error = true, $headers = [], $method = 'get')
    {
        $client = new Client();

        if (!array_key_exists('Authorization', $headers))
        {
            $token = self::find()->where(['not', ['token' => null]])->orderBy('id desc')->one()->token ?? null;
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        $start_time = microtime(true);
        try {
            $response = $client->$method($url, json_encode($request_body), array_merge(['Content-Type' => 'application/json', 'accept-language' => 'ru'], $headers))->send();
            $response_array = (array)json_decode($response->getContent());
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
        }

        self::create($url, $request_body, $response_array, $model, $start_time);

//        if ($throw_error and is_array($response_array) and (
//                (array_key_exists('result', $response_array) and !$response_array['result'])
//                or
//                (array_key_exists('ERROR', $response_array) and $response_array['ERROR'] != 0)
//                or
//                array_key_exists('error_ora', $response_array)
//            )
//        )
//            self::throw_error($response_array, $url);

        if ($throw_error and !is_array($response_array))
            throw new BadRequestHttpException(Yii::t('app', "kapital sugurta API siga murojaatda error"));

        if (is_array($response_array) and array_key_exists('result', $response_array) and $response_array['result'] == -1 and $url != self::URLS['get_token'])
        {
            self::sendRequest(self::URLS['get_token'], $model, $request_body, $throw_error, ['Authorization' => self::getAuthorization()]);
            $response_array = self::sendRequest($url, $model, $request_body, $throw_error);
        }

        return $response_array;
    }

    public static function throw_error($response_array, $url)
    {
        $message = Yii::t('app', "kapital sugurta API siga murojaatda error");

        if (array_key_exists('message', $response_array))
            switch ($response_array['message'])
            {
                case 'Login or Password incorrect' :
                    $message = 'Login or Password incorrect';
                    break;
            }

        if (array_key_exists('ERROR_MESSAGE', $response_array))
            switch ($response_array['ERROR_MESSAGE'])
            {
                case 'You must submit valid form' :
                    $message = 'You must submit valid form';
                    break;
                case 'Данных по предоставленным параметрам не найдено' :
                    $message = 'Данных по предоставленным параметрам не найдено';
            }

        if (array_key_exists('ERROR', $response_array) and in_array($response_array['ERROR'], [404, -404]))
        {
            if ($url == self::URLS['get_auto_info'])
                $message = Yii::t('app', 'fond_not_found auto info');
            elseif ($url == self::URLS['driver_info_by_pinfl'])
                $message = Yii::t('app', 'fond_not_found owner info');
        }

        throw new BadRequestHttpException($message, 0);
    }

    public static function create($url, $request_body, $response_array, $model, $start_time = 0)
    {
        $product = str_replace("common\\models\\", "", get_class($model));

        $osago_request = new KapitalSugurtaRequest();
        $osago_request->url = $url;
        $osago_request->taken_time = floor(microtime(true) * 1000) - floor($start_time * 1000);
        $osago_request->request_body = json_encode($request_body);
        $osago_request->response_body = is_array($response_array) ? json_encode($response_array) : $response_array;
        if (is_array($response_array) and array_key_exists('response', $response_array) and $response_array['response']->token)
            $osago_request->token = $response_array['response']->token;

        $osago_request->send_date = date('Y-m-d H:i:s');
        if ($product == "Osago")
            $osago_request->osago_id = $model->id;
        elseif ($product == "Accident")
            $osago_request->accident_id = $model->id;
        elseif ($product == "Travel")
            $osago_request->travel_id = $model->id;
        $osago_request->save();
    }
}
