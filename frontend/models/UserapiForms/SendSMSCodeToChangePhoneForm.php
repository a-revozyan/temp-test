<?php

namespace frontend\models\UserapiForms;

use common\models\Token;
use Yii;

class SendSMSCodeToChangePhoneForm extends \yii\base\Model
{
    public ?string $phone = null;

    public function rules(): array
    {
        return [
            ['phone', 'trim'],
            ['phone', 'required'],
            ['phone', 'string', 'min' => 2, 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'phone' => Yii::t('app', 'phone'),
        ];
    }

    public function send()
    {
        $token = Token::createNewTokenToChangePhone($this->phone);
        return $token->sendPhoneVerificationMessage();
    }
}