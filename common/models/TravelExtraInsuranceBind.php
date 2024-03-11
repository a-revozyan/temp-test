<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_extra_insurance_bind".
 *
 * @property int $id
 * @property int $travel_id
 * @property int $extra_insurance_id
 *
 * @property Travel $travel
 * @property TravelExtraInsurance $extraInsurance
 */
class TravelExtraInsuranceBind extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_extra_insurance_bind';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['travel_id', 'extra_insurance_id'], 'required'],
            [['travel_id', 'extra_insurance_id'], 'default', 'value' => null],
            [['travel_id', 'extra_insurance_id'], 'integer'],
            [['travel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Travel::className(), 'targetAttribute' => ['travel_id' => 'id']],
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
            'travel_id' => Yii::t('app', 'Travel ID'),
            'extra_insurance_id' => Yii::t('app', 'Extra Insurance ID'),
        ];
    }

    /**
     * Gets query for [[Travel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravel()
    {
        return $this->hasOne(Travel::className(), ['id' => 'travel_id']);
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
