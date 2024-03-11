<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "accident_type".
 *
 * @property int $id
 * @property string|null $name_uz
 * @property string|null $name_ru
 * @property string|null $name_en
 * @property string|null $description_en
 * @property string|null $description_ru
 * @property string|null $description_uz
 * @property int|null $required
 * @property int|null $amount
 */
class AccidentType extends \yii\db\ActiveRecord
{

    public const REQUIRED = [
        'optional' => 0,
        'required' => 1,
    ];

    public const ACCIDENT_TYPE = [
        'document' => 1,
        'life' => 2,
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accident_type';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['required'], 'default', 'value' => null],
            [['required', 'amount'], 'integer'],
            [['name_uz', 'name_ru', 'name_en', 'description_en', 'description_ru', 'description_uz'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_uz' => 'Name Uz',
            'name_ru' => 'Name Ru',
            'name_en' => 'Name En',
            'description_en' => 'Description En',
            'description_ru' => 'Description Ru',
            'description_uz' => 'Description Uz',
            'required' => 'Required',
        ];
    }

    public function getShortArr()
    {
        $lang = GeneralHelper::lang_of_local();
        return [
            'id' => $this->id,
            'name' => $this->{"name_$lang"},
            'description' => $this->{"description_$lang"},
            'required' => $this->required,
            'amount' => $this->amount,
        ];
    }
}
