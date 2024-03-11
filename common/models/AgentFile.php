<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "agent_files".
 *
 * @property int $id
 * @property int|null $agent_id
 * @property string|null $path
 */
class AgentFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agent_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agent_id'], 'default', 'value' => null],
            [['agent_id'], 'integer'],
            [['path'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'agent_id' => Yii::t('app', 'Agent ID'),
            'path' => Yii::t('app', 'Path'),
        ];
    }

    public function getAgent()
    {
        return $this->hasOne(Agent::className(), ['agent_id' => 'id']);
    }

    public function saveFile($model, $file, $old_file = '')
    {
        $folder_path = '/uploads/agent/files/' . $model->agent_id . '/';
        $root = str_replace('\\', '/', Yii::getAlias('@backend') . '/web/');

        if (\yii\helpers\FileHelper::createDirectory($root . $folder_path, $mode = 0775, $recursive = true)) {
            $file_path = $folder_path . $file->baseName . "-" . Yii::$app->security->generateRandomString(5) . "." . $file->extension;
            if ($file->saveAs($root .$file_path))
            {
                $model->path = $file_path;
                if (!empty($old_file) and is_file($root .  $old_file))
                    unlink($root .  $old_file);
            }
        }
        $model->save();

        return $model;
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'path' => GeneralHelper::env('backend_project_website') . $this->path,
        ];
    }

    public static function getShortArrCollection($agent_files)
    {
        $_agent_files = [];
        foreach ($agent_files as $agent_file) {
            $_agent_files[] = $agent_file->getShortArr();
        }
        return $_agent_files;
    }
}
