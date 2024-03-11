<?php

namespace common\models;

use common\helpers\GeneralHelper;
use common\services\TelegramService;
use frontend\controllers\PaymeController;
use Yii;
use yii\base\Exception;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "payme_subscribe_request".
 *
 * @property int $id
 * @property string|null $url
 * @property string|null $request_body
 * @property string|null $response_body
 * @property string|null $send_date
 * @property int|null $model_id
 * @property string|null $model_class
 */
class PaymeSubscribeRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payme_subscribe_request';
    }

    const METHODS = [
        'card_create' => 'cards.create',
        'send_sms_code' => 'cards.get_verify_code',
        'check_sms_code' => 'cards.verify',
        'get_card' => 'cards.check',
        'receipt_create' => 'receipts.create',
        'receipt_pay' => 'receipts.pay',
        'receipt_cancel' => 'receipts.cancel',
    ];

    public const STATE = [
        'creating_check' => 0,
        'creating_transaction_in_merchants_billing' => 1,
        'wthdrawing_money_from_card' => 2,
        'closing_transaction_in_merchants_billing' => 3,
        'check_paid' => 4,
        'check_is_held' => 5,
        'check_is_held_long_time_ago' => 6,
        'check_is_paused_for_manual_intervention' => 20,
        'check_is_in_queue_for_cancel' => 21,
        'check_is_in_queue_for_closing_in_merchant_billing' => 21,
        'check_is_canceled' => 50,
    ];

    public static function getAuthorizationId()
    {
        return GeneralHelper::env('payme_merchant_id');
    }

    public static function getAuthorizationIdKey()
    {
        return GeneralHelper::env('payme_merchant_id') . ":" . GeneralHelper::env('payme_password');
    }

    public static function sendRequest($method, $request_body, $model_class, $model_id, $throw_error = true)
    {
        $client = new Client();

        $request_body = [
            'id' => 1,
            'method' => $method,
            'params' => $request_body
        ];
        $authorization = self::getAuthorizationId();
        if (in_array($method, [self::METHODS['receipt_pay'], self::METHODS['receipt_cancel']]))
            $authorization = self::getAuthorizationIdKey();
        try {
            $response = $client->post(GeneralHelper::env('payme_url') . "/api", json_encode($request_body), ['X-Auth' => $authorization, 'Content-Type' => 'application/json'])->send();
            $response_array = (array)json_decode($response->getContent());
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
        }

        self::create(GeneralHelper::env('payme_url') . "/api", $request_body, $response_array, $model_id, $model_class);
        if ($throw_error and is_array($response_array) and array_key_exists('error', $response_array))
            return self::throw_error($response_array['error']);

        if ($throw_error and !is_array($response_array))
            throw new BadRequestHttpException($response_array);

        $method = array_flip(self::METHODS)[$method];
        return self::$method($response_array['result'], $model_class, $model_id, $throw_error, $request_body['params']);
    }

    public static function card_create($result, $model_class, $model_id, $throw_error, $request_body)
    {
        $card = $result->card;
        $saved_card = new SavedCard();
        $saved_card->card_id = trim($card->token);
        $saved_card->card_mask = $card->number;
        $saved_card->f_user_id = \Yii::$app->user->id;
        $saved_card->status = SavedCard::STATUS['created'];
        $saved_card->payment_type = SavedCard::PAYMENT_TYPE['payme'];
        $saved_card->created_at = time();
        $saved_card->save();

        return self::sendRequest(self::METHODS['send_sms_code'], ['token' => $saved_card->card_id], $model_class, $model_id, $throw_error);
    }

    public static function send_sms_code($result, $model_class, $model_id, $throw_error, $request_body)
    {
        $saved_card = SavedCard::findOne(['card_id' => $request_body['token']]);
        return [
            'sent' => $result->sent,
            'phone' => $result->phone,
            'wait' => $result->wait,
            'saved_card_id' => $saved_card->id,
        ];
    }

    public static function check_sms_code($result, $model_class, $model_id, $throw_error, $request_body)
    {
        $card = $result->card;
        if (!$card->verify)
            return throw new BadRequestHttpException(Yii::t('app', 'Verification code is incorrect'));

        $saved_card = SavedCard::findOne(['card_id' => $card->token]);
        $saved_card->status = $card->recurrent ? SavedCard::STATUS['saved'] : SavedCard::STATUS['verified'];
        $saved_card->save();
    }

    public static function receipt_create($result, $model_class, $model_id, $throw_error, $request_body)
    {
        $receipt = $result->receipt;
        return $receipt->_id;
//        $order = $model_class::findOne($model_id);
//        $receipt = $result->receipt;
//
//        $transaction = new Transaction();
//        $transaction->partner_id = $order->partner_id;
//        $transaction->trans_no = $receipt->_id;
//        $transaction->amount = $receipt->amount/100;
//        $transaction->create_time = $receipt->create_time;
//        $transaction->trans_date = date('Y-m-d');
//        $transaction->perform_time = 0;
//        $transaction->cancel_time = 0;
//        $transaction->status = self::STATE['creating_check'];
//        $transaction->payment_type = 'payme';
//        $transaction->save();
//
//        $order->trans_id = $transaction->id;
//        $order->save();
//
//        return $transaction->trans_no;
    }

    public static function receipt_pay($result, $model_class, $model_id, $throw_error, $request_body)
    {
        return $result;
//        $order = $model_class::findOne($model_id);
//        $receipt = $result->receipt;
//
//        $transaction = Transaction::findOne(['trans_no' => $receipt->_id]);
//        $transaction->status = $receipt->state;
//        $transaction->perform_time = $receipt->pay_time;
//        $transaction->cancel_time = $receipt->cancel_time;
//        $transaction->save();
//
//        if ($receipt->state == self::STATE['check_paid'])
//        {
//            $order->saveAfterPayed();
//            TelegramService::send($order);
//        }
//
//        return $order;
    }

    public static function receipt_cancel($result, $model_class, $model_id, $throw_error, $request_body)
    {

    }

    public static function throw_error($error)
    {
        $message = $error->message;
        switch ($error->code)
        {
            case -31400 :
                $message = Yii::t('app', 'card is not found');
                break;
            case -31103 :
                $message = Yii::t('app', 'Введён неверный одноразовый СМС-код.');
                break;
            case -31002 :
                $message = Yii::t('app', 'Сервер процессингового центра недоступен.');
                break;
            case -31302 :
                $message = Yii::t('app', 'Невозможно получить баланс карты. Попробуйте позже.');
                break;
            case -31900 :
                $message = Yii::t('app', 'Данный тип карты не обслуживается.');
                break;
        }

        throw new BadRequestHttpException($message, $error->code);
    }

    public static function create($url, $request_body, $response_array, $model_id, $model_class)
    {
        $payme_subscribe_request = new PaymeSubscribeRequest();
        $payme_subscribe_request->url = $url;
        $payme_subscribe_request->request_body = json_encode($request_body);
        $payme_subscribe_request->response_body = is_array($response_array) ? json_encode($response_array) : $response_array;
        $payme_subscribe_request->send_date = date('Y.m.d H:i:s');
        $payme_subscribe_request->model_id = $model_id;
        $payme_subscribe_request->model_class = $model_class;
        $payme_subscribe_request->save();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['send_date'], 'safe'],
            [['model_id'], 'default', 'value' => null],
            [['model_id'], 'integer'],
            [['url', 'model_class'], 'string', 'max' => 255],
            [['request_body'], 'string', 'max' => 4000],
            [['response_body'], 'string', 'max' => 4000],
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
            'model_id' => 'Model ID',
            'model_class' => 'Model Class',
        ];
    }
}
