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
 * @property string|null $model_class
 * @property int|null $model_id
 * @property string|null $token
 * @property integer|null $taken_time
 */
class HamkorpayRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hamkorpay_request';
    }

    public const PAY_STATUS = [
        'created' => 1,
        'held' => 2,
        'confirmed' => 3,
        'canceled' => 4,
        'partial_returned' => 5,
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_body', 'response_body', 'token', 'model_class'], 'string'],
            [['send_date'], 'safe'],
            [['model_id'], 'default', 'value' => null],
            [['model_id'], 'integer'],
            [['url'], 'string', 'max' => 255],
        ];
    }

    public static function getAuthorization()
    {
        return 'Basic ' . base64_encode(GeneralHelper::env('hamkorpay_key') . ":" . GeneralHelper::env('hamkorpay_secret'));
    }

    public static function sendRequest($rpc_method, $model, $initial_request_body = [], $headers = [], $method = 'post')
    {
        $client = new Client();

        if (empty($headers))
        {
            $token = self::find()->where(['not', ['token' => null]])->orderBy('id desc')->one()->token ?? null;
            $headers = ['Authorization' =>  'Bearer ' . $token];
        }

        $url = GeneralHelper::env('hamkorpay_url') . "/token";
        if (!is_null($rpc_method))
        {
            $request_body =  [
                'jsonrpc' => '2.0',
                'method' => $rpc_method,
                'id' => 'sb_request',
                'params' => [
                    $initial_request_body
                ]
            ];
            $url = GeneralHelper::env('hamkorpay_url') . "/acquiring/v1";
        }else
            $request_body = $initial_request_body;

        try {
            $response = $client->$method(
                $url,
                json_encode($request_body),
                array_merge($headers, ['Content-Type' => 'application/json', 'Accept' => 'application/json']),
                [
                    'sslCafile' => Yii::getAlias('@common') . "/config/certificates/sugurtabozor.crt",
                    'sslLocalCert' => Yii::getAlias('@common') . "/config/certificates/sugurtabozor.crt",
                    'sslPassphrase' => GeneralHelper::env('hamkorpay_certificate_password'),
                    'sslLocalPk' => Yii::getAlias('@common') . "/config/certificates/user1.key",
                ]
            )->send();

            $response_array = (array)json_decode($response->getContent());
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
        }

        self::create($url, $request_body, $response_array, $model);

        if (is_array($response_array) and array_key_exists('error_code', $response_array) and in_array($response_array['error_code'], [900901, 900902]) and !is_null($rpc_method))
        {
            self::sendRequest(null, $model, ['grant_type' => 'client_credentials'], ['Authorization' => self::getAuthorization()]);
            $response_array = self::sendRequest($rpc_method, $model, $initial_request_body, [], $method);
        }

        if (is_array($response_array) and array_key_exists('error', $response_array))
            self::throw_error($response_array['error']->code);

        if (!is_array($response_array))
            throw new BadRequestHttpException(Yii::t('app', "Hamkorpayda kutilmagan error"));

        return $response_array;
    }

    public static function throw_error($code)
    {
        switch ($code)
        {
            case 1000:
                $message = Yii::t('app', 'Произошла ошибка в хамкорпай. Пожалуйста, попробуйте позже.');
                break;
            case 1001:
                $message = Yii::t('app', 'На вашей карте недостаточно средств');
                break;
            case 1002:
                $message = Yii::t('app', 'Карта не найдена');
                break;
            case 1003:
                $message = Yii::t('app', 'Недостаточно прав для осуществление этой операции');
                break;
            case 1004:
                $message = Yii::t('app', 'Функция отправки SMS-кода не работает. Пожалуйста попробуйте позже');
                break;
            case 1005:
                $message = Yii::t('app', 'Мерчанд не найден.');
                break;
            case 1006:
                $message = Yii::t('app', 'Мерчанд-терминал не найден.');
                break;
            case 1007:
                $message = Yii::t('app', 'Не найдена установленная комиссия для мерчанда');
                break;
            case 1008:
                $message = Yii::t('app', 'Введенный SMS-код не правильный');
                break;
            case 1009:
                $message = Yii::t('app', 'Hold метод не работает');
                break;
            case 1010:
                $message = Yii::t('app', 'Функция подтверждение не работает. Пожалуйста попробуйте позже');
                break;
            case 1011:
            case 1012:
            case 1014:
                $message = Yii::t('app', 'Предоставленная информация не полная. Пожалуйста, заполните все поля');
                break;
            case 1013:
                $message = Yii::t('app', 'Не известный метод.');
                break;
            case 1015:
                $message = Yii::t('app', 'Рayment id не найдена');
                break;
            default :
                $message = Yii::t('app', 'Hamkorpayda kutilmagan error');
                break;
        }

        throw new BadRequestHttpException($message, 0);
    }

    public static function create($url, $request_body, $response_array, $model)
    {
        $model_class = get_class($model);

        $hamkorpay_request = new self();
        $hamkorpay_request->url = $url;
        $hamkorpay_request->request_body = json_encode($request_body);
        $hamkorpay_request->response_body = is_array($response_array) ? json_encode($response_array) : $response_array;
        if (is_array($response_array) and array_key_exists('access_token', $response_array))
            $hamkorpay_request->token = $response_array['access_token'];

        $hamkorpay_request->send_date = date('Y-m-d H:i:s');
        $hamkorpay_request->model_class = $model_class;
        $hamkorpay_request->model_id = $model->id;
        $hamkorpay_request->save();
    }
}
