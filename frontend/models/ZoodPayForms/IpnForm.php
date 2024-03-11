<?php

namespace frontend\models\ZoodPayForms;

use common\helpers\GeneralHelper;
use common\models\Transaction;
use common\models\ZoodpayRequest;
use common\services\TelegramService;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class IpnForm extends \yii\base\Model
{
    public $amount;
    public $created_at;
    public $status;
    public $transaction_id;
    public $merchant_order_reference;
    public $signature;

    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['status', 'transaction_id', 'merchant_order_reference', 'signature', 'amount'], 'required'],
            [['status', 'transaction_id', 'merchant_order_reference', 'signature'], 'string'],
            [['amount'], 'double'],
            [['status'], 'in', 'range' => array_keys(ZoodpayRequest::TRANSACTION_STATUS)]
        ];
    }

    public function save()
    {
        $order = ZoodpayRequest::getOrder($this->merchant_order_reference);
        if (is_null($order))
            return throw new BadRequestHttpException('order is not found');

        $sig = ZoodpayRequest::getSignatureForReceive($order, $this->merchant_order_reference, $this->transaction_id);

        if (hash('sha512', $sig) != $this->signature)
            throw new BadRequestHttpException('signature check failed!');

        $transaction = Transaction::findOne(['trans_no' => $this->transaction_id]);
        if (is_null($transaction))
            return throw new NotFoundHttpException('Transaction is not found');

        $transaction->status = ZoodpayRequest::TRANSACTION_STATUS[$this->status];
        $transaction->save();

        if ($this->status == ZoodpayRequest::TRANSACTION_PAID_STATUS and empty($transaction->perform_time))
        {
            $transaction->perform_time = time();
            $transaction->save();

            $order->saveAfterPayed();
        }

        $callback_url = GeneralHelper::env('front_website_url') . "/kasko/calculator/casco-results/$order->id/done";
        if ($transaction->status == ZoodpayRequest::TRANSACTION_STATUS['Inactive'])
            $callback_url = GeneralHelper::env('front_website_url') . "/kasko/calculator/casco-results/$order->id";

        return Yii::$app->response->redirect($callback_url);
    }
}