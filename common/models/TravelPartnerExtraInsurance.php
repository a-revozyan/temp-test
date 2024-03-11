<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_partner_extra_insurance".
 *
 * @property int $id
 * @property int $partner_id
 * @property int $extra_insurance_id
 * @property float $coeff
 *
 * @property Partner $partner
 * @property TravelExtraInsurance $extraInsurance
 */
class TravelPartnerExtraInsurance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_partner_extra_insurance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'extra_insurance_id'], 'required'],
            [['partner_id', 'extra_insurance_id'], 'default', 'value' => null],
            [['partner_id', 'extra_insurance_id'], 'integer'],
            [['coeff', 'sum_insured'], 'number'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['extra_insurance_id'], 'exist', 'skipOnError' => true, 'targetClass' => TravelExtraInsurance::className(), 'targetAttribute' => ['extra_insurance_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'partner_id' => Yii::t('app', 'Partner ID'),
            'extra_insurance_id' => Yii::t('app', 'Extra Insurance ID'),
            'coeff' => Yii::t('app', 'Coeff'),
        ];
    }

    /**
     * Gets query for [[Partner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }

    /**
     * Gets query for [[ExtraInsurance]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExtraInsurance()
    {
        return $this->hasOne(TravelExtraInsurance::className(), ['id' => 'extra_insurance_id']);
    }
}
