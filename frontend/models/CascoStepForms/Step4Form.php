<?php

namespace frontend\models\CascoStepForms;

use common\helpers\GeneralHelper;
use common\models\Agent;
use common\models\HamkorpayRequest;
use common\models\Kasko;
use common\models\KaskoRisk;
use common\models\OldKaskoRisk;
use common\models\Product;
use common\models\SavedCard;
use common\models\User;
use common\models\ZoodpayRequest;
use frontend\controllers\PayzeController;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\web\BadRequestHttpException;

class Step4Form extends \yii\base\Model
{
    public $kasko_uuid;
    public $payment_variant;
    public $card_id;
    public $first_name;
    public $card_number;
    public $card_expiry;
    public $save_card;

    public const PAYMENT_VARIANT = [
        'PAYME' => 0,
        'CLICK' => 1,
        'PAYZE' => 2,
        'ZOOD_PAY' => 3,
        'PAYME_SUBSCRIBE' => 4,
        'HAMKOR_PAY' => 5,
    ];

    public const ERROR_CODE = [
        'already_payed' => 1
    ];

    public function rules()
    {
        return [
            [['kasko_uuid', 'payment_variant'], 'required'],
            [['card_id'], 'integer'],
            [['kasko_uuid', 'card_id'], 'default', 'value' => null],
            ['payment_variant', 'in', 'range' => self::PAYMENT_VARIANT],
            [['kasko_uuid'], UuidValidator::className()],
            [['kasko_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Kasko::className(), 'targetAttribute' => ['kasko_uuid' => 'uuid'],
                'filter' => function($query){
                    return $query->andWhere([
                        'f_user_id' => Yii::$app->user->id
                    ])->andWhere(['NOT IN', 'status', [Kasko::STATUS['step1'], Kasko::STATUS['step2']]]);
                }
            ],
            [['card_id'], 'exist', 'skipOnError' => true, 'targetClass' => SavedCard::className(), 'targetAttribute' => ['card_id' => 'id'],
                'filter' => function($query){
                    return $query->andWhere([
                        'f_user_id' => Yii::$app->user->id
                    ])->andWhere(['IN', 'status', [SavedCard::STATUS['saved']]]);
                }
            ],
            ['first_name', 'required', 'when' => function ($model) {
                return $model->payment_variant == self::PAYMENT_VARIANT['ZOOD_PAY'];
            }],
            [['card_number', 'card_expiry'], 'required', 'when' => function ($model) {
                return in_array($model->payment_variant, [ self::PAYMENT_VARIANT['PAYME_SUBSCRIBE'], self::PAYMENT_VARIANT['HAMKOR_PAY']]);
            }],
            [['save_card'], 'boolean'],
            [['save_card'], 'default', 'value' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'kasko_uuid' => Yii::t('app', 'kasko'),
            'card_id' => Yii::t('app', 'card_id'),
        ];
    }

    public function send()
    {
        $language = GeneralHelper::lang_of_local();

        $casco = Kasko::findOne(['uuid' => $this->kasko_uuid]);

        if (in_array($casco->status, [Kasko::STATUS['payed'], Kasko::STATUS['attached'], Kasko::STATUS['processed'], Kasko::STATUS['policy_generated']]))
            throw new BadRequestHttpException(Yii::t('app', "Siz allaqachon pul to'lab bo'ldingiz"), self::ERROR_CODE['already_payed']);

        $casco->status = Kasko::STATUS['step4'];
        $casco->step4_date = time();

        $_id = $casco->id;
        $_uuid = $casco->uuid;
        $amount = $casco->amount_uzs;
        $callback_url = GeneralHelper::env('front_website_url') . "/$language/kasko/calculator/casco-results/$_uuid/done";

        $array_for_click = [
            'type' => 3,
            'order_id' => $_id
        ];
        $transaction_param = base64_encode(json_encode($array_for_click));
        if ($this->payment_variant == self::PAYMENT_VARIANT['CLICK'])
            $checkout = "https://my.click.uz/services/pay?service_id=" . GeneralHelper::env('click_service_id') . "&merchant_id=" . GeneralHelper::env('click_merchant_id') . "&amount=$amount&transaction_param=$transaction_param&return_url=$callback_url";

        $amount_for_payme = $amount*100;
        if ($this->payment_variant == self::PAYMENT_VARIANT['PAYME'])
            $checkout = GeneralHelper::env('payme_url') . "/" . base64_encode("m=" . GeneralHelper::env('payme_merchant_id') . ";ac.order_id=$_id;ac.type=3;a=$amount_for_payme;l=$language;c=$callback_url");

        if ($this->payment_variant == self::PAYMENT_VARIANT['PAYZE'])
            $checkout = is_null($this->card_id) ? PayzeController::saveCard($callback_url, $casco) : PayzeController::payWithCard($callback_url, $casco, $this->card_id);

        if ($this->payment_variant == self::PAYMENT_VARIANT['ZOOD_PAY'])
        {
            /** @var User $user */
            $user = Yii::$app->user->identity;
            $user->first_name = $this->first_name;
            $user->save();
            $checkout = ZoodpayRequest::sendRequest(ZoodpayRequest::REQUEST['create_transaction'], $casco->id, Kasko::className(), true, $callback_url)['payment_url'];
        }

//        $payme_subscribe_request = null;
//        if ($this->payment_variant == self::PAYMENT_VARIANT['PAYME_SUBSCRIBE'])
//        {
//            $checkout = "";
//            $payme_subscribe_request = PaymeSubscribeRequest::sendRequest(
//                PaymeSubscribeRequest::METHODS['card_create'],
//                [
//                    'card' => ['number' => $this->card_number, 'expire' => $this->card_expiry],
//                    'save' => $this->save_card,
//                ],
//                Kasko::className(),
//                $casco->id
//            );
//        }

        if ($this->payment_variant == self::PAYMENT_VARIANT['HAMKOR_PAY'])
        {
            $checkout = "";
            $hamkorpay_request = HamkorpayRequest::sendRequest('pay.create', $casco, [
                'external_id' => (string)$casco->id,
                'amount' => $casco->amount_uzs * 100,
                'currency_code' => "860",
                'card' => [
                    'number' => $this->card_number,
                    'expiry' => $this->card_expiry,
                ],
                'details' => []
            ]);
        }

        $casco->save();

        //risklar o'zgarib ketsa tarix o'zgarmasligi uchun saqlab qo'yilyapti.
        $tariff = $casco->tariff;
        $attr_tariff = [];
        foreach ($tariff->getAttributes() as $key => $value) {
            $attr_tariff["tariff_" . $key] = $value;
        }

        unset($attr_tariff['tariff_file']);
        $risks = $tariff->kaskoRisks;
        $casco->unlinkAll('oldKaskoRisk', true);
        foreach ($risks as $risk) {
            $risk = KaskoRisk::findOne($risk['id']);
            $attr_risk = $risk->getAttributes();
            unset($attr_risk['id']);

            $attr_old_kasko_risk = array_merge($attr_risk, $attr_tariff);
            $attr_old_kasko_risk['kasko_risk_id'] = $risk->id;
            $old_kasko_risk = OldKaskoRisk::find()->where($attr_old_kasko_risk)->one();
            if (is_null($old_kasko_risk))
            {
                $old_kasko_risk = new OldKaskoRisk($attr_old_kasko_risk);
                $old_kasko_risk->save();
            }

            $casco->link('oldKaskoRisk', $old_kasko_risk);
        }
        //risklar o'zgarib ketsa tarix o'zgarmasligi uchun saqlab qo'yilyapti.

        //auto lar o'zgarib ketsa tarix o'zgarmasligi uchun saqlab qolinyapti
        $casco->autobrand_name = $casco->autocomp->automodel->autobrand->name;
        $casco->automodel_name = $casco->autocomp->automodel->name;
        $casco->autocomp_name = $casco->autocomp->name;
        //auto lar o'zgarib ketsa tarix o'zgarmasligi uchun saqlab qolinyapti

        /** @var Agent $agent */
        if (
            $agent = Yii::$app->user->getIdentity()->agent
            and $agent_coeff = $agent->getAgentProductCoeffs()->andWhere(['product_id' => Product::products['kasko']])->one()
        )
            $casco->agent_amount = round($agent_coeff->coeff * $casco->amount_uzs / 100);

        $casco->save();

        return [
            'casco' => $casco->getShortArr(),
            'checkout' => $checkout,
//            'payme_subscribe_request' => $payme_subscribe_request
//            'hamkorpay_request' => $hamkorpay_request
        ];
    }
}