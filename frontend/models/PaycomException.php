<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class PaycomException extends Model
{
    const ERROR_INTERNAL_SYSTEM         = -32400;
    const ERROR_INSUFFICIENT_PRIVILEGE  = -32504;
    const ERROR_INVALID_JSON_RPC_OBJECT = -32600;
    const ERROR_METHOD_NOT_FOUND        = -32601;
    const ERROR_INVALID_AMOUNT          = -31001;
    const ERROR_TRANSACTION_NOT_FOUND   = -31003;
    const ERROR_INVALID_ACCOUNT         = -31050;
    const ERROR_COULD_NOT_CANCEL        = -31007;
    const ERROR_COULD_NOT_PERFORM       = -31008;

    public $request_id;
    public $error;
    public $code;
    public $data;

    public static function response($request_id, $message, $code, $data = null)
    {
        // prepare error data
        $error = ['code' => $code];
        $error['message'] = $message;
        $error['data'] = $data;

        return [
          "error" => $error,
          "id" => $request_id
        ];
    }

    public function send()
    {
        header('Content-Type: application/json; charset=UTF-8');

        // create response
        $response['id']     = $this->request_id;
        $response['result'] = null;
        $response['error']  = $this->error;

        echo json_encode($response);
    }

    public static function message($ru, $uz = '', $en = '')
    {
        return ['ru' => $ru, 'uz' => $uz, 'en' => $en];
    }
}