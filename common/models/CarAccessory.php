<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "car_accessory".
 *
 * @property int $id
 * @property string|null $name_ru
 * @property string|null $name_uz
 * @property string|null $name_en
 * @property string|null $description_ru
 * @property string|null $description_uz
 * @property string|null $description_en
 * @property float|null $amount_min
 * @property float|null $amount_max
 */
class CarAccessory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'car_accessory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description_ru', 'description_uz', 'description_en'], 'string'],
            [['amount_min', 'amount_max'], 'number'],
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
            'description_ru' => Yii::t('app', 'Description Ru'),
            'description_uz' => Yii::t('app', 'Description Uz'),
            'description_en' => Yii::t('app', 'Description En'),
            'amount_min' => Yii::t('app', 'Amount Min'),
            'amount_max' => Yii::t('app', 'Amount Max'),
        ];
    }

    public static function getForIdNameArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getForIdNameArr();
        }

        return $_models;
    }

    public function getForIdNameArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_ru,
        ];
    }

    public function getFullArr()
    {
        return [
            'id' => $this->id,
            'name_en' => $this->name_en,
            'name_ru' => $this->name_ru,
            'name_uz' => $this->name_uz,
            'description_en' => $this->description_en,
            'description_ru' => $this->description_ru,
            'description_uz' => $this->description_uz,
            'amount_min' => $this->amount_min,
            'amount_max' => $this->amount_max,
        ];
    }
}
