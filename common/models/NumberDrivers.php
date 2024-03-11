<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "number_drivers".
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $name_en
 * @property string $description_en
 * @property string $description_ru
 * @property string $description_uz
 * @property float $coeff
 */
class NumberDrivers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'number_drivers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_ru', 'name_uz', 'name_en', 'coeff'], 'required'],
            [['coeff'], 'default', 'value' => null],
            [['coeff'], 'number'],
            [['name_ru', 'name_uz', 'name_en', 'description_ru', 'description_uz', 'description_en'], 'string', 'max' => 255],
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
            'coeff' => Yii::t('app', 'Coeff'),
        ];
    }

    public function getShortInRuArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_ru,
            'description' => $this->description_ru,
        ];
    }

    public static function getShortArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[$model->id] = $model->getShortArr();
        }

        return $_models;
    }

    public function getShortArr()
    {
        $lang = GeneralHelper::lang_of_local();
        return [
            'id' => $this->id,
            'name' => $this->{'name_' . $lang},
            'description' => $this->{'description_' . $lang},
        ];
    }
}
