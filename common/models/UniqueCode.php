<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "unique_code".
 *
 * @property int $id
 * @property int|null $clonable_id
 * @property int|null $discount_percent
 * @property string|null $code
 */
class UniqueCode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'unique_code';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clonable_id'], 'default', 'value' => null],
            [['clonable_id', 'discount_percent'], 'integer'],
            [['code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'clonable_id' => 'Clonable ID',
            'code' => 'Code',
            'discount_percent' => 'Discount Percent',
        ];
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
            'code' => $this->code,
            'discount_percent' => $this->discount_percent,
        ];
    }
}
