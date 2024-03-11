<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $value
 * @property string|null $updated_at
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'value'], 'string'],
            [['updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'value' => 'Value',
            'updated_at' => 'Updated At',
        ];
    }

    public const PERIOD = [
        'day' => 1,
        'week' => 2,
        'month' => 3,
        'quarter' => 4,
        'year' => 5,
    ];

    public const SETTING_ID = [
        'car_price_requests_limit' => 1,
        'car_price_requests_period' => 2,
    ];

    public static function getFullArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getFullArr();
        }

        return $_models;
    }

    public function getFullArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'updated_at' => $this->updated_at,
        ];
    }
}
