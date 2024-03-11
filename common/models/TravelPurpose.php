<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_purpose".
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $name_en
 * @property bool $status
 *
 * @property Travel[] $travels
 * @property TravelPartnerPurpose[] $travelPartnerPurposes
 */
class TravelPurpose extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_purpose';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_ru', 'name_uz', 'name_en', 'status'], 'required'],
            [['status'], 'boolean'],
            [['name_ru', 'name_uz', 'name_en'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name_ru' => Yii::t('app', 'Name Ru'),
            'name_uz' => Yii::t('app', 'Name Uz'),
            'name_en' => Yii::t('app', 'Name En'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[Travels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravels()
    {
        return $this->hasMany(Travel::className(), ['purpose_id' => 'id']);
    }

    /**
     * Gets query for [[TravelPartnerPurposes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelPartnerPurposes()
    {
        return $this->hasMany(TravelPartnerPurpose::className(), ['purpose_id' => 'id']);
    }
}
