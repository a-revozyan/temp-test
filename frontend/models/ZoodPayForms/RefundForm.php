<?php

namespace frontend\models\ZoodPayForms;

use common\models\Transaction;
use common\models\ZoodpayRequest;
use common\services\TelegramService;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class RefundForm extends \yii\base\Model
{
    public $refund;
    public $signature;
    public $refund_id;

    public function rules()
    {
        return [
            [['refund', 'signature', 'refund_id'], 'required'],
            [['signature', 'refund_id'], 'string'],
            [['refund'], 'filter', 'filter' => function ($value) {
                try {
                    $refund_attrs = [
                        'created_at', 'currency', 'declined_reason', 'merchant_refund_reference', 'refund_amount', 'refund_id',
                        'refunded_at', 'request_id', 'status', 'transaction_id'
                    ];
                    $refund_string_attrs = [
                        'created_at', 'currency', 'declined_reason', 'merchant_refund_reference', 'refund_id',
                        'refunded_at', 'request_id', 'status', 'transaction_id'
                    ];
                    $data = $value;
                    $dynamicModel = (new \yii\base\DynamicModel($refund_attrs))
                        ->addRule($refund_attrs, 'required', ['skipOnEmpty' => true])
                        ->addRule($refund_string_attrs, 'string')
                        ->addRule(['refund_amount'], 'integer');

                    $dynamicModel->setAttributes((array)$data);
                    $dynamicModel->validate();
                    if ($dynamicModel->hasErrors()) {
                        $this->addError('refund', $dynamicModel->getErrors());
                        return null;
                    }

                    return $value;
                } catch (\Exception $e) {
                    $this->addError('refund', $e->getMessage());
                    return null;
                }
            }],
        ];
    }

    public function save()
    {
        if (hash('sha512', ZoodpayRequest::getSignatureForRefund($this->refund)) != $this->signature)
            throw new BadRequestHttpException('signature check failed!');

        $transaction = Transaction::findOne(['trans_no' => $this->refund->transaction_id]);
        if (is_null($transaction))
            return throw new BadRequestHttpException('transaction is not found');

        $transaction->status = ZoodpayRequest::TRANSACTION_STATUS[$this->refund->status];
        $transaction->save();

        $order = ZoodpayRequest::getOrderByTransId($transaction->id);

        if ($this->refund->status == ZoodpayRequest::TRANSACTION_RUFUND_STATUS)
        {
            $order->statusToBackBeforePayment();
        }

        return 0;
    }
}