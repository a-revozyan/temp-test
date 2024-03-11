<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "accident_partner_program".
 *
 * @property int $id
 * @property int $partner_id
 * @property float $insurance_amount_from
 * @property float $insurance_amount_to
 * @property float $percent
 *
 * @property Partner $partner
 */
class AccidentPartnerProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accident_partner_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'insurance_amount_from', 'insurance_amount_to', 'percent'], 'required'],
            [['partner_id'], 'default', 'value' => null],
            [['partner_id'], 'integer'],
            [['insurance_amount_from', 'insurance_amount_to', 'percent'], 'number'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partner_id' => 'Partner ID',
            'insurance_amount_from' => 'Insurance Amount From',
            'insurance_amount_to' => 'Insurance Amount To',
            'percent' => 'Percent',
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
}
