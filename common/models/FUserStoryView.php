<?php

namespace common\models;

/**
 * This is the model class for table "f_user_story_view".
 *
 * @property int $id
 * @property int|null $f_user_id
 * @property int|null $story_id
 * @property string|null $viewed_at
 */
class FUserStoryView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'f_user_story_view';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['f_user_id', 'story_id'], 'default', 'value' => null],
            [['f_user_id', 'story_id'], 'integer'],
            [['viewed_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'f_user_id' => 'F User ID',
            'story_id' => 'Story ID',
            'viewed_at' => 'Viewed At',
        ];
    }
}
