<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "qa".
 *
 * @property int $id
 * @property string|null $question_uz
 * @property string|null $question_en
 * @property string|null $question_ru
 * @property string|null $answer_uz
 * @property string|null $answer_en
 * @property string|null $answer_ru
 * @property integer|null $page
 * @property integer|null $status
 */
class Qa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'qa';
    }

    public const PAGES = [
        'home_page' => 0,
        'kbs_page' => 1,
        'kasko' => 2,
        'travel' => 3,
    ];

    public const STATUSES = [
        'inactive' => 0,
        'active' => 1,
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_uz', 'question_en', 'question_ru', 'answer_uz', 'answer_en', 'answer_ru'], 'string', 'max' => 65535],
            [['status', 'page'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'question_uz' => Yii::t('app', 'Question Uz'),
            'question_en' => Yii::t('app', 'Question En'),
            'question_ru' => Yii::t('app', 'Question Ru'),
            'answer_uz' => Yii::t('app', 'Answer Uz'),
            'answer_en' => Yii::t('app', 'Answer En'),
            'answer_ru' => Yii::t('app', 'Answer Ru'),
            'status' => Yii::t('app', 'Status'),
            'page' => Yii::t('app', 'Page'),
        ];
    }

    public static function getShortAdminArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getShortAdminArr();
        }

        return $_models;
    }

    public function getShortAdminArr()
    {
        return [
            'id' => $this->id,
            'question_ru' => $this->question_ru,
            'question_uz' => $this->question_uz,
            'answer_ru' => $this->answer_ru,
            'answer_uz' => $this->answer_uz,
            'page' => $this->page,
            'status' => $this->status,
        ];
    }

    public function getFullAdminArr()
    {
        return [
            'id' => $this->id,
            'question_ru' => $this->question_ru,
            'question_uz' => $this->question_uz,
            'answer_ru' => $this->answer_ru,
            'answer_uz' => $this->answer_uz,
            'page' => $this->page,
            'status' => $this->status,
        ];
    }

    public static function getShortClientArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getShortClientArr();
        }

        return $_models;
    }

    public function getShortClientArr()
    {
        $lang = GeneralHelper::lang_of_local();
        return [
            'id' => $this->id,
            'question' => nl2br($this->{"question_$lang"} ?? ""),
            'answer' => nl2br($this->{"answer_$lang"} ?? ""),
            'page' => $this->page,
            'status' => $this->status,
        ];
    }
}
