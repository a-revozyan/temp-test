<?php

namespace frontend\models\OsagoStepForms;

use common\models\Osago;
use common\models\Partner;
use common\models\Product;
use common\models\Promo;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class SetPromoForm extends \yii\base\Model
{
    public $osago_uuid;
    public $promo_code;

    public function rules()
    {
        return [
            [['osago_uuid'], 'required'],
            [['osago_uuid'], 'string', 'max' => 255],
            [['osago_uuid'], UuidValidator::className()],
            [['promo_code'], 'safe'],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['in', 'status', [
                        Osago::STATUS['step3'], Osago::STATUS['step4'],
                    ]]);
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'osago_uuid' => Yii::t('app', 'osago_uuid'),
            'promo_code' => Yii::t('app', 'promo code'),
        ];
    }

    public function save()
    {
        $osago = Osago::findOne(['uuid' => $this->osago_uuid]);
        if ($osago->partner_id == Partner::PARTNER['kapital'])
            throw new BadRequestHttpException("Kapital sug'urta uchun promokod amal qilmaydi");

        if (is_null($this->promo_code))
        {
            $osago->amount_uzs = $osago->amount_uzs + $osago->promo_amount;
            $osago->promo_amount = null;
            $osago->promo_id = null;
            $osago->promo_percent = null;
            $osago->save();

            return $osago;
        }

        $promo = Promo::findOne(['code' => $this->promo_code, 'status' => Promo::STATUS['active'], 'type' => [Promo::TYPE['simple'], null]]);
        if (is_null($promo) or !in_array(Product::products['osago'], ArrayHelper::getColumn($promo->products, 'id')))
            throw new BadRequestHttpException(Yii::t('app', 'Xato Promocode kiritdingiz'));

        if (!empty($osago->promo_id))
            throw new BadRequestHttpException(Yii::t('app', 'This osago order used promo code already'));
        if ($promo->number < 1)
            throw new BadRequestHttpException(Yii::t('app', 'All promocodes is already used'));
        if (!is_null($promo->begin_date) and (date_create_from_format('Y-m-d H:i:s', $promo->begin_date . " 00:00:00")->getTimestamp() >= time()))
            throw new BadRequestHttpException(Yii::t('app', 'The term of use of promo code has not started'));
        if (!is_null($promo->end_date) and (date_create_from_format('Y-m-d H:i:s', $promo->end_date . " 23:59:59")->getTimestamp() <= time()))
            throw new BadRequestHttpException(Yii::t('app', 'The use of promo code has expired'));

        $osago = $this->setPromo($osago, $promo);

        return $osago;
    }

    public function setPromo($osago, $promo)
    {
        $osago->promo_id = $promo->id;
        $amount_without_margin = $osago->amount_uzs + $osago->accident_amount;

        if ($promo->amount_type == Promo::AMOUNT_TYPE['percent'])
        {
            $promo_amount = ($amount_without_margin * $promo->amount/100);
            $osago->amount_uzs += $promo_amount;
            $osago->promo_percent = $promo->amount;
            $osago->promo_amount = abs($promo_amount);
        }
        if ($promo->amount_type == Promo::AMOUNT_TYPE['fixed'])
        {
            $osago->amount_uzs += $promo->amount;
            $osago->promo_amount = abs($promo->amount);
        }
        $promo->number -= 1;
        $promo->save();

        $osago->save();

        return $osago;
    }
}
