<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "period".
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $name_en
 * @property float $coeff
 */
class Period extends \yii\db\ActiveRecord
{
    public const PERIOD_STRING = [
        1 => '+1 year -1 day',
        2 => '+6 months -1 day',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'period';
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
