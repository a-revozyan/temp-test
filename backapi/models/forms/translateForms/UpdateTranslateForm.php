<?php
namespace backapi\models\forms\translateForms;

use common\models\Message;
use common\models\SourceMessage;
use yii\base\Model;


class UpdateTranslateForm extends Model
{
    public $id;
    public $ru;
    public $uz;
    public $en;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['ru', 'uz', 'en'], 'string'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => SourceMessage::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function save()
    {
        $model = SourceMessage::findOne($this->id);
        $model_ru = Message::find()->where(['id' => $this->id, 'language' => 'ru'])->one();
        $model_uz = Message::find()->where(['id' => $this->id, 'language' => 'uz'])->one();
        $model_en = Message::find()->where(['id' => $this->id, 'language' => 'en'])->one();

        $model_ru->translation = $this->ru;
        $model_ru->save();

        $model_uz->translation = $this->uz;
        $model_uz->save();

        $model_en->translation = $this->en;
        $model_en->save();

        return $model;
    }

}