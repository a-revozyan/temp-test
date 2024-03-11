<?php

namespace common\models;

use yii\base\Exception;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "bridge_company_request".
 *
 * @property int $id
 * @property int|null $bridge_company_id
 * @property string|null $url
 * @property string|null $request_body
 * @property string|null $response_body
 * @property string|null $send_date
 * @property int|null $taken_time
 * @property int|null $osago_id
 * @property int|null $accident_id
 */
class BridgeCompanyRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bridge_company_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bridge_company_id', 'taken_time', 'osago_id', 'accident_id'], 'default', 'value' => null],
            [['bridge_company_id', 'taken_time', 'osago_id', 'accident_id'], 'integer'],
            [['request_body', 'response_body'], 'string'],
            [['send_date'], 'safe'],
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
            'bridge_company_id' => 'Bridge Company ID',
            'url' => 'Url',
            'request_body' => 'Request Body',
            'response_body' => 'Response Body',
            'send_date' => 'Send Date',
            'taken_time' => 'Taken Time',
            'osago_id' => 'Osago ID',
            'accident_id' => 'Accident ID',
        ];
    }

    public static function sendRequest($url, $bridge_company, $product, $request_body = [], $throw_error = true, $method = 'POST')
    {
        $client = new Client();

        $headers = [
            'Authorization' => $bridge_company->authorization,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept'=> 'application/json'
        ];

        $start_time = microtime(true);
        try {
            $response = $client->$method($url, json_encode($request_body), $headers)->send();
            $response_array = (array)json_decode($response->getContent());
            $status = $response->getStatusCode();
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
            $status = 0;
        }

        self::create($bridge_company, $url, $request_body, $response_array, $product, $start_time);

        if ($throw_error and $status != 200)
            throw new BadRequestHttpException('error while to ping bridge company');

        return $response_array;
    }

    public static function create($bridge_company, $url, $request_body, $response_array, $model, $start_time = 0)
    {
        $product = str_replace("common\\models\\", "", get_class($model));

        $bridge_company_request = new BridgeCompanyRequest();
        $bridge_company_request->bridge_company_id = $bridge_company->id;
        $bridge_company_request->url = $url;
        $bridge_company_request->taken_time = floor(microtime(true) * 1000) - floor($start_time * 1000);
        $bridge_company_request->request_body = json_encode($request_body);
        $bridge_company_request->response_body = is_array($response_array) ? json_encode($response_array) : $response_array;

        $bridge_company_request->send_date = date('Y-m-d H:i:s');
        if ($product == "Osago")
            $bridge_company_request->osago_id = $model->id;
        elseif ($product == "Accident")
            $bridge_company_request->accident_id = $model->id;
        $bridge_company_request->save();
    }
}
