<?php

namespace frontend\models\OsagoStepForms;

use common\models\HamkorpayRequest;
use common\models\Osago;
use common\models\Transaction;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\web\BadRequestHttpException;

class VerifyForm extends \yii\base\Model
{
    public $osago_uuid;
    public $verifycode;

    public function rules()
    {
        return [
            [['osago_uuid', 'verifycode'], 'required'],
            [['osago_uuid'], UuidValidator::className()],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid'],
                'filter' => function($query){
                    return $query->andWhere([
                        'f_user_id' => Yii::$app->user->id
                    ])->andWhere(['IN', 'status', [Osago::STATUS['step4']]]);
                }
            ],
        ];
    }

    public function send()
    {
        $osago = Osago::findOne(['uuid' => $this->osago_uuid]);

        if (in_array($osago->status, [
            Osago::STATUS['payed'],
            Osago::STATUS['waiting_for_policy'],
            Osago::STATUS['received_policy'],
        ]))
            throw new BadRequestHttpException('already paid');

        $create_response_body = HamkorpayRequest::find()->where([
            'and',
            ['model_class' => get_class($osago)],
            ['model_id' => $osago->id],
            ['ilike', 'request_body', 'pay.create']
        ])->orderBy('id desc')->one()->response_body ?? [];

        $pay_id = json_decode($create_response_body)->result->pay_id ?? null;

        $response = HamkorpayRequest::sendRequest('pay.confirm', $osago, [
            'pay_id' => $pay_id,
            'confirm_code' => $this->verifycode,
        ]);
        $state = $response['result']->state ?? null;
        if ($state != HamkorpayRequest::PAY_STATUS['confirmed'])
            throw new \common\custom\exceptions\BadRequestHttpException(Yii::t('app', "Hamkorpay da kutilmagan xatolik. Iltimos, yana bir bor urunib ko'ring"));

        $transaction = new Transaction();
        $transaction->partner_id = $osago->partner_id;
        $transaction->trans_no = $pay_id;
        $transaction->amount = ($osago->amount_uzs + $osago->accident_amount);
        $transaction->trans_date = date('Y-m-d');
        $transaction->create_time = time();
        $transaction->payment_type = 'hamkorpay';
        $transaction->status = $state;
        $transaction->save();

        $osago->trans_id = $transaction->id;
        $osago->save();

        $osago->saveAfterPayed();

        return $osago;
    }
}