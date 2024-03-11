<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_program_risk".
 *
 * @property int $id
 * @property int $partner_id
 * @property int $program_id
 * @property int $risk_id
 * @property float $amount
 *
 * @property Partner $partner
 * @property TravelProgram $program
 * @property TravelRisk $risk
 */
class TravelProgramRisk extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_program_risk';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'program_id', 'risk_id', 'amount'], 'required'],
            [['partner_id', 'program_id', 'risk_id'], 'default', 'value' => null],
            [['partner_id', 'program_id', 'risk_id'], 'integer'],
            [['amount'], 'number'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => TravelProgram::className(), 'targetAttribute' => ['program_id' => 'id']],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => TravelRisk::className(), 'targetAttribute' => ['risk_id' => 'id']],
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
            'program_id' => Yii::t('app', 'Program ID'),
            'risk_id' => Yii::t('app', 'Risk ID'),
            'amount' => Yii::t('app', 'Amount'),
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
     * Gets query for [[Program]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(TravelProgram::className(), ['id' => 'program_id']);
    }

    /**
     * Gets query for [[Risk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRisk()
    {
        return $this->hasOne(TravelRisk::className(), ['id' => 'risk_id']);
    }
}
