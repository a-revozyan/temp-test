<?php

namespace frontend\models\ZoodPayForms;

use common\models\Kasko;
use common\models\Transaction;
use common\models\ZoodpayRequest;
use common\services\TelegramService;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class CreateReceiptForm extends \yii\base\Model
{
    public $model_id;
    public $model_class;
    public $card_number;
    public $card_expiry;

    public const MODEL_CLASS = [
        0 => 'Kasko',
        1 => 'Osago',
    ];

    public function rules()
    {
        return [
            [['model_id', 'model_class', 'card_number', 'card_expiry'], 'required'],
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

        $credit_balance = ZoodpayRequest::sendRequest(ZoodpayRequest::REQUEST['credit_balance'], $this->model_id, $model_class, false);
        $configuration = ZoodpayRequest::sendRequest(ZoodpayRequest::REQUEST['configuration'], $this->model_id, $model_class)['configuration'];
        $configuration = (array)$configuration[0];
        if (array_key_exists('credit_balance', $credit_balance))
        {
            $credit_balance = (array)$credit_balance['credit_balance'][0];
            $configuration['max_limit'] = $credit_balance['amount'];
        }

        return $configuration;
    }
}