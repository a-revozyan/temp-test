<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_partner_purpose".
 *
 * @property int $id
 * @property int $partner_id
 * @property int $purpose_id
 * @property float $coeff
 *
 * @property Partner $partner
 * @property TravelPurpose $purpose
 */
class TravelPartnerPurpose extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_partner_purpose';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'purpose_id'], 'required'],
            [['partner_id', 'purpose_id'], 'default', 'value' => null],
            [['partner_id', 'purpose_id'], 'integer'],
            [['coeff'], 'number'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['purpose_id'], 'exist', 'skipOnError' => true, 'targetClass' => TravelPurpose::className(), 'targetAttribute' => ['purpose_id' => 'id']],
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
            'purpose_id' => Yii::t('app', 'Purpose ID'),
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
     * Gets query for [[Purpose]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurpose()
    {
        return $this->hasOne(TravelPurpose::className(), ['id' => 'purpose_id']);
    }
}
