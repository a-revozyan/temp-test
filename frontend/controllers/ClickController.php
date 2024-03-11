<?php

namespace frontend\controllers;

use common\helpers\GeneralHelper;
use common\models\Kasko;
use common\models\Osago;
use common\models\Transaction;
use common\models\Travel;
use common\services\TelegramService;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;

class ClickController extends BaseController
{
    public const STATUS = [
        'created' => 0,
        'payed' => 2,
        'cancel' => -9
    ];


    /** @var INPUT string */
    const PAYMENT_STATUS_INPUT = 'input';
    /** @var WAITING string */
    const PAYMENT_STATUS_WAITING = 'waiting';
    /** @var PREAUTH string */
    const PAYMENT_STATUS_PREAUTH = 'preauth';
    /** @var CONFIRMED string */
    const PAYMENT_STATUS_CONFIRMED = 'confirmed';
    /** @var REJECTED string */
    const PAYMENT_STATUS_REJECTED  = 'rejected';
    /** @var REFUNDED string */
    const PAYMENT_STATUS_REFUNDED = 'refunded';
    /** @var ERROR string */
    const PAYMENT_STATUS_ERROR = 'error';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'prepare' => ['POST'],
                'complete' => ['POST']
            ]
        ];

        unset($behaviors['authenticator']);
        return $behaviors;
    }


    public function order($request)
    {
        $transaction_param_for_click = $request['merchant_trans_id'];
        $transaction_param_for_click = json_decode(base64_decode($transaction_param_for_click));
        // getting payment data from model
        switch ($transaction_param_for_click->type)
        {
            case 3:
                $order = Kasko::findOne($transaction_param_for_click->order_id);
                break;
            case 2:
                $order = Osago::findOne($transaction_param_for_click->order_id);
                break;
            case 1:
                $order = Travel::findOne($transaction_param_for_click->order_id);
                break;
            default :
                return [
                    'error' => -2,
                    'error_note' => 'incorrect type'
                ];
        }
        return $order;
    }

    public function actionPrepare($request = null)
    {
        // check the request to nan-null
        if($request == null){
            // getting POST data
            $request = $this->post;
        }

        $payment = $this->order($request)->toArray();
        // getting merchant_confirm_id and merchant_prepare_id
        $merchant_confirm_id = 0;
        $merchant_prepare_id = 0;

        if($payment){
            $merchant_confirm_id = $payment['id'];
            $merchant_prepare_id = $payment['id'];
        }

        // check the request data to errors
        $result = $this->request_check($request);

        // complete the result to response
        $result += [
            'click_trans_id' => $request['click_trans_id'],
            'merchant_trans_id' => $request['merchant_trans_id'],
            'merchant_confirm_id' => $merchant_confirm_id,
            'merchant_prepare_id' => $merchant_prepare_id
        ];

        // change the payment status to waiting if request data will be possible
        if($result['error'] == 0){
            $order = $this->order($request);

            $transaction = new Transaction();
            $transaction->partner_id = $order->partner_id;
            $transaction->trans_no = $request['click_paydoc_id'];
            $transaction->amount = $request['amount'];
            $transaction->create_time = strtotime($request['sign_time']);
            $transaction->trans_date = date('Y-m-d');
            $transaction->perform_time = 0;
            $transaction->cancel_time = 0;
            $transaction->status = self::STATUS['created'];
            $transaction->payment_type = 'click';
            $transaction->save();

            $order->trans_id = Transaction::findOne(['trans_no' => $transaction->trans_no])->id;
            $order->save();
        }

        // return response array
        return $result;
    }

    public function actionComplete($request = null)
    {
        // check the request to nan-null
        if($request == null){
            $request = $this->post;
        }

        $payment = $this->order($request)->toArray();

        // fill merchant_confirm_id and merchant_prepare_id
        $merchant_confirm_id = 0;

        $merchant_prepare_id = 0;

        if($payment){
            $merchant_confirm_id = $payment['id'];
            $merchant_prepare_id = $payment['id'];
        }
        // check the request data to errors
        $result = $this->request_check($request);

        // prepare the data to response
        $result += [
            'click_trans_id' => $request['click_trans_id'],
            'merchant_trans_id' => $request['merchant_trans_id'],
            'merchant_confirm_id' => $merchant_confirm_id,
            'merchant_prepare_id' => $merchant_prepare_id
        ];

        if ($request['error'] < 0)
        {
            $order = $this->order($request);
          
            \Yii::error($order->id . " " .json_encode($result) . ' ' . date('Y-m-d H:i:s') . ' ' . '(click)', 'test');
//            $order->statusToBackBeforePayment();
        }

        if($request['error'] < 0 && ! in_array($result['error'], [-4, -9]) ){
            // update payment status to error if request data will be error
//            $this->model->update_by_id($payment['id'], ['status' => self::PAYMENT_STATUS_REJECTED]);
            $result = [
                'error' => -9,
                'error_note' => 'Transaction cancelled'
            ];

        } elseif( $result['error'] == 0 ) {
            // update payment status to confirmed if request data will be success
            //$this->model->update_by_id($payment['id'], ['status' => self::PAYMENT_STATUS_CONFIRMED]);

            $order = $this->order($request);

            $transaction = $order->trans;
            $transaction->status = 2;
            $transaction->save();

            $order->saveAfterPayed();
        }

        // return response array
        return $result;
    }

    public function request_check($request){
        // check to error in request from click
        if($this->is_not_possible_data($request)){
            // return response array-like
            return [
                'error' => -8,
                'error_note' => 'Error in request from click'
            ];
        }

        // prepare sign string as md5 digest
        $sign_string = md5(
            $request['click_trans_id'] .
            $request['service_id'] .
            GeneralHelper::env('click_secret_key') .
            $request['merchant_trans_id'] .
            ($request['action'] == 1 ? $request['merchant_prepare_id'] : '') .
            $request['amount'] .
            $request['action'] .
            $request['sign_time']
        );
        // check sign string to possible
        if($sign_string != $request['sign_string']){
            // return response array-like
            return [
                'error' => -1,
                'error_note' => 'SIGN CHECK FAILED!'
            ];
        }

        // check to action not found error
        if (!((int)$request['action'] == 0 || (int)$request['action'] == 1)) {
            // return response array-like
            return [
                'error' => -3,
                'error_note' => 'Action not found'
            ];
        }

        // get payment data by merchant_trans_id
        $payment = $this->order($request);

        if(!$payment){
            // return response array-like
            return [
                'error' => -5,
                'error_note' => 'User does not exist'
            ];
        }

        // get payment data by merchant_prepare_id
        if( $request['action'] == 1 ) {
            $payment = $this->order($request);
            if(!$payment){
                // return response array-like
                return [
                    'error' => -6,
                    'error_note' => 'Transaction does not exist	'
                ];
            }
        }


        $re = $this->validateStatusOfOrder($payment, $request);
        if (!is_null($re))
            return $re;

        // check to correct amount
        $diff = abs((float)$payment['amount_uzs'] - (float)$request['amount']);
        if ($payment->hasAttribute('accident_amount'))
            $diff = abs((float)($payment['amount_uzs'] + $payment['accident_amount']) - (float)$request['amount']);

        if($diff > 0.01){
            // return response array-like
            return [
                'error' => -2,
                'error_note' => 'Incorrect parameter amount'
            ];
        }

        // check status to transaction cancelled
//        if($payment['status'] == PaymentsStatus::REJECTED){
//            // return response array-like
//            return [
//                'error' => -9,
//                'error_note' => 'Transaction cancelled'
//            ];
//        }

        // return response array-like as success
        return [
            'error' => 0,
            'error_note' => 'Success'
        ];

    }

    private function is_not_possible_data($request){
        if(!(
                isset($request['click_trans_id']) &&
                isset($request['service_id']) &&
                isset($request['merchant_trans_id']) &&
                isset($request['amount']) &&
                isset($request['action']) &&
                isset($request['error']) &&
                isset($request['error_note']) &&
                isset($request['sign_time']) &&
                isset($request['sign_string']) &&
                isset($request['click_paydoc_id'])
            ) || $request['action'] == 1 && ! isset($request['merchant_prepare_id'])) {
            return true;
        }
        return false;
    }

    public function validateStatusOfOrder($payment, $request)
    {
        $transaction_param_for_click = $request['merchant_trans_id'];
        $transaction_param_for_click = json_decode(base64_decode($transaction_param_for_click));

        switch ($transaction_param_for_click->type)
        {
            case 3:
                // check to already paid
                if(
                    in_array($payment['status'], [
                        Kasko::STATUS['payed'],
                        Kasko::STATUS['attached'],
                        Kasko::STATUS['processed'],
                        Kasko::STATUS['policy_generated']
                    ])
                ){
                    return [
                        'error' => -4,
                        'error_note' => 'Already paid'
                    ];
                }

                // check to invoice not found or order is not ready
                if(
                    in_array($payment['status'], [
                        Kasko::STATUS['step1'],
                        Kasko::STATUS['step2'],
                        Kasko::STATUS['step3'],
                        Kasko::STATUS['canceled'],
                    ])
                ){
                    return [
                        'error' => -6,
                        'error_note' => 'Invoice not found or canceled'
                    ];
                }
                break;
            case 2:
                if(
                    in_array($payment['status'], [
                        Osago::STATUS['payed'],
                        Osago::STATUS['waiting_for_policy'],
                        Osago::STATUS['received_policy'],
                    ])
                ){
                    return [
                        'error' => -4,
                        'error_note' => 'Already paid'
                    ];
                }

                if(
                    in_array($payment['status'], [
                        Osago::STATUS['step1'],
                        Osago::STATUS['step2'],
                        Osago::STATUS['step3'],
                        Osago::STATUS['canceled'],
                    ])
                ){
                    return [
                        'error' => -6,
                        'error_note' => 'Invoice not found or canceled'
                    ];
                }
                break;
            case 1:
                if(
                    in_array($payment['status'], [
                        Travel::STATUSES['payed'],
                        Travel::STATUSES['received_policy'],
                        Travel::STATUSES['waiting_for_policy'],
                    ])
                ){
                    return [
                        'error' => -4,
                        'error_note' => 'Already paid'
                    ];
                }

                if(
                    in_array($payment['status'], [
                        Travel::STATUSES['step1'],
                        Travel::STATUSES['step2'],
                        Travel::STATUSES['canceled'],
                    ])
                ){
                    return [
                        'error' => -6,
                        'error_note' => 'Invoice not found or canceled'
                    ];
                }
                break;
            default :
                return [
                    'error' => -2,
                    'error_note' => 'incorrect type'
                ];
        }

        return null;
    }
}