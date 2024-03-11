<?php

namespace backapi\models\forms\zoodPayForms;

use common\helpers\GeneralHelper;
use common\models\ZoodpayRequest;
use common\services\PaymentService;
use Yii;
use yii\httpclient\Client;
use yii\web\NotFoundHttpException;

class TransactionDeliveryForm extends \yii\base\Model
{
    public $model_id;
    public $model_class;

    public const MODEL_CLASS = [
        'Kasko'
    ];

    public function rules()
    {
        return [
            [['model_id', 'model_class'], 'required'],
            [['model_id'], 'integer'],
            [['model_class'], 'in', 'range' => self::MODEL_CLASS]
        ];
    }

    public function save()
    {
        $model_class = "common\\models\\" . $this->model_class;
        $order = $model_class::findOne($this->model_id);
        if (is_null($order))
            throw new NotFoundHttpException("model id not found");

        if (empty($order->trans) or $order->trans->payment_type != PaymentService::PAYMENT_TYPE['zoodpay'] or empty($order->trans->trans_no))
            throw new NotFoundHttpException("model must be paid by zoodpay");

        $trans_no = $order->trans->trans_no;
        $client = new Client();
        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('PUT')
            ->setUrl(GeneralHelper::env('zoodpay_url') . "/transactions/" . $trans_no . "/delivery")
            ->setData([
                'delivered_at' => date('Y-m-d H:i:s.000')
            ])
            ->addHeaders(['Authorization' => ZoodpayRequest::getAuthorization(), 'Content-Type' => 'application/json'])
            ->send();

        ZoodpayRequest::create(
            GeneralHelper::env('zoodpay_url') . "/transactions/" . $trans_no . "/delivery",
            ['delivered_at' => date('Y-m-d H:i:s.000')],
            $response->getContent(),
            null,
            $order->id,
            $model_class
        );

        return "Ok";
    }
}