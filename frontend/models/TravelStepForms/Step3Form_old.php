<?php

namespace frontend\models\TravelStepForms;

use common\helpers\GeneralHelper;
use common\models\Agent;
use common\models\Kasko;
use common\models\Product;
use common\models\Travel;
use common\models\Warehouse;
use Yii;
use yii\web\NotFoundHttpException;

class Step3Form_old extends \yii\base\Model
{
    public $travel_id;
    public $payment_variant;

    public const PAYMENT_VARIANT = [
        'PAYME' => 0,
        'CLICK' => 1,
    ];

    public function rules()
    {
        return [
            [['travel_id', 'payment_variant'], 'required'],
            ['travel_id', 'integer'],
            ['payment_variant', 'integer', 'min' => 0, 'max' => 1],
            [['travel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Travel::className(), 'targetAttribute' => ['travel_id' => 'id'],
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
            'travel_id' => Yii::t('app', 'travel'),
        ];
    }

    public function send()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $language = $headers->get('accept-language');

        $travel = Travel::findOne(['id' => $this->travel_id]);

        //product_id => 3 => travel
        //status => 0 => new 1 => reserved, 2=>payed
        $warehouse = Warehouse::findOne(['partner_id' => $travel->partner_id, 'product_id' => 3, 'status' => 0]);
        if ($travel->warehouse and $travel->warehouse->status == 1)
            $warehouse = $travel->warehouse;

        if ($warehouse == null)
            throw new NotFoundHttpException(Yii::t('app', 'warehouse not found'));

        $warehouse->status = "1";

        $travel->status = Travel::STATUSES['step3'];
        $travel->step3_date = time();
        $travel->warehouse_id = $warehouse->id;

        $_id = $travel->id;
        $amount = $travel->amount_uzs;
        $callback_url = GeneralHelper::env('front_website_url') . "/kasko/calculator/casco-results/$_id";

        $array_for_click = [
            'type' => 1,
            'order_id' => $_id
        ];
        $transaction_param = base64_encode(json_encode($array_for_click));
        if ($this->payment_variant == self::PAYMENT_VARIANT['CLICK'])
            $checkout = "https://my.click.uz/services/pay?service_id=". GeneralHelper::env('click_service_id') ."&merchant_id=". GeneralHelper::env('click_merchant_id') ."&amount=$amount&transaction_param=$transaction_param&return_url=$callback_url";

        $amount_for_payme = $amount*100;
        if ($this->payment_variant == self::PAYMENT_VARIANT['PAYME'])
            $checkout = GeneralHelper::env('payme_url') . "/" . base64_encode("m=" . GeneralHelper::env('payme_merchant_id') . ";ac.order_id=$_id;ac.type=1;a=$amount_for_payme;l=$language;c=$callback_url");

        $warehouse->save();

        /** @var Agent $agent */
        if (
            $agent = Yii::$app->user->getIdentity()->agent
            and $agent_coeff = $agent->getAgentProductCoeffs()->andWhere(['product_id' => Product::products['travel']])->one()
        )
            $travel->agent_amount = round($agent_coeff->coeff * $travel->amount_uzs / 100);

        $travel->save();

        return [
            'travel' => $travel,
            'checkout' => $checkout
        ];
    }
}