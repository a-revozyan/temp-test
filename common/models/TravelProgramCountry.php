<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_program_country".
 *
 * @property int $id
 * @property int $partner_id
 * @property int $program_id
 * @property int $country_id
 *
 * @property Country $country
 * @property Partner $partner
 * @property TravelProgram $program
 */
class TravelProgramCountry extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_program_country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'program_id', 'country_id'], 'required'],
            [['partner_id', 'program_id', 'country_id'], 'default', 'value' => null],
            [['partner_id', 'program_id', 'country_id'], 'integer'],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
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
            'country_id' => Yii::t('app', 'Country ID'),
        ];
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
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
