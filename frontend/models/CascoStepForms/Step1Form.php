<?php

namespace frontend\models\CascoStepForms;

use common\models\Autocomp;
use common\models\BridgeCompany;
use common\models\Kasko;
use common\models\KaskoTariff;
use common\models\Partner;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class Step1Form extends \yii\base\Model
{
    public $autocomp_id;
    public $year;
    public $price;
    public $tariff_id;
    public $bridge_company_code;

    public function rules()
    {
        return [
            [['autocomp_id', 'year', 'price', 'tariff_id'], 'required'],
            [['autocomp_id', 'year', 'price', 'tariff_id'], 'integer'],
            ['price', 'betweenMinMaxPrice'],
            [['autocomp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Autocomp::className(), 'targetAttribute' => ['autocomp_id' => 'id']],
            [['bridge_company_code'], 'exist', 'skipOnError' => true, 'targetClass' => BridgeCompany::className(), 'targetAttribute' => ['bridge_company_code' => 'code'], 'filter' => ['status' => 10]],
            [['year'], 'integer', 'min' => 2011, 'max' => date('Y')],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoTariff::className(), 'targetAttribute' => ['tariff_id' => 'id']]
        ];
    }

    public function betweenMinMaxPrice($attribute, $params, $validator)
    {
        $autocomp = Autocomp::findOne($this->autocomp_id);
        $min_price_of_auto = 30000000;

        if ($autocomp != null)
            $price_of_car = Kasko::getAutoRealPrice($autocomp->price, $this->year);

        if ($autocomp != null and $this->$attribute < $min_price_of_auto || $this->$attribute > $price_of_car)
            $this->addError('price', Yii::t('app', "Tanlangan narx {min_price_of_auto}! so'mdan dan katta va {price_of_car}! so'mdan dan kichkina bo'lishi shart", [
                'min_price_of_auto' => $min_price_of_auto,
                'price_of_car' => $price_of_car
            ]));

    }

    public function attributeLabels()
    {
        return [
            'autocomp_id' => Yii::t('app', 'autocomp'),
            'year' => Yii::t('app', 'year'),
            'price' => Yii::t('app', 'price'),
            'tariff_id' => Yii::t('app', 'tariff'),
            'bridge_company_code' => Yii::t('app', 'bridge company code'),
        ];
    }

    /**
     * @return array|ActiveRecord|BadRequestHttpException
     */
    public function save()
    {
        $tariff = KaskoTariff::findOne(['id' => $this->tariff_id]);
        if (!$partner = Partner::findOne(['id' => $tariff->partner_id]))
            throw new NotFoundHttpException(Yii::t('app', 'Partner topilmadi'));

        $kasko = new Kasko();
        $kasko->setAttributes($this->attributes);
        $kasko->f_user_id = Yii::$app->user->id;
        $kasko->partner_id = $partner->id;
        $kasko->status = Kasko::STATUS['step1'];
        $kasko->created_at = time();
        $kasko->bridge_company_id = BridgeCompany::findOne(['code' => $this->bridge_company_code])->id ?? null;

        //amount_uzs uchun cacl-kasko dan cho'chirdim
        $model = new Kasko();
        $model->autocomp_id = $this->autocomp_id;
        $model->year = $this->year;
        $model->price_coeff = 1;
        $model->tariff_id = $this->tariff_id;
        $tariffs = $model->calc2($this->price, $tariff->is_islomic, Autocomp::findOne($this->autocomp_id)->automodel->auto_risk_type_id);

        if (empty($tariffs))
            return new BadRequestHttpException(Yii::t('app', "Noto'g'ri tariff tanlandi"));

        $kasko->amount_uzs = $tariffs[0]['amount'];
        $kasko->amount_usd = $tariffs[0]['amount_usd'];
        //amount_uzs uchun cacl-kasko dan cho'chirdim

        $kasko->save();

        return Kasko::find()->with(['autocomp.automodel.autobrand'])->where(['id' => $kasko->id])->one();
    }
}