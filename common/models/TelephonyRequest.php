<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;
use yii\base\Exception;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "telephony_request".
 *
 * @property int $id
 * @property string|null $url
 * @property string|null $request
 * @property string|null $response
 * @property string|null $send_date
 * @property int|null $f_user_id
 * @property string|null $phone_number
 */
class TelephonyRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'telephony_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['send_date'], 'safe'],
            [['f_user_id'], 'default', 'value' => null],
            [['f_user_id'], 'integer'],
            [['request', 'phone_number', 'url'], 'string', 'max' => 255],
            [['response'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request' => 'Request',
            'response' => 'Response',
            'send_date' => 'Send Date',
            'f_user_id' => 'F User ID',
            'phone_number' => 'Phone Number',
        ];
    }

    const URLS = [
        'auth' => 'https://api2.onlinepbx.ru/pbx16880.onpbx.ru/auth.json',
        'call' => 'https://api2.onlinepbx.ru/pbx16880.onpbx.ru/call/now.json',
    ];

    public static function getAuthorization($phone_number)
    {
        $new = false;
        $last_request = self::find()
            ->where(['ilike', 'request', '%"new":true%', false])
            ->andWhere(['ilike', 'response', '%"status":"1"%', false])
            ->orderBy('send_date desc')
            ->one();

        if (empty($last_request)  or (int)(date_diff(date_create($last_request->send_date), date_create(date('Y-m-d H:i:s')))->format("%a")) >= 3)
            $new = true;

        $response = self::sendRequest(self::URLS['auth'], $phone_number, [
            "auth_key" => GeneralHelper::env('online_p_b_x_auth_key'),
            "new" => $new,
        ]);
        return $response['data']->key_id . ":" . $response['data']->key;
    }

    /**
     * @throws BadRequestHttpException
     */
    public static function sendRequest($url, $phone_number, $request_body)
    {
        $f_user_id = null;
        if ($user = User::findOne(['phone' => $phone_number]))
            $f_user_id = $user->id;

        $auth = "";
        if ($url != self::URLS['auth'])
            $auth = self::getAuthorization($phone_number);

        $client = new Client();

        try {
            $response = $client->post($url, json_encode($request_body), ['x-pbx-authentication' => $auth, 'Content-Type' => 'application/json'])->send();
            $response_array = (array)json_decode($response->getContent());
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
        }

        self::create($url, $request_body, $response_array, $f_user_id, $phone_number);

        if (is_array($response_array) and array_key_exists('status', $response_array) and !$response_array['status'])
            throw new BadRequestHttpException($response_array['comment']);
        self::throw_unexpected_error($response_array);

        return $response_array;
    }

    public static function throw_unexpected_error($response_array)
    {
        if (
            !is_array($response_array)
            or
            !array_key_exists('data', $response_array)
        )
            throw new BadRequestHttpException(Yii::t('app', "onlinePBX API siga murojaatda error"));
    }

    public static function create($url, $request_body, $response_array, $f_user_id, $phone_number)
    {
        $request = new TelephonyRequest();
        $request->url = $url;
        $request->request = json_encode($request_body);
        $request->response = is_array($response_array) ? json_encode($response_array) : $response_array;
        $request->send_date = date('Y-m-d H:i:s');
        $request->f_user_id = $f_user_id;
        $request->phone_number = $phone_number;
        $request->save();
    }

}
