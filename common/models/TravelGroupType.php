<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_group_type".
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $name_en
 * @property bool $status
 *
 * @property Travel[] $travels
 * @property TravelPartnerGroupType[] $travelPartnerGroupTypes
 */
class TravelGroupType extends \yii\db\ActiveRecord
{
    public const GROUP_TYPE = [
        'individual' => 1,
        'family' => 2,
        'group' => 3,
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_group_type';
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
        return $this->hasMany(Travel::className(), ['group_type_id' => 'id']);
    }

    /**
     * Gets query for [[TravelPartnerGroupTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelPartnerGroupTypes()
    {
        return $this->hasMany(TravelPartnerGroupType::className(), ['group_type_id' => 'id']);
    }
}
