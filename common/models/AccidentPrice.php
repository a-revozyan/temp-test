<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "accident_price".
 *
 * @property int $id
 * @property int|null $gross
 * @property int|null $kapital
 * @property string|null $updated_at
 */
class AccidentPrice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accident_price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gross', 'kapital'], 'default', 'value' => null],
            [['gross', 'kapital'], 'integer'],
            [['updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gross' => 'Gross',
            'kapital' => 'Kapital',
            'updated_at' => 'Updated At',
        ];
    }
}
