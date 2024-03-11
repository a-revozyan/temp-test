<?php

namespace frontend\models\KaskoapiForms;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\Kasko;
use common\models\OldKaskoRisk;
use Yii;

class DataOfPolicyForm extends \yii\base\Model
{
    public $kaskoUuid;

    public function rules()
    {
        return [
            [['kaskoUuid'], 'required'],
            [['kaskoUuid'], 'string'],
            [['kaskoUuid'], 'exist', 'skipOnError' => true, 'targetClass' => Kasko::className(), 'targetAttribute' => ['kaskoUuid' => 'id'], 'filter' => function($query){
                return $query->where(['status' => Kasko::STATUS['policy_generated']]);
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'kaskoUuid' => Yii::t('app', 'kaskoUuid'),
        ];
    }

    public function data()
    {
        $lang = GeneralHelper::lang_of_local();
        $kasko = Kasko::findOne(['uuid' => $this->kaskoUuid]);
        return [
            'partner_name' => $kasko->partner->name,
            'insurer_passport_series' => $kasko->insurer_passport_series,
            'insurer_passport_number' => $kasko->insurer_passport_number,
            'autonumber' => $kasko->autonumber,
            'tariff_name' => $kasko->tariff->name,
            'insurer_name' => $kasko->insurer_name,
            'autocomp' => $kasko->autocomp->name,
            'year' => $kasko->year,
            'automodel' => $kasko->autocomp->automodel->name,
            'product' => Yii::t('app', 'kasko'),
            'begin_date' => DateHelper::date_format($kasko->begin_date, 'Y-m-d', 'd/m/Y'),
            'end_date' => DateHelper::date_format($kasko->end_date, 'Y-m-d', 'd/m/Y'),
//            'tariff_risks' => $kasko->tariff->kaskoRisks,
            'tariff_risks' => OldKaskoRisk::getShortArrCollection($kasko->oldKaskoRisk),
            'tariff_franchise' => $kasko->tariff->{"franchise_" . $lang},
        ];
    }
}