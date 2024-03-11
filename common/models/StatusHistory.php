<?php

namespace common\models;

use common\helpers\DateHelper;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "status_history".
 *
 * @property int $id
 * @property string|null $model_class
 * @property int|null $model_id
 * @property int|null $from_status
 * @property int|null $to_status
 * @property int|null $created_at
 * @property int|null $user_id
 * @property string|null $comment
 * @property \backapi\models\User|null $user
 */
class StatusHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_id', 'from_status', 'to_status', 'created_at', 'user_id'], 'default', 'value' => null],
            [['model_id', 'from_status', 'to_status', 'user_id'], 'integer'],
            [['model_class', 'comment', 'created_at'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_class' => 'Model Class',
            'model_id' => 'Model ID',
            'from_status' => 'From Status',
            'to_status' => 'To Status',
            'created_at' => 'Created At',
            'user_id' => 'User ID',
            'comment' => 'Comment',
        ];
    }

    public static function create($model)
    {
        $old_status = $model->oldAttributes['status'] ?? null;
        $new_status = $model->attributes['status'] ?? null;
        if ($old_status != $new_status or !empty($model->status_comment))
        {
            $status_history = new StatusHistory();
            $status_history->model_class = get_class($model);
            $status_history->model_id = $model->id;
            $status_history->from_status = $old_status;
            $status_history->to_status = $new_status;
            $status_history->created_at = date('Y-m-d H:i:s');
            $status_history->user_id = Yii::$app->user->id ?? null;
            if (!empty($model->status_comment))
                $status_history->comment = $model->status_comment;
            $status_history->save();
        }
    }

    public function getUser()
    {
        return $this->hasOne(\backapi\models\User::className(), ['id' => 'user_id']);
    }

    public static function getFullArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getFullArr();
        }

        return $_models;
    }

    public function getFullArr()
    {
        return [
            'id' => $this->id,
            'from_status' => $this->get_from_status(),
            'to_status' => $this->get_to_status(),
            'created_at' => !empty($this->created_at) ? DateHelper::date_format($this->created_at, 'Y-m-d H:i:s', 'd.m.Y H:i:s') : null,
            'user' => !empty($this->user) ? $this->user->getShortArr() : null,
            'comment' => $this->comment,
        ];
    }

    public function get_from_status()
    {
        if ($this->model_class == CarInspection::className())
            return  CarInspection::getStatusLabel($this->from_status);
        return $this->from_status;
    }

    public function get_to_status()
    {
        if ($this->model_class == CarInspection::className())
            return  CarInspection::getStatusLabel($this->to_status);
        return $this->to_status;
    }
}
