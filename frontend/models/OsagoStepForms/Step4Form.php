<?php

namespace frontend\models\OsagoStepForms;

use common\helpers\GeneralHelper;
use common\models\Accident;
use common\models\HamkorpayRequest;
use common\models\KapitalSugurtaRequest;
use common\models\Osago;
use common\models\Partner;
use common\models\SavedCard;
use frontend\controllers\PayzeController;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class Step4Form extends \yii\base\Model
{
    public $osago_uuid;
    public $payment_variant;
    public $card_id;
    public $card_number;
    public $card_expiry;

    public const PAYMENT_VARIANT = [
        'PAYME' => 0,
        'CLICK' => 1,
//        'PAYZE' => 2,
        'HAMKOR_PAY' => 3
    ];

    public const ERROR_CODE = [
        'already_payed' => 1
    ];

    public function rules()
    {
        return [
            [['osago_uuid', 'payment_variant'], 'required'],
            [['osago_uuid'], 'string', 'max' => 255],
            [['osago_uuid'], UuidValidator::className()],
            [['card_id'], 'integer'],
            [['osago_uuid', 'card_id'], 'default', 'value' => null],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid'],
                'filter' => function($query){
                    return $query
                        ->andWhere(['not in', 'status', [
                            Osago::STATUS['step1'], Osago::STATUS['step2']
                        ]]);
                }
            ],
            ['payment_variant', 'in', 'range' => self::PAYMENT_VARIANT],
            [['card_id'], 'exist', 'skipOnError' => true, 'targetClass' => SavedCard::className(), 'targetAttribute' => ['card_id' => 'id'],
                'filter' => function($query){
                    return $query->andWhere([
                        'f_user_id' => Yii::$app->user->id
                    ])->andWhere(['IN', 'status', [SavedCard::STATUS['saved']]]);
                }
            ],
            [['card_number', 'card_expiry'], 'required', 'when' => function ($model) {
                return in_array($model->payment_variant, [self::PAYMENT_VARIANT['HAMKOR_PAY']]);
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'osago_uuid' => Yii::t('app', 'osago_uuid'),
            'payment_variant' => Yii::t('app', 'payment_variant'),
            'card_id' => Yii::t('app', 'card_id'),
        ];
    }

    public function save()
    {
        $language = GeneralHelper::lang_of_local();

        $osago = Osago::findOne(['uuid' => $this->osago_uuid]);

        if (!$osago->is_juridic and Yii::$app->user->id != $osago->f_user_id)
            throw new BadRequestHttpException('This osago is not yours');

        $osago->f_user_id = Yii::$app->user->id;
        $osago->save();

        if (in_array($osago->status, [Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']]))
            throw new BadRequestHttpException(Yii::t('app', "Siz allaqachon pul to'lab bo'ldingiz"), self::ERROR_CODE['already_payed']);

        $click_service_id = GeneralHelper::env('click_service_id');
        $click_merchant_id = GeneralHelper::env('click_merchant_id');
        $payme_merchant_id = GeneralHelper::env('payme_merchant_id');
        $_id = $osago->id;
        if ($osago->partner_id == Partner::PARTNER['kapital'])
        {
            $osago->amount_uzs = $osago->getAmountUzsWithoutDiscount();
            $osago->accident_amount = $osago->getAccidentAmountWithoutDiscount();
            $osago->save();

            $payment_info = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['payment_info'], $osago);
            $click_service_id = $payment_info['click']->service_id;
            $click_merchant_id = $payment_info['click']->merchant_id;
            $payme_merchant_id = $payment_info['payme']->merchant_id;
            if (empty($osago->order_id_in_gross))
            {
                $response_arr = $osago->create_osago_in_partner_system();
                $osago->order_id_in_gross = $response_arr['anketa_id'];
                if (!empty($response_arr['doc_anketa_id']))
                {
                    $accident = (new Accident())->save_accident_from_osago($osago, []);
                    $accident->order_id_in_gross = $response_arr['doc_anketa_id'];
                    $accident->amount_uzs = $response_arr['doc_prem'];
                    $accident->save();
                }
            }
            $_id = $osago->order_id_in_gross;
        }

        $osago->status = Osago::STATUS['step4'];
        $amount = $osago->amount_uzs + $osago->accident_amount;
        $callback_url = GeneralHelper::env('front_website_url') . "/$language/osago/done?id=" . $osago->uuid;
        if (!empty($osago->bridge_company_id))
            $callback_url .= "&road24=true";

        $array_for_click = [
            'type' => 2,
            'order_id' => $_id
        ];
        $transaction_param = base64_encode(json_encode($array_for_click));
        if ($this->payment_variant == self::PAYMENT_VARIANT['CLICK'])
        {
            $checkout = "https://my.click.uz/services/pay?service_id=". $click_service_id ."&merchant_id=". $click_merchant_id ."&amount=$amount&transaction_param=$transaction_param&return_url=$callback_url";
            if ($osago->partner_id == Partner::PARTNER['kapital'])
                $checkout = "https://my.click.uz/services/pay?service_id=". $click_service_id ."&merchant_id=". $click_merchant_id ."&amount=$amount&transaction_param=$_id&return_url=$callback_url";
        }
        $amount_for_payme = $amount*100;
//        VarDumper::dump("m=" . $payme_merchant_id . ";ac.order_id=$_id;ac.type=2;a=$amount_for_payme;l=$language;c=$callback_url");
        if ($this->payment_variant == self::PAYMENT_VARIANT['PAYME'])
            $checkout = GeneralHelper::env('payme_url') . "/" . base64_encode("m=" . $payme_merchant_id . ";ac.order_id=$_id;ac.type=2;a=$amount_for_payme;l=$language;c=$callback_url");

//        if ($this->payment_variant == self::PAYMENT_VARIANT['PAYZE'])
//            $checkout = is_null($this->card_id) ? PayzeController::saveCard($callback_url, $osago) : PayzeController::payWithCard($callback_url, $osago, $this->card_id);

        if ($this->payment_variant == self::PAYMENT_VARIANT['HAMKOR_PAY'])
        {
            if ($osago->partner_id == Partner::PARTNER['kapital'])
                throw new BadRequestHttpException('You can not choose hamkorpay for kapital policy');

            $checkout = "";
            $hamkorpay_request = HamkorpayRequest::sendRequest('pay.create', $osago, [
                'external_id' => (string)$osago->id,
                'amount' => $amount * 100,
                'currency_code' => "860",
                'card' => [
                    'number' => $this->card_number,
                    'expiry' => $this->card_expiry,
                ],
                'details' => []
            ]);
        }

        $osago->save();

        return $checkout;
    }
}