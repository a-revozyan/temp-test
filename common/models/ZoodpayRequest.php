<?php

namespace common\models;

use common\helpers\GeneralHelper;
use frontend\controllers\ClickController;
use Yii;
use yii\base\Exception;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "zoodpay_request".
 *
 * @property int $id
 * @property string|null $url
 * @property string|null $request_body
 * @property string|null $response_body
 * @property string|null $response_status_code
 * @property string|null $send_date
 * @property integer|null $model_id
 * @property string|null $model_class
 */
class ZoodpayRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zoodpay_request';
    }

    public const TRANSACTION_STATUS = [
        'created' => 0,
        'Paid' => 1,             //ipn
        'Pending' => -1,         //ipn
        'Failed' => -2,          //ipn
        'Cancelled' => -3,       //ipn
        'Inactive' => -4,        //ipn
        'Initiated' => -5,        //refund
        'Approved' => -6,        //refund
        'Declined' => -7,        //refund
    ];

    public const TRANSACTION_PAID_STATUS = "Paid";
    public const TRANSACTION_RUFUND_STATUS = "Approved";

    public const REQUEST = [
        'configuration' => 0,
        'create_transaction' => 1,
        'credit_balance' => 2,
        'refund' => 3,
    ];

    public const URL = [
        self::REQUEST['configuration'] => '/configuration',
        self::REQUEST['create_transaction'] => '/transactions',
        self::REQUEST['credit_balance'] => '/customer/credit/balance',
        self::REQUEST['refund'] => '/refunds',
    ];

    public const MARKET_CODE = "UZ";
    public const CURRENCY = "UZS";
    public const MODEL_NAMESPACE = "common\\models\\";

    public static function getAuthorization()
    {
        return 'Basic ' . base64_encode(GeneralHelper::env('zoodpay_merchant_key') . ":" . GeneralHelper::env('zoodpay_merchant_secret'));
    }

    public static function getSignatureForSend($order, $merchant_reference_no)
    {
        return GeneralHelper::env('zoodpay_merchant_key') . "|" . $merchant_reference_no . "|" . $order->amount_uzs . "|" . self::CURRENCY . "|" . self::MARKET_CODE . "|" . GeneralHelper::env('salt');
    }

    public static function getSignatureForReceive($order, $merchant_reference_no, $trans_no)
    {
        return  self::MARKET_CODE . "|" . self::CURRENCY . "|" . number_format((float)$order->amount_uzs, 2, '.', '') . "|" . $merchant_reference_no . "|" . GeneralHelper::env('zoodpay_merchant_key') . "|" . $trans_no . "|" . GeneralHelper::env('salt');
    }

    public static function getSignatureForRefund($refund)
    {
        return $refund->merchant_refund_reference . "|" . $refund->refund_amount . "|" . $refund->status . "|" . GeneralHelper::env('zoodpay_merchant_key') . "|" . $refund->refund_id . "|" . GeneralHelper::env('salt');
    }

    public static function getMerchantReferenceNo($model_class, $model_id)
    {
        return explode('\\', $model_class)[2] . '-' . $model_id;
    }

    public static function getOrder($merchant_order_reference)
    {
        $model_class = explode('-', $merchant_order_reference)[0];
        $model_id = explode('-', $merchant_order_reference)[1];
        return  (self::MODEL_NAMESPACE . $model_class)::findOne($model_id);
    }

    public static function getOrderByTransId($trans_id)
    {
        $order = Osago::find()->where(['trans_id' => $trans_id])->one();
        if ($order == null)
            $order = Kasko::find()->where(['trans_id' => $trans_id])->one();
        if ($order == null)
            $order = Travel::find()->where(['trans_id' => $trans_id])->one();
        if ($order == null)
            $order = Accident::find()->where(['trans_id' => $trans_id])->one();

        return $order;
    }

    public static function sendRequest($request, $model_id, $model_class, $throw_error = true, $callbackurl = "")
    {
        $client = new Client();
        $order = $model_class::findOne($model_id);

        $url = GeneralHelper::env('zoodpay_url') . self::URL[$request];
        $request_body = self::getRequestBody($request, $order, $model_class, $callbackurl);

        try {
            $response = $client->post($url, json_encode($request_body), ['Authorization' => self::getAuthorization(), 'Content-Type' => 'application/json'])->send();
            $response_array = (array)json_decode($response->getContent());
            $response_status_code = $response->getStatusCode();
        }catch (Exception $exception) {
            $response_array = $exception->getMessage();
        }

        self::create($url, $request_body, $response_array, $response_status_code, $model_id, $model_class);

        if ($throw_error and is_array($response_array) and array_key_exists('status', $response_array) and !in_array($response_array['status'], [200, 201]))
            return self::throw_error($response_array['status']);

        if (is_array($response_array) and array_key_exists('transaction_id', $response_array) and $request == self::REQUEST['create_transaction'])
            self::createTransaction($response_array['transaction_id'], $order);

        return $response_array;
    }

    public static function throw_error($status)
    {
        $message = '';
        switch ($status)
        {
            case 400 :
                $message = Yii::t('app', 'Relevant error message will be displayed');
                break;
            case 401 :
                $message = Yii::t('app', 'You are not authenticated to perform the requested action.');
                break;
            case 404 :
                $message = Yii::t('app', 'Resource not found.');
                break;
            case 500 :
                $message = Yii::t('app', 'Please try again.');
                break;
            case 503 :
                $message = Yii::t('app', 'Service Unavailable');
                break;
            case 504 :
                $message = Yii::t('app', 'Gateway Timeout');
                break;
        }

        throw new BadRequestHttpException($message, $status);
    }

    public static function create($url, $request_body, $response_array, $response_status_code, $model_id, $model_class)
    {
        $zoodpay_request = new ZoodpayRequest();
        $zoodpay_request->url = $url;
        $zoodpay_request->request_body = json_encode($request_body);
        $zoodpay_request->response_body = is_array($response_array) ? json_encode($response_array) : $response_array;
        $zoodpay_request->response_status_code = $response_status_code;
        $zoodpay_request->send_date = date('Y-m-d H:i:s');
        $zoodpay_request->model_id = $model_id;
        $zoodpay_request->model_class = $model_class;
        $zoodpay_request->save();

        return $zoodpay_request;
    }

    public static function createTransaction($transaction_id, $order)
    {
        $transaction = new Transaction();
        $transaction->partner_id = $order->partner_id;
        $transaction->trans_no = $transaction_id;
        $transaction->amount = $order->amount_uzs;
        $transaction->create_time = time();
        $transaction->trans_date = date('Y-m-d');
        $transaction->perform_time = 0;
        $transaction->cancel_time = 0;
        $transaction->status = self::TRANSACTION_STATUS['created'];
        $transaction->payment_type = 'zoodpay';
        $transaction->save();

        $order->trans_id = Transaction::findOne(['trans_no' => $transaction->trans_no])->id;
        $order->save();
    }

    public static function getRequestBody($request, $order, $model_class, $callbackurl)
    {
        $request_body = [];
        switch ($request)
        {
            case self::REQUEST['configuration'] :
                $request_body = self::getConfigurationRequestBody();
                break;
            case self::REQUEST['create_transaction'] :
                $request_body = self::createTransactionRequestBody($order, $model_class, $callbackurl);
                break;
            case self::REQUEST['credit_balance'] :
                $request_body = self::creditBalanceRequestBody();
                break;
            case self::REQUEST['refund'] :
                $request_body = self::refundRequestBody($order, $model_class);
                break;
        }

        return $request_body;
    }

    public static function getConfigurationRequestBody()
    {
        return [
            "market_code" => self::MARKET_CODE
        ];
    }

    public static function createTransactionRequestBody($order, $model_class, $callbackurl)
    {
        $arr = explode('\\', $model_class);
        $product_name = $arr[array_key_last($arr)];
        $lang = GeneralHelper::lang_of_local();

        $merchant_reference_no = self::getMerchantReferenceNo($model_class, $order->id);
        $signature = hash('sha512', self::getSignatureForSend($order, $merchant_reference_no));

        /** @var User $user */
        $user = Yii::$app->user->identity;
        return [
            "customer" => [
                "customer_phone" => $user->phone,
                "first_name" => $user->first_name
            ],
            "items" => [
                [
                    "categories" => [
                        [
                            "policy"
                        ]
                    ],
                    "currency_code" => self::CURRENCY,
                    "name" => $product_name,
                    "price" => $order->amount_uzs,
                    "quantity" => 1
                ]
            ],
            "order" => [
                "amount" => $order->amount_uzs,
                "currency" => self::CURRENCY,
                "lang" => $lang,
                "market_code" => self::MARKET_CODE,
                "merchant_reference_no" => $merchant_reference_no,
                "service_code" => "ZPI",
                "signature" => $signature,
            ],
            "shipping" => [
                "address_line1" => "Uzbekistan",
                "country_code" => "UZ",
                "name" => "Sug'urtabozor",
                "zipcode" => "100100"
            ],
            "callbacks" => [
                "success_url" => GeneralHelper::env('front_website_send_request_url') .  "/zood-pay/ipn?id=$order->id",
                "error_url" => GeneralHelper::env('front_website_send_request_url') .  "/zood-pay/ipn?id=$order->id",
                "ipn_url" => GeneralHelper::env('front_website_send_request_url') .  "/zood-pay/ipn",
                "refund_url" => GeneralHelper::env('front_website_send_request_url') .  "/zood-pay/refund",
            ]
        ];
    }

    public static function creditBalanceRequestBody()
    {
        return [
            "customer_mobile" => Yii::$app->user->identity->phone,
            "market_code" => self::MARKET_CODE
        ];
    }

    public static function refundRequestBody($order, $model_class)
    {
        $client = new Client();

        $trans_no = $order->trans->trans_no;

        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('GET')
            ->setUrl(GeneralHelper::env('zoodpay_url') . "/transactions/" . $trans_no)
            ->addHeaders(['Authorization' => ZoodpayRequest::getAuthorization(), 'Content-Type' => 'application/json'])
            ->send();

        ZoodpayRequest::create(
            GeneralHelper::env('zoodpay_url') . "/transactions/" . $trans_no,
            [],
            $response->getContent(),
            null,
            $order->id,
            $model_class
        );

        $refund_amount = ((array)json_decode($response->getContent()))['amount'];

        return [
            'transaction_id' => $trans_no,
            'refund_amount' => $refund_amount,
            'reason' => "Had to do so",
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'request_body', 'response_body', 'model_class', 'send_date'], 'string'],
            [['model_id', 'response_status_code'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_id' => 'Model Id',
            'model_class' => 'Model class',
            'request_body' => 'request_body',
            'response_body' => 'response_body',
            'url' => 'url',
            'send_date' => 'send_date',
            'response_status_code' => 'response_status_code',
        ];
    }

    public function getOsago()
    {
        return $this->hasOne(Osago::className(), ['id' => 'osago_id'])->orOnCondition(['model_class' => Osago::className()]);
    }

    public function getKasko()
    {
        return $this->hasOne(Kasko::className(), ['id' => 'osago_id'])->orOnCondition(['model_class' => Kasko::className()]);
    }

}
