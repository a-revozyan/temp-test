<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "story_file".
 *
 * @property int $id
 * @property int|null $story_id
 * @property int|null $type
 * @property string|null $path
 */
class StoryFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_id'], 'default', 'value' => null],
            [['story_id', 'type'], 'integer'],
            [['path'], 'string', 'max' => 255],
        ];
    }

    public const TYPE = [
        'file' => 0,
        'cover' => 1,
    ];

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'story_id' => 'Story ID',
            'path' => 'Path',
        ];
    }

    public function deleteWithFile()
    {
        $root = str_replace('\\', '/', \Yii::getAlias('@backapi') . '/web/');
        if (is_file($root .  $this->path))
            unlink($root .  $this->path);
        $this->delete();
    }

    public function saveFile($file, $old_file = '')
    {
        $folder_path = '/uploads/story/files/' . $this->story_id . '/';
        $root = str_replace('\\', '/', Yii::getAlias('@backapi') . '/web/');

        if (\yii\helpers\FileHelper::createDirectory($root . $folder_path, $mode = 0775, $recursive = true)) {
            $file_path = $folder_path . $file->baseName . "-" . Yii::$app->security->generateRandomString(5) . "." . $file->extension;
            if ($file->saveAs($root .$file_path))
            {
                if (!empty($old_file) and is_file($root .  $old_file))
                    unlink($root .  $old_file);
            }
            return $file_path;
        }

        return null;
    }

    public static function getArrCollection($models): array
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getArr();
        }

        return $_models;
    }

    public function getArr()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'path' => GeneralHelper::env('backapi_project_website') . $this->path,
        ];
    }
}
