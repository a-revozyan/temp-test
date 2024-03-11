<?php
namespace backapi\models\forms\tagForms;

use common\models\Tag;
use yii\base\Model;


class UpdateTagForm extends Model
{
    public $id;
    public $name_uz;
    public $name_ru;
    public $name_en;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name_uz', 'name_ru', 'name_en'], 'required'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::className(), 'targetAttribute' => ['ig' => 'id']],
        ];
    }

    public function save()
    {
        $tag = Tag::findOne($this->id);
        $tag->setAttributes($this->attributes);
        $tag->save();

        return $tag;
    }
}