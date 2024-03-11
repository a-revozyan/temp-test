<?php

namespace frontend\models\UserapiForms;

use common\helpers\GeneralHelper;
use common\models\Token;
use common\models\User;
use common\services\SMSService;
use frontend\models\SignupForm;
use Yii;
use yii\web\BadRequestHttpException;

class SendSMSCodeForm extends \yii\base\Model
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
        $token = Token::createNewToken($this->phone);
        return $token->sendPhoneVerificationMessage();
    }
}