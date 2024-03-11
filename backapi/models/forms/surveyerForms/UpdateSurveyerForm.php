<?php
namespace backapi\models\forms\surveyerForms;

use common\models\Region;
use common\models\Surveyer;
use Yii;
use yii\base\Model;


class UpdateSurveyerForm extends Model
{
    public $id;
    public $first_name;
    public $region_id;
    public $phone_number;
    public $password;
    public $repeat_password;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_id', 'first_name', 'phone_number', 'password', 'repeat_password', 'status', 'id'], 'required'],
            [['status'], 'in', 'range'=> [0,10]],
            [['region_id'], 'integer'],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Surveyer::className(), 'targetAttribute' => ['id' => 'id'], 'filter' => function($query){
                return $query->leftJoin('auth_assignment', '"user"."id" = CAST("auth_assignment"."user_id" AS INTEGER)')
                    ->where(['auth_assignment.item_name' => Surveyer::SURVEYER_ROLE_NAME]);
            }],
            ['password', 'compare', 'compareAttribute' => 'repeat_password']
        ];
    }

    public function save()
    {
        $model = Surveyer::findOne($this->id);
        $model->email = "default@surveyer.uz";
        $model->setAttributes($this->attributes);

        if (!empty($password))
            $model->setPassword($password);

        $model->save();
        $model->updated_at = time();

        return $model;
    }

}