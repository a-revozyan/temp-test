<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_country".
 *
 * @property int $id
 * @property int $travel_id
 * @property int $country_id
 *
 * @property Country $country
 * @property Travel $travel
 */
class TravelCountry extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['travel_id', 'country_id'], 'required'],
            [['travel_id', 'country_id'], 'default', 'value' => null],
            [['travel_id', 'country_id'], 'integer'],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['travel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Travel::className(), 'targetAttribute' => ['travel_id' => 'id']],
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
     * Gets query for [[Travel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravel()
    {
        return $this->hasOne(Travel::className(), ['id' => 'travel_id']);
    }
}
