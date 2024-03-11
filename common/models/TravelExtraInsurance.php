<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_extra_insurance".
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $name_en
 * @property bool $status
 *
 * @property TravelExtraInsuranceBind[] $travelExtraInsuranceBinds
 * @property TravelPartnerExtraInsurance[] $travelPartnerExtraInsurances
 */
class TravelExtraInsurance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_extra_insurance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_ru', 'name_uz', 'name_en', 'status'], 'required'],
            [['status'], 'boolean'],
            [['name_ru', 'name_uz', 'name_en'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name_ru' => Yii::t('app', 'Name Ru'),
            'name_uz' => Yii::t('app', 'Name Uz'),
            'name_en' => Yii::t('app', 'Name En'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[TravelExtraInsuranceBinds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelExtraInsuranceBinds()
    {
        return $this->hasMany(TravelExtraInsuranceBind::className(), ['extra_insurance_id' => 'id']);
    }

    /**
     * Gets query for [[TravelPartnerExtraInsurances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelPartnerExtraInsurances()
    {
        return $this->hasMany(TravelPartnerExtraInsurance::className(), ['extra_insurance_id' => 'id']);
    }
}
