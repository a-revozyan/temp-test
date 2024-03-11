<?php

namespace frontend\models\KaskoBySubscriptionStepForms;

use common\models\KaskoBySubscription;
use common\models\Product;
use common\models\Promo;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class SetPromoForm extends \yii\base\Model
{
    public $kasko_by_subscription_uuid;
    public $promo_code;

    public function rules()
    {
        return [
            [['kasko_by_subscription_uuid'], 'required'],
            [['kasko_by_subscription_uuid'], 'string', 'max' => 255],
            [['kasko_by_subscription_uuid'], UuidValidator::className()],
            [['promo_code'], 'safe'],
            [['kasko_by_subscription_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoBySubscription::className(), 'targetAttribute' => ['kasko_by_subscription_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['in', 'status', [
                        KaskoBySubscription::STATUS['step4'], KaskoBySubscription::STATUS['step5'],
                    ]]);
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'kasko_by_subscription_uuid' => Yii::t('app', 'kasko_by_subscription_uuid'),
            'promo_code' => Yii::t('app', 'promo code'),
        ];
    }

    public function save()
    {
        $kbs = KaskoBySubscription::findOne(['uuid' => $this->kasko_by_subscription_uuid]);

        if (is_null($this->promo_code))
        {
            $kbs->amount_uzs = $kbs->amount_uzs + $kbs->promo_amount;
            $kbs->promo_amount = null;
            $kbs->promo_id = null;
            $kbs->promo_percent = null;
            $kbs->save();

            return $kbs;
        }

        $promo = Promo::findOne(['code' => $this->promo_code, 'status' => Promo::STATUS['active']]);

        if (is_null($promo) or !in_array(Product::products['kasko-by-subscription'], ArrayHelper::getColumn($promo->products, 'id')))
            throw new BadRequestHttpException(Yii::t('app', 'Xato Promocode kiritdingiz'));

        if (!empty($kbs->promo_id))
            throw new BadRequestHttpException(Yii::t('app', 'This kasko by subscription order used promo code already'));
        if ($promo->number < 1)
            throw new BadRequestHttpException(Yii::t('app', 'All promocodes is already used'));
        if (!is_null($promo->begin_date) and (date_create_from_format('Y-m-d', $promo->begin_date)->getTimestamp() > time()))
            throw new BadRequestHttpException(Yii::t('app', 'The term of use of promo code has not started'));
        if (!is_null($promo->end_date) and (date_create_from_format('Y-m-d', $promo->end_date)->getTimestamp() < time()))
            throw new BadRequestHttpException(Yii::t('app', 'The use of promo code has expired'));

        $kbs->promo_id = $promo->id;
        $amount_without_margin = $kbs->amount_uzs;

        if ($promo->amount_type == Promo::AMOUNT_TYPE['percent'])
        {
            $promo_amount = ($amount_without_margin * $promo->amount/100);
            $kbs->amount_uzs += $promo_amount;
            $kbs->promo_percent = $promo->amount;
            $kbs->promo_amount = abs($promo_amount);
        }
        if ($promo->amount_type == Promo::AMOUNT_TYPE['fixed'])
        {
            $kbs->amount_uzs += $promo->amount;
            $kbs->promo_amount = abs($promo->amount);
        }
        $promo->number -= 1;
        $promo->save();

        $kbs->save();

        return $kbs;
    }
}
