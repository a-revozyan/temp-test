<?php

namespace frontend\models\CascoStepForms;


use common\models\Currency;
use common\models\Kasko;
use common\models\Product;
use common\models\Promo;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class SetPromoForm extends \yii\base\Model
{
    public $promo_code;
    public $kasko_uuid;

    public function rules()
    {
        return [
            [['promo_code', 'kasko_uuid'], 'required'],
            [['promo_code'], 'exist', 'skipOnError' => true, 'targetClass' => Promo::className(), 'targetAttribute' => ['promo_code' => 'code'],
                'filter' => [
                    'status' => Promo::STATUS['active']
                ]],
            ['kasko_uuid', UuidValidator::className()],
            [['kasko_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Kasko::className(),
                'targetAttribute' => ['kasko_uuid' => 'uuid'],
                'filter' => function($query){
                    return $query
                        ->andWhere(['in', 'status', [
                            Kasko::STATUS['step3'], Kasko::STATUS['step4'],
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
        $promo = Promo::findOne(['code' => $this->promo_code]);
        $kasko = Kasko::find()->with(['autocomp.automodel.autobrand'])->where(['uuid' => $this->kasko_uuid])->one();
        if (!in_array(Product::products['kasko'], ArrayHelper::getColumn($promo->products, 'id')))
            throw new BadRequestHttpException(Yii::t('app', 'Xato Promocode kiritdingiz'));

        if (!empty($kasko->promo_id))
            throw new BadRequestHttpException(Yii::t('app', 'This kakso order used promo code already'));
        if ($promo->number < 1)
            throw new BadRequestHttpException(Yii::t('app', 'All promocodes is already used'));
        if (!is_null($promo->begin_date) and (date_create_from_format('Y-m-d H:i:s', $promo->begin_date . " 00:00:00")->getTimestamp() >= time()))
            throw new BadRequestHttpException(Yii::t('app', 'The term of use of promo code has not started'));
        if (!is_null($promo->end_date) and (date_create_from_format('Y-m-d H:i:s', $promo->end_date . " 23:59:59")->getTimestamp() <= time()))
            throw new BadRequestHttpException(Yii::t('app', 'The use of promo code has expired'));

        $kasko->promo_id = $promo->id;
        $amount_without_margin = $kasko->amount_uzs;
        $usd = Currency::getUsdRate();
        if ($promo->amount_type == Promo::AMOUNT_TYPE['percent'])
        {
            $kasko->amount_uzs *= (100+$promo->amount)/100;
            $kasko->amount_usd = round($kasko->amount_usd * (100+$promo->amount)/100, 2);
            $kasko->promo_percent = $promo->amount;
            $kasko->promo_amount = $amount_without_margin - $kasko->amount_uzs;
        }
        if ($promo->amount_type == Promo::AMOUNT_TYPE['fixed'])
        {
            $kasko->amount_uzs += $promo->amount;
            $kasko->amount_usd = round($kasko->amount_usd + $promo->amount / $usd, 2);
            $kasko->promo_amount = $promo->amount;
        }
        $promo->number -= 1;
        $promo->save();

        $kasko->save();

        return $kasko;
    }
}