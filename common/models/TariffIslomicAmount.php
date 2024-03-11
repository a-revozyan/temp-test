<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tariff_islomic_amount".
 *
 * @property int $id
 * @property int|null $kasko_tariff_id
 * @property int|null $auto_risk_type_id
 * @property float|null $amount
 *
 * @property AutoRiskType $autoRiskType
 * @property KaskoTariff $kaskoTariff
 */
class TariffIslomicAmount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tariff_islomic_amount';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kasko_tariff_id', 'auto_risk_type_id'], 'default', 'value' => null],
            [['kasko_tariff_id', 'auto_risk_type_id'], 'integer'],
            [['amount'], 'number'],
            [['auto_risk_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => AutoRiskType::className(), 'targetAttribute' => ['auto_risk_type_id' => 'id']],
            [['kasko_tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoTariff::className(), 'targetAttribute' => ['kasko_tariff_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'kasko_tariff_id' => Yii::t('app', 'Kasko Tariff ID'),
            'auto_risk_type_id' => Yii::t('app', 'Auto Risk Type ID'),
            'amount' => Yii::t('app', 'Amount'),
        ];
    }

    public function fields()
    {
        $fieds = parent::fields();
        $fieds[] = 'autoRiskType';

        return $fieds;
    }

    /**
     * Gets query for [[AutoRiskType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutoRiskType()
    {
        return $this->hasOne(AutoRiskType::className(), ['id' => 'auto_risk_type_id']);
    }

    /**
     * Gets query for [[KaskoTariff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKaskoTariff()
    {
        return $this->hasOne(KaskoTariff::className(), ['id' => 'kasko_tariff_id']);
    }

    public static function getMergeRiskTypeArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getMergeRiskTypeArr();
        }

        return $_models;
    }

    public function getMergeRiskTypeArr()
    {
        return array_merge(
            $this->autoRiskType->getShortArr(),
            ['amount' => $this->amount]
        );
    }
}
