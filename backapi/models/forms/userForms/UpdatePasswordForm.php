<?php
namespace backapi\models\forms\userForms;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\web\BadRequestHttpException;


class UpdatePasswordForm extends Model
{
    public $old_password;
    public $password;
    public $password_repeat;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['old_password', 'password'], 'required'],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Password and Repeat password don't match" ],
        ];
    }

    public function save()
    {
       $user = Yii::$app->user->identity;
       if (!$user->validatePassword($this->old_password))
            throw new BadRequestHttpException('Old password is incorrect');

       $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
       $user->save();

       return true;
    }

}