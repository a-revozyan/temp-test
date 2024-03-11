<?php

namespace frontend\models\UserapiForms;

use common\models\User;
use Yii;

class CheckForm extends \yii\base\Model
{
    public ?string $phone = null;
    const ACTION = [
        'login' => 1,
        'register' => 2,
    ];

    public function rules(): array
    {
        return [
            [['phone'], 'required']
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'phone' => Yii::t('app', 'phone'),
        ];
    }

    public function check(): array
    {
        $user = User::findByPhone($this->phone);
        $action = ($user and !empty($user->password_hash)) ? self::ACTION['login'] : self::ACTION['register'];
        return [
            'action' => $action,
        ];
    }
}