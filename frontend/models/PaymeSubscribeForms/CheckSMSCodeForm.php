<?php

namespace frontend\models\PaymeSubscribeForms;

use common\models\PaymeSubscribeRequest;
use common\models\SavedCard;
use frontend\controllers\PaymeController;
use Yii;
use yii\web\BadRequestHttpException;

class CheckSMSCodeForm extends \yii\base\Model
{
    public ?string $model_class = null;
    public int $model_id;
    public int $saved_card_id;
    public $verifycode;

    public function rules(): array
    {
        return [
            [['saved_card_id', 'model_class', 'model_id'], 'required'],
            [['saved_card_id', 'verifycode', 'model_id'], 'integer'],
            [['model_class'], 'string', 'max' => 255],
            [['model_class'], 'in', 'range' => ['Kasko']],
            [['saved_card_id'], 'exist', 'skipOnError' => true, 'targetClass' => SavedCard::className(), 'targetAttribute' => ['saved_card_id' => 'id']],
            [['verifycode'], 'filter', 'filter' => 'trim'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'model_class' => Yii::t('app', 'model class'),
            'model_id' => Yii::t('app', 'model id'),
            'saved_card_id' => Yii::t('app', 'card id'),
            'verifycode' => Yii::t('app', 'verifycode'),
        ];
    }

    /**
     * @throws BadRequestHttpException
     */
    public function send()
    {
        $model_class = "common\\models\\" . $this->model_class;
        if (!($order = $model_class::findOne($this->model_id)))
            return throw new BadRequestHttpException(Yii::t('app', "Incorrect ID"));

        $saved_card = SavedCard::findOne($this->saved_card_id);

        if (!is_null($this->verifycode))
             PaymeSubscribeRequest::sendRequest(
                PaymeSubscribeRequest::METHODS['check_sms_code'],
                [
                    'token' => $saved_card->card_id,
                    'code' => (string)$this->verifycode,
                ],
                $model_class,
                $this->model_id,
            );

        $trans_no = PaymeSubscribeRequest::sendRequest(
            PaymeSubscribeRequest::METHODS['receipt_create'],
            [
                'amount' => $order->amount_uzs * 100,
                'account' => [
                    'order_id' => $this->model_id,
                    'type' => PaymeController::TYPE[strtolower(explode('\\', $model_class)[2])],
                ],
            ],
            $model_class,
            $this->model_id,
        );

        PaymeSubscribeRequest::sendRequest(
            PaymeSubscribeRequest::METHODS['receipt_pay'],
            [
                'id' => $trans_no,
                'token' => $saved_card->card_id,
            ],
            $model_class,
            $this->model_id,
        );

        return $order;
    }
}