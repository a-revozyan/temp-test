<?php

namespace frontend\controllers;

use common\helpers\GeneralHelper;
use common\models\Kasko;
use common\models\Osago;
use common\models\SavedCard;
use common\models\Transaction;
use common\models\Travel;
use common\services\TelegramService;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;

class PayzeController extends BaseController
{
    public const KEY = '3806270853A94C63BD59ABE778CE0958';
    public const SECRET = '60B4446671AC42E5870347843A44489A';
    public const URL = 'https://payze.io/api/v1';

    public const STATUS = [
        'Created' => 0,
        'Committed' => 1,
        'CardAdded' => 2,
        'Blocked' => 3,
        'Refunded' => 4,
        'RefundedPartially' => 5,
        'Timeout' => 6,
        'Rejected' => 7,
    ];

    public const TRANSACTION_PAID_STATUSES = ["CardAdded", "Committed"];

    public static function getCallbackErrorUrl()
    {
        return GeneralHelper::env('front_website_url');
    }

    public static function getHookUrl()
    {
        return GeneralHelper::env('frontend_project_website') . "/payze/hook";
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'hook' => ['POST'],
            ]
        ];

        $behaviors['authenticator']['only'] = ["cards"];

        return $behaviors;
    }

    public static function saveCard($callback_url, $order)
    {
        $request_body = [
            "method" => "addCard",
            "apiKey" => self::KEY,
            "apiSecret" => self::SECRET,
            "data" => [
                "callback" => $callback_url,
                "callbackError" => self::getCallbackErrorUrl(),
                "hookUrl" => self::getHookUrl(),
                "amount" => $order->amount_uzs+$order->accident_amount,
//                "amount" => 10,
                "currency" => "UZS"
            ]
        ];
        $client = new Client();
        $response = $client->post(self::URL, json_encode($request_body), [
            'accept' => 'application/json',
            'content-type' => 'application/json'
        ])->send();

        $response = json_decode($response->getContent());
        $saved_card = new SavedCard();
        $saved_card->card_id = $response->response->cardId;
        $saved_card->trans_no = $response->response->transactionId;
        $saved_card->f_user_id = \Yii::$app->user->id;
        $saved_card->status = SavedCard::STATUS['created'];
        $saved_card->created_at = time();
        $saved_card->save();

        self::createTransaction($order, $response->response->transactionId);

        return $response->response->transactionUrl;
    }

    public static function payWithCard($callback_url, $order, $card_id)
    {
        $saved_card = SavedCard::findOne($card_id);
        $request_body = [
            "method" => "payWithCard",
            "apiKey" => self::KEY,
            "apiSecret" => self::SECRET,
            "data" => [
                "callback" => $callback_url,
                "callbackError" => self::getCallbackErrorUrl(),
                "hookUrl" => self::getHookUrl(),
                "amount" => $order->amount_uzs + $order->accident_amount,
//                "amount" => 10,
                "currency" => "UZS",
                "cardToken" => $saved_card->card_id,
                "preauthorize" => false,
            ]
        ];
        $client = new Client();
        $response = $client->post(self::URL, json_encode($request_body), [
            'accept' => 'application/json',
            'content-type' => 'application/json'
        ])->send();

        $response = json_decode($response->getContent());
        if (property_exists($response->response, 'error'))
            throw new BadRequestHttpException($response->response->error);

        self::createTransaction($order, $response->response->transactionId);
        return $callback_url;
    }

    private static function createTransaction($order, $trans_no)
    {
        $trans = new Transaction();
        $trans->partner_id = $order->partner_id;
        $trans->trans_no = $trans_no;
        $trans->amount = $order->amount_uzs;
//        $trans->amount = 10;
        $trans->trans_date = date('Y-m-d');
        $trans->create_time = time();
        $trans->payment_type = 'payze';
        $trans->status = 0;
        $trans->save();

        $order->trans_id = $trans->id;
    }

    public function actionHook()
    {
        $post = json_decode(\Yii::$app->getRequest()->getRawBody());

        $trans = Transaction::findOne(['trans_no' => $post->transactionId]);
        $trans->status = self::STATUS[$post->status];
        $trans->save();

        if (in_array($post->status, self::TRANSACTION_PAID_STATUSES))
        {
            $saved_card = SavedCard::findOne(['trans_no' => $post->transactionId]);
            if (!is_null($saved_card))
            {
                $saved_card->card_mask = $post->cardMask;
                $saved_card->status = 1;
                $saved_card->save();
            }

            $trans = self::activateTrans($post->transactionId, $post->status);

            $order = Osago::findOne(['trans_id' => $trans->id]);
            if (is_null($order))
                $order = Kasko::findOne(['trans_id' => $trans->id]);

            $order->saveAfterPayed();
        }

        return $saved_card;
    }

    private static function activateTrans($transactionId, $status)
    {
        $trans = Transaction::findOne(['trans_no' => $transactionId]);
        $trans->status = self::STATUS[$status];
        $trans->perform_time = time();
        $trans->save();

        return $trans;
    }

    public function actionCards()
    {
        return SavedCard::find()->select(['id', 'card_mask'])
            ->where([
                'f_user_id' => \Yii::$app->user->id,
                'status' => SavedCard::STATUS['saved']
            ])->all();
    }
}