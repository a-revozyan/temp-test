<?php
namespace frontend\models\SurveyerForms;


use common\models\Kasko;
use Yii;
use yii\web\NotFoundHttpException;

class SurveyerAttachKaskoForm extends \yii\base\Model
{
    public $kasko_id;
    public $surveyer;

    public function rules()
    {
        return [
            ['kasko_id', 'required'],
            ['kasko_id', 'integer'],
            ['kasko_id', 'exist', 'skipOnError' => true, 'targetClass' => Kasko::className(), 'targetAttribute' => ['kasko_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'kasko_id' => Yii::t('app', 'kasko')
        ];
    }

    public function save()
    {
        $kasko = Kasko::findOne(['id' => $this->kasko_id]);

        if ($kasko->status !== Kasko::STATUS['payed'])
            throw new NotFoundHttpException(Yii::t('app', 'This order is already taken by another surveyer or not ready yet'));
        $kasko->surveyer_id = $this->surveyer->id;
        $kasko->status = Kasko::STATUS['attached'];
        $kasko->save();

        return $kasko;
    }
}