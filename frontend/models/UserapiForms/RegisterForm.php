<?php

namespace frontend\models\UserapiForms;

use common\helpers\GeneralHelper;
use common\models\User;
use frontend\models\SignupForm;
use Yii;
use yii\web\BadRequestHttpException;

class RegisterForm extends \yii\base\Model
{
    public ?string $phone = null;
    public bool $is_reset_password = false;

    public function rules(): array
    {
        return [
            [['phone'], 'required'],
            [['is_reset_password'], 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'phone' => Yii::t('app', 'phone')
        ];
    }

    public function send()
    {
        $user = User::findByPhone($this->phone);

        if ($user and !empty($user->password_hash) and !$this->is_reset_password)
            throw new BadRequestHttpException(Yii::t('app', "This phone is already registered"));

        $model = new SignupForm();
        $model->phone = $this->phone;

        if ( ($user and empty($user->password_hash)) or ($user and $this->is_reset_password) )
        {
            $user->generateEmailVerificationToken();
            return ($model->sendPhoneMessage($user) and $user->save());
        }

        return $model->signup();
    }
}