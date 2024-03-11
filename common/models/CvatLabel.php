<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cvat_label".
 *
 * @property int $id
 * @property int|null $label_id
 * @property string|null $name
 * @property string|null $color
 * @property int|null $status
 */
class CvatLabel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cvat_label';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['label_id', 'status'], 'default', 'value' => null],
            [['label_id', 'status'], 'integer'],
            [['name', 'color'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label_id' => 'Label ID',
            'name' => 'Name',
            'color' => 'Color',
            'status' => 'Status',
        ];
    }
}
