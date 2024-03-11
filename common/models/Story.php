<?php

namespace common\models;

use common\helpers\DateHelper;

/**
 * This is the model class for table "story".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $begin_period
 * @property string|null $end_period
 * @property string|null $weekdays
 * @property string|null $begin_time
 * @property string|null $end_time
 * @property int|null $priority
 * @property int|null $view_condition
 * @property int|null $type
 * @property int|null $status
 * @property int|null $period_status
 * @property string|null $created_at
 * @property int|null $views_count
 * @property StoryFile[] $coverFile
 * @property StoryFile[] $files
 */
class Story extends \yii\db\ActiveRecord
{
    public $period_status;
    public $views_count;
    /**
     * @var mixed|null
     */

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['begin_period', 'end_period', 'weekdays', 'begin_time', 'end_time', 'created_at'], 'safe'],
            [['priority', 'view_condition', 'type', 'status'], 'default', 'value' => null],
            [['priority', 'view_condition', 'type', 'status'], 'integer'],
            [['name'], 'string', 'max' => 1000],
        ];
    }

    public const STATUS = [
        'draft' => 0,
        'ready' => 1,
    ];

    public const PERIOD_STATUS = [
        'inactive' => 0,
        'active' => 1,
    ];

    public const TYPE = [
        'story' => 0,
        'reel' => 1,
    ];

    public const WEEKDAY = [
        'monday' => 1,
        'tuesday' => 2,
        'wednesday' => 3,
        'thursday' => 4,
        'friday' => 5,
        'saturday' => 6,
        'sunday' => 7,
    ];

    public const VIEW_CONDITION = [
        'new_users' => 0, //registered max 3 days ago
        'bought_only_1_policy' => 1,
        'bought_several_policy' => 2,
        'old_user_but_never_bought' => 3, //registered min 1 month ago
    ];

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'begin_period' => 'Begin Period',
            'end_period' => 'End Period',
            'weekdays' => 'Weekdays',
            'begin_time' => 'Begin Time',
            'end_time' => 'End Time',
            'priority' => 'Priority',
            'view_condition' => 'View Condition',
            'type' => 'Type',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public function getFiles()
    {
        return $this->hasMany(StoryFile::className(), ['story_id' => 'id'])->where(['type' => StoryFile::TYPE['file']]);
    }

    public function getCoverFile()
    {
        return $this->hasMany(StoryFile::className(), ['story_id' => 'id'])->where(['type' => StoryFile::TYPE['cover']]);
    }

    public function getPeriodStatus()
    {
        if (!is_null($this->period_status))
            return $this->period_status;

        $this->period_status = Story::PERIOD_STATUS['inactive'];
        if ($this->begin_period <= date('Y-m-d') and $this->end_period >= date('Y-m-d'))
            $this->period_status = Story::PERIOD_STATUS['active'];

        return $this->period_status;
    }

    public function getViewsCount()
    {
        if (!is_null($this->views_count))
            return $this->views_count;

        return FUserStoryView::find()->where(['story_id' => $this->id])->count('id');
    }

    public static function getFullAdminArrCollection($models): array
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getFullAdminArr();
        }

        return $_models;
    }

    public function getFullAdminArr(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'priority' => $this->priority,
            'begin_period' => !empty($this->begin_period) ? DateHelper::date_format($this->begin_period, 'Y-m-d', 'd.m.Y') : null,
            'end_period' => !empty($this->end_period) ? DateHelper::date_format($this->end_period, 'Y-m-d', 'd.m.Y') : null,
            'begin_time' => $this->begin_time,
            'end_time' => $this->end_time,
            'weekdays' => json_decode($this->weekdays),
            'view_condition' => $this->view_condition,
            'period_status' => $this->getPeriodStatus(),
            'views_count' => $this->getViewsCount(),
            'files' => !empty($this->coverFile) ? StoryFile::getArrCollection($this->files) : null,
            'cover' => !empty($this->coverFile) ? StoryFile::getArrCollection($this->coverFile)[0] : null,
        ];
    }
}
