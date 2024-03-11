<?php

namespace frontend\models\ZoodPayForms;

use common\models\ZoodpayRequest;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class GetConfigurationForm extends \yii\base\Model
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
            [['model_id'], 'string'],
            [['model_id'], UuidValidator::className()],
            [['model_class'], 'in', 'range' => self::MODEL_CLASS]
        ];
    }

    public function save()
    {
        $model_class = "common\\models\\" . $this->model_class;
        $order = $model_class::findOne(['uuid' => $this->model_id]);
        if (is_null($order))
            throw new NotFoundHttpException("model id not found");

        $credit_balance = ZoodpayRequest::sendRequest(ZoodpayRequest::REQUEST['credit_balance'], $order->id, $model_class, false);
        $response_body = ZoodpayRequest::sendRequest(ZoodpayRequest::REQUEST['configuration'], $order->id, $model_class);

        if (!array_key_exists('configuration', $response_body))
            throw new BadRequestHttpException(Yii::t('app', 'Something went wrong with Zoodpay'));

        $configuration = $response_body['configuration'];
        $configuration = (array)$configuration[0];
        if (array_key_exists('credit_balance', $credit_balance))
        {
            $credit_balance = (array)$credit_balance['credit_balance'][0];
            $configuration['max_limit'] = $credit_balance['amount'];
        }

        return $configuration;
    }
}