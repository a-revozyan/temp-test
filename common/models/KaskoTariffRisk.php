<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "kasko_tariff_risk".
 *
 * @property int $id
 * @property int $tariff_id
 * @property int $risk_id
 *
 * @property KaskoRisk $risk
 * @property KaskoTariff $tariff
 */
class KaskoTariffRisk extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kasko_tariff_risk';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tariff_id', 'risk_id'], 'required'],
            [['tariff_id', 'risk_id'], 'default', 'value' => null],
            [['tariff_id', 'risk_id'], 'integer'],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoRisk::className(), 'targetAttribute' => ['risk_id' => 'id']],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoTariff::className(), 'targetAttribute' => ['tariff_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tariff_id' => Yii::t('app', 'Tariff ID'),
            'risk_id' => Yii::t('app', 'Risk ID'),
        ];
    }

    /**
     * Gets query for [[Risk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRisk()
    {
        return $this->hasOne(KaskoRisk::className(), ['id' => 'risk_id']);
    }

    /**
     * Gets query for [[Tariff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTariff()
    {
        return $this->hasOne(KaskoTariff::className(), ['id' => 'tariff_id']);
    }
}
