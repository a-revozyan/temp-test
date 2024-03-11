<?php

namespace frontend\models\CascoStepForms;

use common\models\HamkorpayRequest;
use common\models\Kasko;
use common\models\Transaction;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\web\BadRequestHttpException;

class VerifyForm extends \yii\base\Model
{
    public $kasko_uuid;
    public $verifycode;

    public function rules()
    {
        return [
            [['kasko_uuid', 'verifycode'], 'required'],
            [['kasko_uuid'], UuidValidator::className()],
            [['kasko_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Kasko::className(), 'targetAttribute' => ['kasko_uuid' => 'uuid'],
                'filter' => function($query){
                    return $query->andWhere([
                        'f_user_id' => Yii::$app->user->id
                    ])->andWhere(['IN', 'status', [Kasko::STATUS['step4']]]);
                }
            ],
        ];
    }

    public function send()
    {
        $casco = Kasko::findOne(['uuid' => $this->kasko_uuid]);

        if (in_array($casco->status, [
            Kasko::STATUS['payed'],
            Kasko::STATUS['attached'],
            Kasko::STATUS['processed'],
            Kasko::STATUS['policy_generated'],
        ]))
            throw new BadRequestHttpException('already paid');

        $create_response_body = HamkorpayRequest::find()->where([
            'and',
            ['model_class' => get_class($casco)],
            ['model_id' => $casco->id],
            ['ilike', 'request_body', 'pay.create']
        ])->orderBy('id desc')->one()->response_body ?? [];

        $pay_id = json_decode($create_response_body)->result->pay_id ?? null;

        $response = HamkorpayRequest::sendRequest('pay.confirm', $casco, [
            'pay_id' => $pay_id,
            'confirm_code' => $this->verifycode,
        ]);

        $transaction = new Transaction();
        $transaction->partner_id = $casco->partner_id;
        $transaction->trans_no = $pay_id;
        $transaction->amount = $casco->amount_uzs;
        $transaction->trans_date = date('Y-m-d');
        $transaction->create_time = time();
        $transaction->payment_type = 'hamkorpay';
        $transaction->status = $response['result']->state ?? null;
        $transaction->save();

        $casco->trans_id = $transaction->id;
        $casco->save();

        $casco->saveAfterPayed();

        return $casco;
    }
}