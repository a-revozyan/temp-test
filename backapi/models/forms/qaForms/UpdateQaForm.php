<?php
namespace backapi\models\forms\qaForms;

use common\models\Autobrand;
use common\models\Automodel;
use common\models\Qa;
use yii\base\Model;


class UpdateQaForm extends Model
{
    public $id;
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
            [['status', 'id'], 'required'],
            [['question_ru', 'question_uz', 'question_en', 'answer_ru', 'answer_uz', 'answer_en'], 'string', 'max' => 65535],
            [['status', 'page'], 'integer'],
            [['page'], 'in', 'range' => Qa::PAGES],
            [['status'], 'in', 'range' => [0, 1]],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Qa::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function save()
    {
        $qa = Qa::findOne($this->id);
        $qa->setAttributes($this->attributes);
        $qa->save();

        return $qa;
    }

}