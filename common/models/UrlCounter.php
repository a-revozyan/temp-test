<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "url_counter".
 *
 * @property int $id
 * @property string $url
 * @property string $code
 * @property int $count
 */
class UrlCounter extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'url_counter';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'code'], 'required'],
            [['count'], 'default', 'value' => null],
            [['count'], 'integer'],
            [['url', 'code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'Url',
            'code' => 'Code',
            'count' => 'Count',
        ];
    }
}
