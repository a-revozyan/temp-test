<?php

namespace common\models;

use common\helpers\DateHelper;
use Yii;

/**
 * This is the model class for table "travel_member".
 *
 * @property int $id
 * @property int|null $travel_id
 * @property int|null $age
 * @property int|null $position
 * @property string|null $name
 * @property string|null $passport_series
 * @property string|null $passport_number
 * @property string|null $birthday
 *
 * @property Travel $travel
 */
class TravelMember extends \yii\db\ActiveRecord
{
     const POSITIONS = [
        'simple_member' => 0,
        'parent' => 1,
        'child' => 2
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_member';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['travel_id', 'age', 'position'], 'default', 'value' => null],
            [['travel_id', 'age', 'position'], 'integer'],
            [['travel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Travel::className(), 'targetAttribute' => ['travel_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'travel_id' => 'Travel ID',
            'age' => 'Age',
            'position' => 'Position',
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

    public static function getShortArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getShortArr();
        }

        return $_models;
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'passport_series' => $this->passport_series,
            'passport_number' => $this->passport_number,
            'birthday' => empty($this->birthday) ? null : DateHelper::date_format($this->birthday, 'Y-m-d', 'd.m.Y'),
            'age' => $this->age,
        ];
    }
}
