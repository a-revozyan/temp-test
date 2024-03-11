<?php
namespace backapi\models\forms\tagForms;

use common\models\Autobrand;
use common\models\Tag;
use yii\base\Model;

class CreateTagForm extends Model
{
    public $name_uz;
    public $name_ru;
    public $name_en;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_uz', 'name_ru', 'name_en'], 'required'],
        ];
    }

    public function save()
    {
        $tag = new Tag();
        $tag->setAttributes($this->attributes);
        $tag->save();

        return $tag;
    }

}