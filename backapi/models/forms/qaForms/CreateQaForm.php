<?php
namespace backapi\models\forms\qaForms;

use common\models\Autobrand;
use common\models\Qa;
use yii\base\Model;

class CreateQaForm extends Model
{
    public $question_ru;
    public $question_uz;
    public $question_en;
    public $answer_ru;
    public $answer_uz;
    public $answer_en;
    public $page;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['question_ru', 'question_uz', 'question_en', 'answer_ru', 'answer_uz', 'answer_en'], 'string', 'max' => 65535],
            [['status', 'page'], 'integer'],
            [['page'], 'in', 'range' => Qa::PAGES],
            [['status'], 'in', 'range' => [0, 1]],
        ];
    }

    public function save()
    {
        $qa = new Qa();
        $qa->setAttributes($this->attributes);
        $qa->save();

        return $qa;
    }

}