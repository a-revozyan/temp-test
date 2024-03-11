<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "agent".
 *
 * @property int $id
 * @property int|null $f_user_id
 * @property string|null $contract_number
 * @property string|null $logo
 * @property string|null $inn
 */
class Agent extends \yii\db\ActiveRecord
{

    public const SUPER_AGENT_IDS = [
        'road24' => 1
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agent';
    }

    public function fields()
    {
        $fields =  parent::fields();
        $fields[] = 'user';
        $fields['agentFiles'] = function ($model){
            return ArrayHelper::toArray($model->agentFiles, [
                AgentFile::className() => [
                    'id',
                    'agent_id',
                    'path' => function($file){
                        return  GeneralHelper::env('backend_project_website') . $file->path;
                    }
                ]
            ]);
        };
        $fields[] = 'agentProductCoeffs';
        $fields['logo'] = function ($model){
            return  GeneralHelper::env('backend_project_website') . $model->logo;
        };

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['f_user_id'], 'default', 'value' => null],
            [['f_user_id'], 'integer'],
            [['contract_number', 'logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'f_user_id' => Yii::t('app', 'F User ID'),
            'contract_number' => Yii::t('app', 'Contract Number'),
            'logo' => Yii::t('app', 'Logo'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'f_user_id']);
    }

    public function getAgentProductCoeffs()
    {
        return $this->hasMany(AgentProductCoeff::className(), ['agent_id' => 'id']);
    }

    public function getAgentFiles()
    {
        return $this->hasMany(AgentFile::className(), ['agent_id' => 'id']);
    }

    public function saveFile($model, $file, $old_file = '')
    {
        $name = $model->user->first_name ?? "";
        $folder_path = '/uploads/agent/logo/' . "$model->id-$name" . '/';
        $root = str_replace('\\', '/', Yii::getAlias('@backend') . '/web/');

        if (\yii\helpers\FileHelper::createDirectory($root . $folder_path, $mode = 0775, $recursive = true)) {
            $file_path = $folder_path . $file->baseName . "-" . Yii::$app->security->generateRandomString(5) . "." . $file->extension;
            if ($file->saveAs($root .$file_path))
            {
                $model->logo = $file_path;
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
            'inn' => $this->inn,
            'contract_number' => $this->contract_number,
            'logo' =>  GeneralHelper::env('backend_project_website') . $this->logo,
            'user' => $this->user->getArrForAgent() ?? null,
            'agentFiles' => AgentFile::getShortArrCollection($this->agentFiles),
            'agentProductCoeffs' => AgentProductCoeff::getShortArrCollection($this->agentProductCoeffs),
        ];
    }
}
