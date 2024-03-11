<?php

namespace frontend\models\TravelStepForms;

use common\helpers\GeneralHelper;
use common\models\Agent;
use common\models\KapitalSugurtaRequest;
use common\models\Partner;
use common\models\Product;
use common\models\Travel;
use thamtech\uuid\validators\UuidValidator;
use Yii;

class Step3Form extends \yii\base\Model
{
    public $travel_uuid;
    public $payment_variant;

    public const PAYMENT_VARIANT = [
        'PAYME' => 0,
        'CLICK' => 1,
    ];

    public function rules()
    {
        return [
            [['travel_uuid', 'payment_variant'], 'required'],
            [['travel_uuid'], 'string', 'max' => 255],
            [['travel_uuid'], UuidValidator::className()],
            ['payment_variant', 'integer', 'min' => 0, 'max' => 1],
            [['travel_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Travel::className(), 'targetAttribute' => ['travel_uuid' => 'uuid'],
                'filter' => function($query){
                    return $query->andWhere([
                        'f_user_id' => Yii::$app->user->id
                    ])->andWhere(['IN', 'status', [Travel::STATUSES['step2'], Travel::STATUSES['step3']]]);
                }
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'travel_uuid' => Yii::t('app', 'travel'),
        ];
    }

    public function send()
    {
        $language = GeneralHelper::lang_of_local();

        $travel = Travel::findOne(['uuid' => $this->travel_uuid]);

        $travel->status = Travel::STATUSES['step3'];
        $travel->insurer_phone = Yii::$app->user->identity->phone;
        $travel->step3_date = time();

        $click_service_id = GeneralHelper::env('click_service_id');
        $click_merchant_id = GeneralHelper::env('click_merchant_id');
        $payme_merchant_id = GeneralHelper::env('payme_merchant_id');

        $_id = $travel->id;
        $_uuid = $travel->uuid;
        if ($travel->partner_id == Partner::PARTNER['kapital'])
        {
            $travel->amount_uzs = $travel->getAmountUzsWithoutDiscount();
            $travel->save();

            $payment_info = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['payment_info'], $travel);
            $click_service_id = $payment_info['click']->service_id;
            $click_merchant_id = $payment_info['click']->merchant_id;
            $payme_merchant_id = $payment_info['payme']->merchant_id;

            if (empty($travel->order_id_in_gross))
            {
                $response_arr = $travel->send_save_to_partner_system(1, 0, true);
                $travel->order_id_in_gross = $response_arr['anketa_id'];
            }
            $_id = $travel->order_id_in_gross;
        }
        $amount = $travel->amount_uzs;
        $callback_url = GeneralHelper::env('front_website_url') . "/$language/travel/calculator/travel-results/$_uuid/3-step";

        $array_for_click = [
            'type' => 1,
            'order_id' => $_id
        ];
        $transaction_param = base64_encode(json_encode($array_for_click));
        if ($this->payment_variant == self::PAYMENT_VARIANT['CLICK'])
            $checkout = "https://my.click.uz/services/pay?service_id=". $click_service_id ."&merchant_id=". $click_merchant_id ."&amount=$amount&transaction_param=$transaction_param&return_url=$callback_url";

        $amount_for_payme = $amount*100;
        if ($this->payment_variant == self::PAYMENT_VARIANT['PAYME'])
            $checkout = "https://checkout.paycom.uz" . "/" . base64_encode("m=" . $payme_merchant_id . ";ac.order_id=$_id;ac.type=1;a=$amount_for_payme;l=$language;c=$callback_url");

        /** @var Agent $agent */
        if (
            $agent = Yii::$app->user->getIdentity()->agent
            and $agent_coeff = $agent->getAgentProductCoeffs()->andWhere(['product_id' => Product::products['travel']])->one()
        )
            $travel->agent_amount = round($agent_coeff->coeff * $travel->amount_uzs / 100);

        $travel->save();

        return [
            'checkout' => $checkout
        ];
    }
}