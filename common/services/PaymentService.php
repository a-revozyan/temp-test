<?php

namespace common\services;


use common\helpers\GeneralHelper;
use common\models\ClickRequest;
use common\models\HamkorpayRequest;
use common\models\Kasko;
use common\models\PaymeSubscribeRequest;
use common\models\ZoodpayRequest;
use Yii;
use yii\base\Exception;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;

class PaymentService
{
    public const PAYMENT_TYPE = [
        'click' => 'click',
        'payme' => 'payme',
        'zoodpay' => 'zoodpay',
        'payze' => 'payze',
        'hamkorpay' => 'hamkorpay',
    ];

    /**
     * @param Kasko $kasko
     * @return void
     */
    public static function cancel($model_class, $model_id)
   {
       $order = $model_class::findOne($model_id);
       if ($trans = $order->trans)
       {
           $payment_type = $trans->payment_type;
           $method_cancel = $payment_type . "_cancel";
           self::$method_cancel($trans, $model_id, $model_class);
       }
   }

    public static function click_cancel($trans, $model_id, $model_class)
    {
        $client = new Client();

        $payment_id = $trans->trans_no;
        $timestamp = time();
        $digest = sha1($timestamp . GeneralHelper::env('click_secret_key'));

        try {
            $response = $client->createRequest()
                ->setFormat(Client::FORMAT_JSON)
                ->setMethod('DELETE')
                ->setUrl("https://api.click.uz/v2/merchant/payment/reversal/". GeneralHelper::env('click_service_id') ."/$payment_id")
                ->addHeaders(['Auth' => GeneralHelper::env('click_merchant_user_id').":$digest:$timestamp", "content-type" => "application/json"])
                ->send();
            $response_array = (array)json_decode($response->getContent());
        }catch (Exception $exception){
            $response_array = $exception->getMessage();
        }

        ClickRequest::create(
            "https://api.click.uz/v2/merchant/payment/reversal/". GeneralHelper::env('click_service_id') ."/$payment_id",
            [],
            is_array($response_array) ? json_encode($response_array) : $response_array,
            $model_id,
            $model_class
        );

        if (is_array($response_array) and array_key_exists('error_code', $response_array) and $response_array['error_code'] != 0)
            throw new BadRequestHttpException($response_array['error_note']);

        if (is_array($response_array) and !array_key_exists('error_code', $response_array))
            throw new BadRequestHttpException(json_encode($response_array));

        if (!is_array($response_array))
            throw new BadRequestHttpException($response_array);

        return $response_array;
    }

    public static function zoodpay_cancel($trans, $model_id, $model_class)
    {
        ZoodpayRequest::sendRequest(ZoodpayRequest::REQUEST['refund'], $model_id, $model_class);
    }

    public static function payme_cancel($trans, $model_id, $model_class)
    {
        PaymeSubscribeRequest::sendRequest(
            PaymeSubscribeRequest::METHODS['receipt_cancel'],
            ['id' => $trans->trans_no],
            $model_class,
            $model_id,
        );
    }

    public static function payze_cancel($trans, $model_id, $model_class)
    {

    }

    public static function hamkorpay_cancel($trans, $model_id, $model_class)
    {
        $order = $model_class::findOne($model_id);
        HamkorpayRequest::sendRequest('pay.cancel', $order, [
            'pay_id' => $trans->trans_no,
        ]);
    }
}