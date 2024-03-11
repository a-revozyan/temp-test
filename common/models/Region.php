<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $name_en
 * @property float $coeff
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'region';
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
            'coeff' => Yii::t('app', 'Coeff'),
        ];
    }

    public function getShortInRuArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_ru,
        ];
    }

    public function getShortArr()
    {
        $lang = GeneralHelper::lang_of_local();
        return [
            'id' => $this->id,
            'name' => $this->{'name_' . $lang},
        ];
    }
}
