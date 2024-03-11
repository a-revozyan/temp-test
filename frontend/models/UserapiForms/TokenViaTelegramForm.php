<?php

namespace frontend\models\UserapiForms;

use common\helpers\GeneralHelper;
use common\models\Token;
use common\services\TelegramService;
use yii\web\BadRequestHttpException;

class TokenViaTelegramForm extends \yii\base\Model
{
    public ?string $data_check_string = null;
    public ?string $bcrypted_token = null;
    public ?string $telegram_chat_id = null;
    public ?string $car_price_telegram_chat_id = null;

    public function rules(): array
    {
        return [
            [['data_check_string', 'bcrypted_token'], 'safe'],
            [['telegram_chat_id', 'car_price_telegram_chat_id'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'data_check_string' => 'data_check_string',
            'telegram_chat_id' => 'telegram_chat_id',
        ];
    }

    /**
     * @throws BadRequestHttpException
     */
    public function send()
    {
        if (
            TelegramService::checkFromTelegram($this->data_check_string)
            and
            !empty($this->telegram_chat_id)
            and
            $token = Token::findOne(['telegram_chat_id' => $this->telegram_chat_id])
        )
            return $token;

        if (
            password_verify(GeneralHelper::env('car_price_bot_token'), $this->bcrypted_token)
            and
            !empty($this->car_price_telegram_chat_id)
            and
            $token = Token::findOne(['car_price_telegram_chat_id' => $this->car_price_telegram_chat_id])
        )
            return $token;

        throw new BadRequestHttpException('Please login via sms');
    }
}