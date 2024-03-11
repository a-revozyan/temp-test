<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "traveler".
 *
 * @property int $id
 * @property int $travel_id
 * @property string $name
 * @property string $birthday
 * @property string $passport_series
 * @property string $passport_number
 * @property string $phone
 * @property string $address
 *
 * @property Travel $travel
 */
class Traveler extends \yii\db\ActiveRecord
{
    public $isInsurer;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'traveler';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['travel_id', 'name', 'passport_series', 'passport_number'], 'required', 'message' => Yii::t('app', 'Необходимо заполнить')],
            ['birthday', 'required', 'when' => function($model) {
                return true;
            }, 'whenClient' => "function (attribute, value) {
                if($('input[name=\'Travel[group_type_id]\']:checked').val() != 2) return true;
                else return false;
            }", 'message' => Yii::t('app', 'Необходимо заполнить')],
            [['travel_id'], 'default', 'value' => null],
            [['travel_id'], 'integer'],
            [['birthday'], 'safe'],
            [['name', 'passport_series', 'passport_number', 'phone', 'address'], 'string', 'max' => 255],
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
            'name' => Yii::t('app', 'Name'),
            'birthday' => Yii::t('app', 'Birthday'),
            'passport_series' => Yii::t('app', 'Passport Series'),
            'passport_number' => Yii::t('app', 'Passport Number'),
            'phone' => Yii::t('app', 'Phone'),
            'address' => Yii::t('app', 'Address'),
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
}
