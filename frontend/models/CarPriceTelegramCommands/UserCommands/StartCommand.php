<?php
namespace frontend\models\Commands\UserCommands;


use common\helpers\GeneralHelper;
use common\models\Token;
use common\models\User;
use common\services\SMSService;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Yii;
use yii\helpers\VarDumper;

class StartCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'auth for bot users';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '0.4.0';

    /**
     * @var bool
     */
//    protected $need_mysql = true;

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Conversation Object
     *
     * @var Conversation
     */
    protected $conversation;

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */

    public function returnWebAppUrl($data, $lang, $access_token)
    {
        $lang = strtolower($lang);
        $inline_keyboard = new InlineKeyboard([
            ['text' => Yii::t('app', 'calculate', [], $lang), 'web_app' => ['url' => GeneralHelper::env('car_price_front_url') . "/" . $lang . '?token=' . $access_token]],
        ]);

        $data['text'] = Yii::t('app', "Quyidagi tugmaga bosing", [], $lang);
//        $data['reply_markup'] = Keyboard::remove(['selective' => true]);
        $data['reply_markup'] = $inline_keyboard;

        return Request::sendMessage($data);
    }

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        $chat    = $message->getChat();
        $user    = $message->getFrom();
        $text = $message->getText(true);
        $text    = $text == null ? null : trim($text);
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        // Preparing response
        $data = [
            'chat_id'      => $chat_id,
            // Remove any keyboard by default
            'reply_markup' => Keyboard::remove(['selective' => true]),
        ];

        $token = Token::findOne(['car_price_telegram_chat_id' => $chat_id, 'status' => Token::STATUS['verified']]);
        if (!is_null($token) and $user = User::findOne($token->f_user_id))
            return $this->returnWebAppUrl($data, $token->telegram_lang, $token->access_token);

        if (is_null($user))
            Token::deleteAll(['f_user_id' => $token->f_user_id]);

        if ($chat->isGroupChat() || $chat->isSuperGroup()) {
            // Force reply is applied by default so it can work with privacy on
            $data['reply_markup'] = Keyboard::forceReply(['selective' => true]);
        }

        // Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        // Load any existing notes from this conversation
        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        // Load the current state of the conversation
        $state = $notes['state'] ?? 0;

        $result = Request::emptyResponse();

        // State machine
        // Every time a step is achieved the state is updated
        if (array_key_exists('lang', $notes) and $text == Yii::t('app', "Telefon raqamini boshqattan jo'natish", [], $notes['lang']))
        {
            $notes['state'] = 1;
            $state = 1;
        }

        switch ($state) {
            case 0:
                if ($text === '' || !in_array($text, ['UZ', 'RU'], true)) {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard(['UZ', 'RU']))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = Yii::t('app', "Qaysi tilda so'zlashishni xoxlaysiz:", [], 'uz');
                    if (!is_null($text)) {
                        $data['text'] = Yii::t('app', 'Iltimos tilni tugmalardan tanlang', [], 'uz');
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['lang'] = strtolower($text);
            case 1:
                $phone_number_validation_regex = "/^\\+?[9]{2}[8][0-9]{9}$/";
                $phone_is_correct = false;
                if (!is_null($text))
                    $phone_is_correct = preg_match($phone_number_validation_regex, $text);

                if ($message->getContact() === null and !$phone_is_correct) {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard(
                        (new KeyboardButton(Yii::t('app', "Telegram raqamimni yuborish", [], $notes['lang'])))->setRequestContact(true)
                    ))
                        ->setOneTimeKeyboard(true)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = Yii::t('app', "Telefon raqamingizni pastdagi tugma orqali jo'nating yoki yozing(faqat quyidagi shaklda yozing: 998AAXXXXXXX):", [], $notes['lang']);

                    $result = Request::sendMessage($data);
                    break;
                }

                if ($message->getContact() !== null)
                    $notes['phone_number'] = $message->getContact()->getPhoneNumber();
                else
                    $notes['phone_number'] = $text;
                $notes['phone_number'] = str_replace('+', '', $notes['phone_number']);

                $token = Token::createNewToken($notes['phone_number']);
                $token->sendPhoneVerificationMessage($notes['lang']);

            // No break!

            case 2:
                $f_user = User::find()->where(['phone' => $notes['phone_number']])->one();
                $sms_code_is_correct = false;
                if ($token = Token::findOne(['f_user_id' => $f_user->id, 'verification_token' => $text, 'status' => Token::STATUS['sent']]))
                    $sms_code_is_correct = true;

                if ($text === null or !$sms_code_is_correct) {
                    $notes['state'] = 2;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard(
                        (new KeyboardButton(Yii::t('app', "Telefon raqamini boshqattan jo'natish", [], $notes['lang'])))
                    ))
                        ->setOneTimeKeyboard(true)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = Yii::t('app', "{phone_number} raqamiga yuborilgan sms kodni kiriting", ['phone_number' => $notes['phone_number']], $notes['lang']);
                    if (!isset($phone_is_correct) and !is_null($text) and !$sms_code_is_correct)
                        $data['text'] =  Yii::t('app', "Kodni xato kiritdingiz, boshqattan kiriting", [], $notes['lang']);

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['sms_code'] = $text;
                $f_user->status = User::STATUS_ACTIVE;
                $f_user->save();

                $token->car_price_telegram_chat_id = $chat_id;
                $token->telegram_lang = $notes['lang'];
                $token->generateAccessToken();
                $token->status = Token::STATUS['verified'];
                $token->save();

            // No break!

            case 3:
                $this->conversation->update();

                $keyboard = Keyboard::remove();
                $this->replyToChat(Yii::t('app', "Tabriklaymiz, siz muvaffaqiyatli ro'yxatdan o'tdingiz!", [], $notes['lang']), [
                    'reply_markup' => $keyboard,
                ]);

                $result = $this->returnWebAppUrl($data, $notes['lang'], $token->access_token);

                $this->conversation->stop();
                break;
        }

        return $result;
    }

}