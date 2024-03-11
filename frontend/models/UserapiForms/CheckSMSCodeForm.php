<?php

namespace frontend\models\UserapiForms;

use common\helpers\GeneralHelper;
use common\models\Token;
use common\models\User;
use common\services\TelegramService;
use Yii;
use yii\web\BadRequestHttpException;

class CheckSMSCodeForm extends \yii\base\Model
{
    public $phone = null;
    public $verifycode = null;

    public $data_check_string = null;
    public $bcrypted_token = null;
    public $telegram_chat_id = null;
    public $car_price_telegram_chat_id = null;

    public function rules(): array
    {
        return [
            [['phone', 'verifycode'], 'required'],
            [['phone'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['phone' => 'phone']],
            [['verifycode'], 'integer'],
            [['verifycode'], 'filter', 'filter' => 'trim'],
            [['data_check_string', 'telegram_chat_id', 'car_price_telegram_chat_id', 'bcrypted_token'], 'safe']
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'phone' => Yii::t('app', 'phone'),
            'verifycode' => Yii::t('app', 'verifycode'),

            'data_check_string' => 'data_check_string',
            'telegram_chat_id' => 'telegram_chat_id',
            'car_price_telegram_chat_id' => 'car_price_telegram_chat_id',
        ];
    }

    /**
     * @throws BadRequestHttpException
     */
    public function send()
    {
        $user = User::findByPhone($this->phone);

        if (!$token = Token::findOne(['f_user_id' => $user->id, 'verification_token' => $this->verifycode, 'status' => Token::STATUS['sent']]))
            throw new BadRequestHttpException(Yii::t('app', 'Verify code is incorrect'));

        if (!empty($this->data_check_string) and TelegramService::checkFromTelegram($this->data_check_string))
            $token->telegram_chat_id = $this->telegram_chat_id;

        if (!empty($this->bcrypted_token) and password_verify(GeneralHelper::env('car_price_bot_token'), $this->bcrypted_token))
            $token->car_price_telegram_chat_id = $this->car_price_telegram_chat_id;

        $token->generateAccessToken();
        $token->status = Token::STATUS['verified'];
        $token->verified_at = date('Y-m-d H:i:s');
        $token->save();

        if (!empty(Yii::$app->getUser()->identity))
            $user->phone = $token->new_phone_number;
        $user->status = User::STATUS_ACTIVE;
        $user->save();

        return $token;
    }
}