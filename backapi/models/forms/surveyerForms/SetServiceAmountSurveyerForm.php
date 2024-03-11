<?php
namespace backapi\models\forms\surveyerForms;

use common\models\Surveyer;
use yii\base\Model;


class SetServiceAmountSurveyerForm extends Model
{
    public $id;
    public $service_amount;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'service_amount'], 'required'],
            [['id', 'service_amount'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Surveyer::className(), 'targetAttribute' => ['id' => 'id'], 'filter' => function($query){
                return $query->leftJoin('auth_assignment', '"user"."id" = CAST("auth_assignment"."user_id" AS INTEGER)')
                    ->where(['auth_assignment.item_name' => Surveyer::SURVEYER_ROLE_NAME]);
            }],
        ];
    }

    public function save()
    {
        $model = Surveyer::findOne($this->id);
        $model->service_amount = $this->service_amount;
        $model->save();
        return $model;
    }

}