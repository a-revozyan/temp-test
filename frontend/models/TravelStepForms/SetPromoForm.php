<?php

namespace frontend\models\TravelStepForms;

use common\models\Autocomp;
use common\models\BridgeCompany;
use common\models\Currency;
use common\models\Kasko;
use common\models\Product;
use common\models\Promo;
use common\models\Travel;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class SetPromoForm extends \yii\base\Model
{
    public $promo_code;
    public $travel_uuid;

    public function rules()
    {
        return [
            [['promo_code', 'travel_uuid'], 'required'],
            [['travel_uuid'], 'string', 'max' => 255],
            [['travel_uuid'], UuidValidator::className()],
            [['travel_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Travel::className(),
                'targetAttribute' => ['travel_uuid' => 'uuid'],
                'filter' => function($query){
                    return $query
                        ->andWhere(['in', 'status', [
                            Travel::STATUSES['step2'], Travel::STATUSES['step3'],
                        ]]);
                }
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'promo_code' => Yii::t('app', 'promo code'),
        ];
    }

    public function save()
    {
        $promo = Promo::findOne(['code' => $this->promo_code, 'status' => Promo::STATUS['active']]);
        if (is_null($promo) or !in_array(Product::products['travel'], ArrayHelper::getColumn($promo->products, 'id')))
            throw new BadRequestHttpException(Yii::t('app', 'Xato Promocode kiritdingiz'));

        $promo = Promo::findOne(['code' => $this->promo_code]);
        $travel = Travel::find()->where(['uuid' => $this->travel_uuid])->one();

        if (!empty($travel->promo_id))
            throw new BadRequestHttpException(Yii::t('app', 'This travel order used promo code already'));
        if ($promo->number < 1)
            throw new BadRequestHttpException(Yii::t('app', 'All promocodes is already used'));
        if (!is_null($promo->begin_date) and (date_create_from_format('Y-m-d', $promo->begin_date)->getTimestamp() >= time()))
            throw new BadRequestHttpException(Yii::t('app', 'The term of use of promo code has not started'));
        if (!is_null($promo->end_date) and (date_create_from_format('Y-m-d', $promo->end_date)->getTimestamp() <= time()))
            throw new BadRequestHttpException(Yii::t('app', 'The use of promo code has expired'));

        $travel->promo_id = $promo->id;
        $amount_without_margin = $travel->amount_uzs;
        $usd = Currency::getUsdRate();
        if ($promo->amount_type == Promo::AMOUNT_TYPE['percent'])
        {
            $travel->amount_uzs *= (100+$promo->amount)/100;
            $travel->amount_usd = round($travel->amount_usd * (100+$promo->amount)/100, 2);
            $travel->promo_percent = $promo->amount;
            $travel->promo_amount = $amount_without_margin - $travel->amount_uzs;
        }
        if ($promo->amount_type == Promo::AMOUNT_TYPE['fixed'])
        {
            $travel->amount_uzs += $promo->amount;
            $travel->amount_usd = round($travel->amount_usd + $promo->amount / $usd, 2);
            $travel->promo_amount = $promo->amount;
        }
        $promo->number -= 1;
        $promo->save();

        $travel->save();

        return $travel;
    }
}