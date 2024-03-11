<?php
namespace backapi\models\forms\story;

use common\helpers\DateHelper;
use common\models\Story;
use common\models\StoryFile;
use yii\base\Model;


class CreateStoryForm extends Model
{
    public $name;
    public $type;
    public $files;
    public $cover_file;
    public $status;
    public $begin_period;
    public $end_period;
    public $weekdays;
    public $begin_time;
    public $end_time;
    public $priority;
    public $view_condition;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type', 'files', 'status', 'cover_file'], 'required'],
            [['name'], 'string'],
            [['name'], 'filter', 'filter' => 'trim'],
            [['type', 'priority', 'status', 'view_condition'], 'integer'],
            [['status'], 'in', 'range' => Story::STATUS],
            [['type'], 'in', 'range' => Story::TYPE],
            [['view_condition'], 'in', 'range' => Story::VIEW_CONDITION],
            [['files'], 'each', 'rule' => ['file', 'skipOnEmpty' => false]],
            [['files'], 'max5'],
            [['cover_file'], 'file', 'skipOnEmpty' => false],
            [['weekdays'], 'each', 'rule' => ['in', 'range' => Story::WEEKDAY]],
            [['begin_period', 'end_period'], 'date', 'format' => 'd.m.Y'],
            [['begin_time', 'end_time'], 'match', 'pattern' => '/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', 'message' => 'Invalid time format. Use H:i format.'],
        ];
    }

    public function max5()
    {
        if (count($this->files) > 5)
            $this->addError('files','Number of files must not be greater then 5!');
    }

    public function save()
    {
        $story = new Story();
        $story->name = $this->name;
        $story->type = $this->type;
        $story->status = $this->status;
        $story->begin_period = !empty($this->begin_period) ? DateHelper::date_format($this->begin_period, 'd.m.Y', 'Y-m-d') : null;
        $story->end_period =  !empty($this->end_period) ? DateHelper::date_format($this->end_period, 'd.m.Y', 'Y-m-d') : null;
        $story->begin_time = $this->begin_time;
        $story->end_time = $this->end_time;
        $story->weekdays = json_encode($this->weekdays);
        $story->priority = $this->priority;
        $story->view_condition = $this->view_condition;
        $story->save();

        foreach ($this->files as $file) {
            $this->saveFile(StoryFile::TYPE['file'], $file, $story);
        }

        $this->saveFile(StoryFile::TYPE['cover'], $this->cover_file, $story);

        return $story;
    }

    public function saveFile($type, $file, $story)
    {
        $story_file = new StoryFile();
        $story_file->story_id = $story->id;
        $story_file->type = $type;
        $story_file->path = $story_file->saveFile($file);
        if (!empty($story_file->path))
            $story_file->save();
    }

}