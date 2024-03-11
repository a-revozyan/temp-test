<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;
use yii\base\Exception;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "insonline_request".
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
class InsonlineRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'insonline_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_body', 'response_body', 'token'], 'string'],
            [['send_date'], 'safe'],
            [['osago_id', 'accident_id', 'taken_time'], 'default', 'value' => null],
            [['osago_id', 'accident_id', 'taken_time'], 'integer'],
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
            'accident_id' => 'Accident ID',
            'token' => 'Token',
        ];
    }

    const URLS = [
        'get_token' => 'https://api.insonline.uz/auth/login',
        'create_osago' => 'https://api.insonline.uz/osgo/create/create',
        'get_anketa' => 'https://api.insonline.uz/osgo/check/payment',
        'payment_osago' => 'https://api-payment.insonline.uz/api/v1/payment/osago/policy',

        'get_auto_info' => 'https://api.insonline.uz/osgo/provider/vehicle',
        'driver_info_by_pinfl' => 'https://api.insonline.uz/osgo/provider/driver-summary',
        'driver_license_info_by_pinfl' => 'https://api.insonline.uz/osgo/provider/driver-license',
        'driver_info_by_birthday_pass' => 'https://api.insonline.uz/osgo/provider/passport-birth-date',
        'calc' => 'https://api.insonline.uz/osgo/provider/calc-prem',
    ];

    public static function getAuthorization()
    {
        return 'Basic ' . base64_encode(GeneralHelper::env('insonline_login') . ":" . GeneralHelper::env('insonline_password'));
    }

    public function getOsago()
    {
        return $this->hasOne(Osago::className(), ['id' => 'osago_id']);
    }

    public static function sendRequest($url, $model, $request_body = [], $throw_error = true, $headers = [], $method = 'get')
    {
        $client = new Client();

        if ($url == self::URLS['payment_osago']){
            $headers = ['Authorization' =>  self::getAuthorization()];
        } elseif (empty($headers))
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
                or in_array("false", $response_array)
            )
        )
            self::throw_error($response_array, $url);

        if ($throw_error and !is_array($response_array))
            throw new BadRequestHttpException(Yii::t('app', "insonline sugurta API siga murojaatda error"));

        if (is_array($response_array) and array_key_exists('result', $response_array) and $response_array['result'] == -1 and $url != self::URLS['get_token'])
        {
            self::sendRequest(self::URLS['get_token'], $model, $request_body, $throw_error, ['Authorization' => self::getAuthorization()]);
            $response_array = self::sendRequest($url, $model, $request_body, $throw_error);
        }

        return $response_array;
    }

    public static function throw_error($response_array, $url)
    {
        $message = Yii::t('app', "insonline sugurta API siga murojaatda error");

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
                    $message = Yii::t('app', 'Данных по предоставленным параметрам не найдено');
                    break;
                case 'У провайдера данных случилась непредвиденная ошибка' :
                    $message = Yii::t('app', 'У провайдера данных случилась непредвиденная ошибка');
                    break;
            }

        if (array_key_exists('ERROR', $response_array) and in_array($response_array['ERROR'], [404, -404]))
        {
            if ($url == self::URLS['get_auto_info'])
                $message = Yii::t('app', 'fond_not_found auto info');
            elseif ($url == self::URLS['driver_info_by_pinfl'])
                $message = Yii::t('app', 'fond_not_found owner info');
        }

        if (array_key_exists("result", $response_array) and $response_array['result'] != 0)
            $message = $response_array['result_message'] ?? "";

        throw new BadRequestHttpException($message, 0);
    }

    public static function create($url, $request_body, $response_array, $model, $start_time = 0)
    {
        $product = str_replace("common\\models\\", "", get_class($model));

        $osago_request = new InsonlineRequest();
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
        $osago_request->save();
    }
}
