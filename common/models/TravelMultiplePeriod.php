<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_multiple_period".
 *
 * @property int $id
 * @property int|null $partner_id
 * @property int|null $program_id
 * @property int|null $available_interval_days
 * @property int|null $days
 * @property float|null $amount
 *
 * @property Partner $partner
 * @property TravelProgram $program
 */
class TravelMultiplePeriod extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_multiple_period';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'program_id', 'available_interval_days', 'days'], 'default', 'value' => null],
            [['partner_id', 'program_id', 'available_interval_days', 'days'], 'integer'],
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
            'id' => 'ID',
            'partner_id' => 'Partner ID',
            'program_id' => 'Program ID',
            'available_interval_days' => 'Available Interval Days',
            'days' => 'Travel Days',
            'amount' => 'Amount',
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
