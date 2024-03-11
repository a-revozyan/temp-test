<?php
namespace frontend\controllers;

use common\helpers\GeneralHelper;
use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\services\TelegramService;
use Yii;
use yii\helpers\VarDumper;
use yii\rest\Controller;
use yii\filters\auth\HttpBasicAuth;
use common\models\Osago;
use common\models\Kasko;
use common\models\Travel;
use common\models\Accident;
use common\models\Transaction;
use frontend\models\PaycomException;
use yii\httpclient\Client;
use yii\helpers\Url;


class PaymeController extends Controller
{
    public const TYPE = [
        'does_not_metter_type' => -1,
        'travel' => 1,
        'osago' => 2,
        'kasko' => 3,
        'accident' => 4,
        'kaskobysubscriptionpolicy' => 5,
    ];

    public $type = -1;
    public $request_id;
    public $params;
    /** Order is available for sell, anyone can buy it. */
    const STATE_AVAILABLE = 0;

    /** Pay in progress, order must not be changed. */
    const STATE_WAITING_PAY = 1;

    /** Order completed and not available for sell. */
    const STATE_PAY_ACCEPTED = 2;

    /** Order is cancelled. */

    const STATE_CREATED                  = 1;
    const STATE_COMPLETED                = 2;
    const STATE_CANCELLED                = -1;
    const STATE_CANCELLED_AFTER_COMPLETE = -2;

    const TIMEOUT = 43200000;


    const REASON_RECEIVERS_NOT_FOUND         = 1;
    const REASON_PROCESSING_EXECUTION_FAILED = 2;
    const REASON_EXECUTION_FAILED            = 3;
    const REASON_CANCELLED_BY_TIMEOUT        = 4;
    const REASON_FUND_RETURNED               = 5;
    const REASON_UNKNOWN                     = 10;

    //public $modelClass = "common\models\Filial";

    /*public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
        ];
        return $behaviors;
    }*/
    public function checkAccess($code, $header) {
       $authKey = "Paycom:" . GeneralHelper::env('payme_password');

      if("Basic " . trim(utf8_encode(base64_encode($authKey))) != $header->get('Authorization')) {
          return false;
        }

      return true;
    }

    public function actionPayme()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = 200;

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $this->params = (object) $data['params'];

        if (isset($this->params->account) and empty($this->params->account))
            return [
                "error" => PaymeController::RPCErrors('access_denied'),
            ];

        if(isset($this->params->account))
            $this->type = $this->params->account['type'];

        switch ((int)$this->type)
        {
            case self::TYPE['travel'] :
                return $this->actionTravel();
                break;
            case self::TYPE['osago'] :
            case self::TYPE['does_not_metter_type'] :
                return $this->actionOsago();
                break;
            case self::TYPE['kasko'] :
                return $this->actionKasko();
                break;
            case self::TYPE['accident'] :
                return $this->actionAccident();
                break;
            case self::TYPE['kaskobysubscriptionpolicy'] :
                return $this->actionKaskoBySubscriptionPolicy();
                break;
            default :
                return PaycomException::response(
                    $this->request_id,
                    PaycomException::message(
                        'type is not found',
                        'type is not found',
                        'type is not found'
                    ),
                    PaycomException::ERROR_INVALID_ACCOUNT,
                    'order_id'
                );
        }

    }

    public function actionOsago() {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = 200;

        $header = Yii::$app->request->getHeaders();

        if(!$this->checkAccess('osago', $header)) {
          return [
              "error" => PaymeController::RPCErrors('access_denied'),
            ];
        }

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $method = $data['method'];
        $this->params = (object) $data['params'];
        $this->request_id = $data["id"];
        $account = null;
        if(isset($params->account)) $account = (object) $params->account;
        if(isset($params->amount)) $amount = $params->amount;

        // Method CheckPerformTransaction
        if($method == "CheckPerformTransaction") {
          return $this->CheckPerformTransaction('osago');
        } elseif($method == "CreateTransaction") {
          return $this->CreateTransaction('osago');
        } elseif($method == "CancelTransaction") {
          return $this->CancelTransaction('osago');
        } elseif($method == "PerformTransaction") {
          return $this->PerformTransaction('osago');
        } elseif($method == "CheckTransaction") {
          return $this->CheckTransaction('osago');
        } elseif($method == "ChangePassword") {
          return [
            'result' => [
                'success' => true,
            ]
          ];
        } else {
          return [
            "error" => PaymeController::RPCErrors('method_not_found'),
            "id" => $data["id"],
          ];
        }
    }

    public function actionKasko() {

      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = 200;

        $header = Yii::$app->request->getHeaders();

        if(!$this->checkAccess('kasko', $header)) {
          return [
              "error" => PaymeController::RPCErrors('access_denied'),
            ];
        }

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $method = $data['method'];
        $this->params = (object) $data['params'];
        $this->request_id = $data["id"];
        $account = null;
        if(isset($params->account)) $account = (object) $params->account;
        if(isset($params->amount)) $amount = $params->amount;

        // Method CheckPerformTransaction
        if($method == "CheckPerformTransaction") {
          return $this->CheckPerformTransaction('kasko');
        } elseif($method == "CreateTransaction") {
          return $this->CreateTransaction('kasko');
        } elseif($method == "CancelTransaction") {
          return $this->CancelTransaction('kasko');
        } elseif($method == "PerformTransaction") {
          return $this->PerformTransaction('kasko');
        } elseif($method == "CheckTransaction") {
          return $this->CheckTransaction('kasko');
        } elseif($method == "ChangePassword") {
          return [
            'result' => [
                'success' => true,
            ]
          ];
        } else {
          return [
            "error" => PaymeController::RPCErrors('method_not_found'),
            "id" => $data["id"],
          ];
        }
    }

    public function actionTravel() {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = 200;

        $header = Yii::$app->request->getHeaders();

        if(!$this->checkAccess('travel', $header)) {
          return [
              "error" => PaymeController::RPCErrors('access_denied'),
            ];
        }

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $method = $data['method'];
        $this->params = (object) $data['params'];
        $this->request_id = $data["id"];
        $account = null;
        if(isset($params->account)) $account = (object) $params->account;
        if(isset($params->amount)) $amount = $params->amount;

        // Method CheckPerformTransaction
        if($method == "CheckPerformTransaction") {
          return $this->CheckPerformTransaction('travel');
        } elseif($method == "CreateTransaction") {
          return $this->CreateTransaction('travel');
        } elseif($method == "CancelTransaction") {
          return $this->CancelTransaction('travel');
        } elseif($method == "PerformTransaction") {
          return $this->PerformTransaction('travel');
        } elseif($method == "CheckTransaction") {
          return $this->CheckTransaction('travel');
        } elseif($method == "ChangePassword") {
          return [
            'result' => [
                'success' => true,
            ]
          ];
        } else {
          return [
            "error" => PaymeController::RPCErrors('method_not_found'),
            "id" => $data["id"],
          ];
        }
    }

    public function actionAccident() {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = 200;

        $header = Yii::$app->request->getHeaders();

        if(!$this->checkAccess('accident', $header)) {
          return [
              "error" => PaymeController::RPCErrors('access_denied'),
            ];
        }

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $method = $data['method'];
        $this->params = (object) $data['params'];
        $this->request_id = $data["id"];
        $account = null;
        if(isset($params->account)) $account = (object) $params->account;
        if(isset($params->amount)) $amount = $params->amount;

        // Method CheckPerformTransaction
        if($method == "CheckPerformTransaction") {
          return $this->CheckPerformTransaction('accident');
        } elseif($method == "CreateTransaction") {
          return $this->CreateTransaction('accident');
        } elseif($method == "CancelTransaction") {
          return $this->CancelTransaction('accident');
        } elseif($method == "PerformTransaction") {
          return $this->PerformTransaction('accident');
        } elseif($method == "CheckTransaction") {
          return $this->CheckTransaction('accident');
        } elseif($method == "ChangePassword") {
          return [
            'result' => [
                'success' => true,
            ]
          ];
        } else {
          return [
            "error" => PaymeController::RPCErrors('method_not_found'),
            "id" => $data["id"],
          ];
        }
    }

    public function actionKaskoBySubscriptionPolicy() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = 200;

        $header = Yii::$app->request->getHeaders();

        if(!$this->checkAccess('accident', $header)) {
            return [
                "error" => PaymeController::RPCErrors('access_denied'),
            ];
        }

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $method = $data['method'];
        $this->params = (object) $data['params'];
        $this->request_id = $data["id"];
        $account = null;
        if(isset($params->account)) $account = (object) $params->account;
        if(isset($params->amount)) $amount = $params->amount;

        // Method CheckPerformTransaction
        if($method == "CheckPerformTransaction") {
            return $this->CheckPerformTransaction('kaskobysubscriptionpolicy');
        } elseif($method == "CreateTransaction") {
            return $this->CreateTransaction('kaskobysubscriptionpolicy');
        } elseif($method == "CancelTransaction") {
            return $this->CancelTransaction('kaskobysubscriptionpolicy');
        } elseif($method == "PerformTransaction") {
            return $this->PerformTransaction('kaskobysubscriptionpolicy');
        } elseif($method == "CheckTransaction") {
            return $this->CheckTransaction('kaskobysubscriptionpolicy');
        } elseif($method == "ChangePassword") {
            return [
                'result' => [
                    'success' => true,
                ]
            ];
        } else {
            return [
                "error" => PaymeController::RPCErrors('method_not_found'),
                "id" => $data["id"],
            ];
        }
    }

    public function validate($params, $code)
    {
        // todo: Validate amount, if failed throw error
        // for example, check amount is numeric
        if (!is_numeric($params->amount)) {
            return PaycomException::response(
                $this->request_id,
                 [
                        'ru' => 'Incorrect amount.',
                        'uz' => 'Incorrect amount.',
                        'en' => 'Incorrect amount.',
                        ],
                PaycomException::ERROR_INVALID_AMOUNT
            );
        }

        // todo: Validate account, if failed throw error
        // assume, we should have order_id
        if (!isset($params->account['order_id']) || !$params->account['order_id']) {
            return PaycomException::response(
                $this->request_id,
                PaycomException::message(
                    'Неверный код заказа.',
                    'Harid kodida xatolik.',
                    'Incorrect order code.'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
                'order_id'
            );
        }

        // todo: Check is order available

        // assume, after find() $this will be populated with Order data
        switch ($code) {
            case "osago":
                $order = Osago::findOne($params->account['order_id']);
                $amount = (float)$order->amount_uzs*100;
                $amount = (float)($order->amount_uzs + $order->accident_amount) * 100;
                break;
            case "kasko":
                $order = Kasko::findOne($params->account['order_id']);
                $amount = (float)$order->amount_uzs*100;
                break;
            case "travel":
                $order = Travel::findOne($params->account['order_id']);
                $amount = (float)$order->amount_uzs*100;
                break;
            case "accident":
                $order = Accident::findOne($params->account['order_id']);
                $amount = (float)$order->amount_uzs*100;
                break;
            case "kaskobysubscriptionpolicy":
                $order = KaskoBySubscriptionPolicy::findOne($this->params->account['order_id']);
                $amount = (float)$order->amount_uzs * 100;
                break;
            default:
                echo "Error";
        }

        // Check, is order found by specified order_id
        if (!$order || !$order->id) {
            return PaycomException::response(
                $this->request_id,
                PaycomException::message(
                    'Неверный код заказа.',
                    'Harid kodida xatolik.',
                    'Incorrect order code.'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
                'order_id'
            );
        }

        // validate amount
        // convert $this->amount to coins
        // $params['amount'] already in coins

//        $amount = (float)$order->amount_uzs;

        if (round($amount) != round($params->amount)) {
            return PaycomException::response(
                $this->request_id,
                PaycomException::message(
                'Incorrect amount.',
                'Incorrect amount.',
                'Incorrect amount.'),
                PaycomException::ERROR_INVALID_AMOUNT
            );
        }

        if ($result = $this->checkOrderStatus($order, $code))
            return $result;

        $transaction = Transaction::findOne(['id' => $order->trans_id, 'payment_type' => 'payme']);

        // for example, order state before payment should be 'waiting pay'
        if ($transaction && $transaction->status != self::STATE_WAITING_PAY) {
            return PaycomException::response(
                $this->request_id,
                PaycomException::message(
                'Order state is invalid.',
                'Order state is invalid.',
                'Order state is invalid.'),
                PaycomException::ERROR_COULD_NOT_PERFORM
            );
        }

        return 1;
    }

    public function error($code, $message = null, $data = null)
    {
        return PaycomException::response($this->request_id, $message, $code, $data);
    }

    private function CheckPerformTransaction($code)
    {
        if (!isset($this->params->account['order_id']) || !$this->params->account['order_id']) {
            return PaycomException::response(
                $this->request_id,
                PaycomException::message(
                    'Неверный код заказа.',
                    'Harid kodida xatolik.',
                    'Incorrect order code.'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
                'order_id'
            );
        }

        // todo: Check is order available

        // assume, after find() $this will be populated with Order data
        switch ($code) {
            case "osago":
                $order = Osago::findOne($this->params->account['order_id']);
                $amount = (float)($order->amount_uzs + $order->accident_amount) * 100;
                break;
            case "kasko":
                $order = Kasko::findOne($this->params->account['order_id']);
                $amount = (float)$order->amount_uzs * 100;
                break;
            case "travel":
                $order = Travel::findOne($this->params->account['order_id']);
                $amount = (float)$order->amount_uzs * 100;
                break;
            case "accident":
                $order = Accident::findOne($this->params->account['order_id']);
                $amount = (float)$order->amount_uzs * 100;
                break;
            case "kaskobysubscriptionpolicy":
                $order = KaskoBySubscriptionPolicy::findOne($this->params->account['order_id']);
                $amount = (float)$order->amount_uzs * 100;
                break;
            default:
                echo "Error";
        }

        if (!$order || !$order->id) {
            return PaycomException::response(
                $this->request_id,
                PaycomException::message(
                    'Неверный код заказа.',
                    'Harid kodida xatolik.',
                    'Incorrect order code.'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
                'order_id'
            );
        }

//        $amount = (float)$order->amount_uzs;

        if (round($amount) != round($this->params->amount)) {
            return PaycomException::response(
                $this->request_id,
                PaycomException::message(
                    'Incorrect amount.',
                    'Incorrect amount.',
                    'Incorrect amount.'),
                PaycomException::ERROR_INVALID_AMOUNT
            );
        }

        if ($result = $this->checkOrderStatus($order, $code))
            return $result;

        $transaction = Transaction::findOne(['id' => $order->trans_id, 'payment_type' => 'payme']);

        // for example, order state before payment should be 'waiting pay'
        if ($transaction && $transaction->status != self::STATE_WAITING_PAY) {
            return PaycomException::response(
                $this->request_id,
                PaycomException::message(
                    'Order state is invalid.',
                    'Order state is invalid.',
                    'Order state is invalid.'),
                PaycomException::ERROR_COULD_NOT_PERFORM
            );
        }

        // todo: Check is there another active or completed transaction for this order
        /*$transaction = Transactions::findOne($order->trans_id);
        if ($transaction && ($transaction->status == self::STATE_CREATED || $transaction->status == self::STATE_COMPLETED)) {
            return $this->error(
                PaycomException::ERROR_COULD_NOT_PERFORM,
                PaycomException::message(
                'There is other active/completed transaction for this order.',
                'There is other active/completed transaction for this order.',
                'There is other active/completed transaction for this order.')
            );
        }*/

        // if control is here, then we pass all validations and checks
        // send response, that order is ready to be paid.
        return [
          "result" => [
            "allow" => true
          ]
        ];
    }

    private function CheckTransaction($code)
    {
        // todo: Find transaction by id
        $transaction = Transaction::find()->where(['trans_no' => $this->params->id, 'payment_type' => 'payme'])->one();
        if (!$transaction) {
            return $this->error(
                PaycomException::ERROR_TRANSACTION_NOT_FOUND,
                PaycomException::message(
                'Transaction not found.',
                'Transaction not found.',
                'Transaction not found.')
            );
        }

        // todo: Prepare and send found transaction
        return [
            "result" => [
                "create_time" => $transaction->create_time,
                "perform_time" => $transaction->perform_time,
                "cancel_time" => $transaction->cancel_time,
                "transaction" => (string) $transaction->trans_no,
                "state" => $transaction->status,
                "reason" => $transaction->reason
            ]
        ];
    }

    public function isExpired($transaction)
    {
        // todo: Implement transaction expiration check
        // for example, if transaction is active and passed TIMEOUT milliseconds after its creation, then it is expired
        return $transaction->status == self::STATE_CREATED && (1000 * time() - $transaction->create_time) > self::TIMEOUT;
    }

    public function checkOrderStatus($order, $code)
    {
        if ( $order
            and
            (
                ($code == "kasko" and $order->status != Kasko::STATUS['step4'])
                or
                ($code == "travel" and $order->status != Travel::STATUSES['step3'])
                or
                ($code == "osago" and $order->status != Osago::STATUS['step4'])
                or
                ($code == "kaskobysubscriptionpolicy" and ($order->status != KaskoBySubscriptionPolicy::STATUS['created'] or !in_array($order->kaskoBySubscription->status, [KaskoBySubscription::STATUS['step6'], KaskoBySubscription::STATUS['payed']])))
            )
        )
            return PaycomException::response(
                $this->request_id,
                PaycomException::message(
                    'Неверный статус заказа.',
                    'Harid statusida xatolik.',
                    'Incorrect order status.'
                ),
                PaycomException::ERROR_COULD_NOT_PERFORM,
                'order_id'
            );

        return 0;
    }

    private function CreateTransaction($code)
    {
        // validate parameters
        if($this->validate($this->params, $code) != 1)
          return $this->validate($this->params, $code);

        switch ($code) {
          case "osago":
              $order = Osago::findOne($this->params->account['order_id']);
              break;
          case "kasko":
              $order = Kasko::findOne($this->params->account['order_id']);
              break;
          case "travel":
              $order = Travel::findOne($this->params->account['order_id']);
              break;
          case "accident":
              $order = Accident::findOne($this->params->account['order_id']);
              break;
            case "kaskobysubscriptionpolicy":
                $order = KaskoBySubscriptionPolicy::findOne($this->params->account['order_id']);
                break;
          default:
              echo "Error";
        }

        if($order->trans_id) {
          $transaction = Transaction::findOne(['id' => $order->trans_id, 'payment_type' => 'payme']);
          if ($transaction) {
              if (($transaction->status == self::STATE_CREATED || $transaction->status == self::STATE_COMPLETED)
                  && $transaction->trans_no !== $this->params->id) {
                  return $this->error(
                      PaycomException::ERROR_INVALID_ACCOUNT,
                      [
                        'ru' => 'There is other active/completed transaction for this order.1',
                        'uz' => 'There is other active/completed transaction for this order.2',
                        'en' => 'There is other active/completed transaction for this order.3',
                      ],
                      'order_id'
                  );
              }
          }
        }

        // todo: Find transaction by id
        $transaction = Transaction::find()->where(['trans_no' => $this->params->id])->one();

        if ($transaction) {
            if ($transaction->status != self::STATE_CREATED) { // validate transaction state
                return $this->error(
                    PaycomException::ERROR_COULD_NOT_PERFORM,
                    PaycomException::message(
                    'Transaction found, but is not active.',
                    'Transaction found, but is not active.',
                    'Transaction found, but is not active.')
                );
            } elseif ($this->isExpired($transaction)) { // if transaction timed out, cancel it and send error
                $transaction->cancel_time = time()*1000;

                // todo: Change $state to cancelled (-1 or -2) according to the current state

                if ($transaction->status == self::STATE_COMPLETED) {
                    // Scenario: CreateTransaction -> PerformTransaction -> CancelTransaction
                    $transaction->status = self::STATE_CANCELLED_AFTER_COMPLETE;
                } else {
                    // Scenario: CreateTransaction -> CancelTransaction
                    $transaction->status = self::STATE_CANCELLED;
                }

                // set reason
                $transaction->reason = self::REASON_CANCELLED_BY_TIMEOUT;

                // todo: Update transaction on data store
                $transaction->save();

                return $this->error(
                    PaycomException::ERROR_COULD_NOT_PERFORM,
                    PaycomException::message(
                    'Transaction is expired.',
                    'Transaction is expired.',
                    'Transaction is expired.')
                );
            } else { // if transaction found and active, send it as response
                return [
                  "result" => [
                    'create_time' => $transaction->create_time,
                    'transaction' => $transaction->trans_no,
                    'state'       => $transaction->status
                  ]
                ];
            }
        } else { // transaction not found, create new one

            // validate new transaction time
            if (time()*1000 - $this->params->time >= self::TIMEOUT) {
                return $this->error(
                    PaycomException::ERROR_INVALID_ACCOUNT,
                    PaycomException::message(
                        'С даты создания транзакции прошло ' . self::TIMEOUT . 'мс',
                        'Tranzaksiya yaratilgan sanadan ' . self::TIMEOUT . 'ms o`tgan',
                        'Since create time of the transaction passed ' . self::TIMEOUT . 'ms'
                    ),
                    'time'
                );
            }

            // create new transaction
            // keep create_time as timestamp, it is necessary in response
            $create_time                        = $this->params->time;
            $transaction = new Transaction();
            $transaction->partner_id = $order->partner_id;
            $transaction->trans_no = $this->params->id;
            $transaction->amount = $this->params->amount;
            $transaction->create_time = $this->params->time;
            $transaction->trans_date = date('Y-m-d');
            $transaction->perform_time = 0;
            $transaction->cancel_time = 0;
            $transaction->status = self::STATE_CREATED;
            $transaction->payment_type = 'payme';
            $transaction->save();
            $order->trans_id = $transaction->getPrimaryKey();
            $order->save();

            // send response
            return [
              "result" => [
                'create_time' => $create_time,
                'transaction' => $transaction->trans_no,
                'state'       => $transaction->status
            ]
          ];
        }
    }

    private function PerformTransaction($code)
    {
        $transaction = Transaction::find()->where(['trans_no' => $this->params->id])->one();
        if (!$transaction) {
            return $this->error(
                PaycomException::ERROR_TRANSACTION_NOT_FOUND,
                PaycomException::message(
                'Transaction not found.',
                'Transaction not found.',
                'Transaction not found.')
            );
        }

        $order = Osago::find()->where(['trans_id' => $transaction->id])->one();
        if ($order == null)
            $order = Kasko::find()->where(['trans_id' => $transaction->id])->one();
        if ($order == null)
            $order = Travel::find()->where(['trans_id' => $transaction->id])->one();
        if ($order == null)
            $order = Accident::find()->where(['trans_id' => $transaction->id])->one();
        if ($order == null)
            $order = KaskoBySubscriptionPolicy::find()->where(['trans_id' => $transaction->id])->one();


        switch ($transaction->status) {
            case self::STATE_CREATED: // handle active transaction
                if ($this->isExpired($transaction)) { // if transaction is expired, then cancel it and send error
                  $transaction->cancel_time = time()*1000;

                  // todo: Change $state to cancelled (-1 or -2) according to the current state

                  if ($transaction->status == self::STATE_COMPLETED) {
                      // Scenario: CreateTransaction -> PerformTransaction -> CancelTransaction
                      $transaction->status = self::STATE_CANCELLED_AFTER_COMPLETE;
                  } else {
                      // Scenario: CreateTransaction -> CancelTransaction
                      $transaction->status = self::STATE_CANCELLED;
                  }

                  // set reason
                  $transaction->reason = self::REASON_CANCELLED_BY_TIMEOUT;

                  // todo: Update transaction on data store
                  $transaction->save();

                  return $this->error(
                      PaycomException::ERROR_COULD_NOT_PERFORM,
                      PaycomException::message(
                      'Transaction is expired.',
                      'Transaction is expired.',
                      'Transaction is expired.')
                  );
                } else { // perform active transaction
                    // todo: Mark order/service as completed

                    // todo: Mark transaction as completed
                $transaction->status = self::STATE_COMPLETED;
                $transaction->perform_time = time()*1000;
                $transaction->cancel_time = 0;
                $transaction->save();

                //125708395 Sardor
                //144528462 Hikmat
                //1010848584 Tigran
                //601599321 gross1
               // 636771019 gross2
                //185411276 alfalife
                //1274635762 apex
                //1402313437 euroasia
                //701117191 newlife1
                //1368730068 newlife2
                //netkost call centr
                if($order->partner_id == 1) {
                    if($code == 'travel' || $code == 'accident') $order->setGrossPolicyNumber();
                }

                    //Jobir's code begin

                    $product = str_replace("common\\models\\", "", get_class($order));

                    $order->saveAfterPayed();
                    //Jobir's code end


                if($order->partner_id == 10) {
                    Yii::$app->mailer->compose()
                        ->setTo('osago@alfainvest.uz')
                        ->setFrom('info@netkost.uz')
                        ->setTextBody(TelegramService::chatText($order))
                        ->send();
                }


                    return [
                      'result' => [
                        'transaction' => (string) $transaction->trans_no,
                        'perform_time' => $transaction->perform_time,
                        'state' => $transaction->status,
                      ]
                    ];
                }
                break;

            case self::STATE_COMPLETED: // handle complete transaction
                // todo: If transaction completed, just return it

                return [
                  'result' => [
                    'transaction' => (string) $transaction->trans_no,
                    'perform_time' => $transaction->perform_time,
                    'state' => $transaction->status,
                  ]
                ];
                break;

            default:
                // unknown situation
                return $this->error(
                    PaycomException::ERROR_COULD_NOT_PERFORM,
                    PaycomException::message(
                    'Could not perform this operation.',
                    'Could not perform this operation.',
                    'Could not perform this operation.')
                );
                break;
        }
    }

    private function CancelTransaction($code)
    {
        $transaction = Transaction::find()->where(['trans_no' => $this->params->id])->one();
        if (!$transaction) {
            return $this->error(
                PaycomException::ERROR_TRANSACTION_NOT_FOUND,
                PaycomException::message(
                'Transaction not found.',
                'Transaction not found.',
                'Transaction not found.')
            );
        }

        $url = 'https://bot.gross.uz/get-order/';

        $order = Osago::find()->where(['trans_id' => $transaction->id])->one();
        if ($order == null)
            $order = Kasko::find()->where(['trans_id' => $transaction->id])->one();
        if ($order == null)
            $order = Travel::find()->where(['trans_id' => $transaction->id])->one();
        if ($order == null)
            $order = Accident::find()->where(['trans_id' => $transaction->id])->one();
        if ($order == null)
            $order = KaskoBySubscriptionPolicy::find()->where(['trans_id' => $transaction->id])->one();


        $product = str_replace("common\\models\\", "", get_class($order));
        if ($product == "Kasko" or $product == "Travel" or $product == "Osago")
        {
            \Yii::error($order->id . " " . $product . ' ' . date('Y-m-d H:i:s') . ' ' . '(payme)', 'test');
            $order->statusToBackBeforePayment();
        }

        switch ($transaction->status) {
            // if already cancelled, just send it
            case self::STATE_CANCELLED:
                return [
                  'result' => [
                    'transaction' => $transaction->trans_no,
                    'cancel_time' => $transaction->cancel_time,
                    'state'       => $transaction->status,
                  ]
                ];
                break;
            case self::STATE_CANCELLED_AFTER_COMPLETE:
                return [
                  'result' => [
                    'transaction' => $transaction->trans_no,
                    'cancel_time' => $transaction->cancel_time,
                    'state'       => $transaction->status,
                  ]
                ];
                break;

            // cancel active transaction
            case self::STATE_CREATED:
                // cancel transaction with given reason
                $transaction->cancel_time = time()*1000;

                  // todo: Change $state to cancelled (-1 or -2) according to the current state

                  if ($transaction->status == self::STATE_COMPLETED) {
                      // Scenario: CreateTransaction -> PerformTransaction -> CancelTransaction
                      $transaction->status = self::STATE_CANCELLED_AFTER_COMPLETE;
                  } else {
                      // Scenario: CreateTransaction -> CancelTransaction
                      $transaction->status = self::STATE_CANCELLED;
                  }

                  // set reason
                  $transaction->reason = $this->params->reason;

                  // todo: Update transaction on data store
                  $transaction->save();

                // after $found->cancel(), cancel_time and state properties populated with data

                // send response
                return [
                  'result' => [
                    'transaction' => $transaction->trans_no,
                    'cancel_time' => $transaction->cancel_time,
                    'state'       => $transaction->status,
                  ]
                ];
                break;

            case self::STATE_COMPLETED:
                // todo: If cancelling after performing transaction is not possible, then return error -31007
                $transaction->cancel_time = time()*1000;

                  // todo: Change $state to cancelled (-1 or -2) according to the current state

                if ($transaction->status == self::STATE_COMPLETED) {
                    // Scenario: CreateTransaction -> PerformTransaction -> CancelTransaction
                    $transaction->status = self::STATE_CANCELLED_AFTER_COMPLETE;
                } else {
                    // Scenario: CreateTransaction -> CancelTransaction
                    $transaction->status = self::STATE_CANCELLED;
                }

                // set reason
                $transaction->reason = $this->params->reason;

                // todo: Update transaction on data store
                $transaction->save();

                // after $found->cancel(), cancel_time and state properties populated with data

                // send response
                return [
                  'result' => [
                    'transaction' => $transaction->trans_no,
                    'cancel_time' => $transaction->cancel_time,
                    'state'       => $transaction->status,
                  ]
                ];
                break;
        }
    }

    public static function RPCErrors($method)
    {
        $error = array();
        switch ($method) {

            case 'transport_error':
                $error = array(
                    'code' => -32300,
                    'message' => 'Transport Error',
                    'data' => "order_id"
                );
                break;
            case 'access_denied':
                $error = array(
                    'code' => -32504,
                    'message' => array('uz'=>'Access denied','en'=>'Access denied','ru'=>'Access denied'),
                    'data' => 'order_id'
                );
                break;
            case 'parse_error':
                $error = array(
                    'code' => -32700,
                    'message' => 'Parse Error',
                    'data' => "order_id"
                );
                break;
            case 'method_not_found': {
                $error = array(
                    'code' => -32601,
                    'message' => 'Method not found',
                    'data' => "order_id"
                );
                break;
            }
        }

        return $error;
    }

    public static function BilingErrors($method)
    {

        $error = array();

        switch ($method) {

            case 'transaction_not_found':
                $error = array(
                    'code' => -31003,
                    'message' =>
                        array(
                            'ru' => 'Transaction not found',
                            'uz' => 'Transaction not found',
                            'en' => 'Transaction not found'
                        ),
                    'data' => null
                );
                break;

            case 'unexpected_transaction_state':
                $error = array(
                    'code' => -31008,
                    'message' => array(
                        'ru' => 'Статус транзакции не позволяет выполнить операцию',
                        'uz' => 'Статус транзакции не позволяет выполнить операцию',
                        'en' => 'Статус транзакции не позволяет выполнить операцию'
                    ),
                    'data' => null
                );
                break;
            case 'incorrect_amount':
                $error = array(
                    'code' => -31001,
                    'message' => array(
                        'ru' => 'Неверная сумма заказа',
                        'uz' => 'Неверная сумма заказа',
                        'en' => 'Неверная сумма заказа'
                    ),

                    'data' => null
                );
                break;
            case 'order_not_found':
                $error = array(
                    'code' => -31050,
                    'message' => array(
                        'ru' => 'Заказ не найден',
                        'uz' => 'Заказ не найден',
                        'en' => 'Заказ не найден'
                    ),
                    'data' => 'order_id'
                );
                break;

            case 'order_available':
                $error = array(
                    'code' => -31051,
                    'message' => array(
                        'ru' => 'Не возможно выполнить операцию. Заказ ожидает оплаты или оплачен.',
                        'uz' => 'Не возможно выполнить операцию. Заказ ожидает оплаты или оплачен.',
                        'en' => 'Не возможно выполнить операцию. Заказ ожидает оплаты или оплачен.'
                    ),
                    'data' => 'order_id'
                );
                break;

            case 'order_not_canceled':
                $error = array(
                    'code' => -31007,
                    'message' => array(
                        'ru' => 'Заказ полностью выполнен и не подлежит отмене.',
                        'uz' => 'Заказ полностью выполнен и не подлежит отмене.',
                        'en' => 'Заказ полностью выполнен и не подлежит отмене.'
                    ),
                    'data' => null
                );
                break;

            case 'user_not_found':
                $error = array(
                    'code' => -31099,
                    'message' => array(
                        'ru' => 'Введеный id пользователя не найден.',
                        'uz' => 'Введеный id пользователя не найден.',
                        'en' => 'Введеный id пользователя не найден.'
                    ),
                    'data' => 'order_id'
                );
                break;
        }
        return $error;
    }

}