<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_program_period".
 *
 * @property int $id
 * @property int $partner_id
 * @property int $program_id
 * @property int $from_day
 * @property int $to_day
 * @property float $amount
 *
 * @property Partner $partner
 * @property TravelProgram $program
 */
class TravelProgramPeriod extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_program_period';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'program_id', 'from_day', 'to_day', 'amount'], 'required'],
            [['partner_id', 'program_id', 'from_day', 'to_day'], 'default', 'value' => null],
            [['partner_id', 'program_id', 'from_day', 'to_day'], 'integer'],
            [['amount'], 'number'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => TravelProgram::className(), 'targetAttribute' => ['program_id' => 'id']],
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
            'from_day' => Yii::t('app', 'From Day'),
            'to_day' => Yii::t('app', 'To Day'),
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
}
