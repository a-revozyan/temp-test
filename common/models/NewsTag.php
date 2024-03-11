<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "news_tag".
 *
 * @property int $id
 * @property int|null $news_id
 * @property int|null $tag_id
 */
class NewsTag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['news_id', 'tag_id'], 'default', 'value' => null],
            [['news_id', 'tag_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'news_id' => 'News ID',
            'tag_id' => 'Tag ID',
        ];
    }
}
