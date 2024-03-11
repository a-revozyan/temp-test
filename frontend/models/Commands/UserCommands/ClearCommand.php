<?php
namespace frontend\models\Commands\UserCommands;


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

class ClearCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'clear';

    /**
     * @var string
     */
    protected $description = 'auth for bot users';

    /**
     * @var string
     */
    protected $usage = '/clear';

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


    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        $chat = $message->getChat();
        $user = $message->getFrom();
        $text = $message->getText(true);
        $text = $text == null ? null : trim($text);
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        // Preparing response
        $data = [
            'chat_id' => $chat_id,
            // Remove any keyboard by default
            'reply_markup' => Keyboard::remove(['selective' => true]),
        ];

        Token::deleteAll(['telegram_chat_id' => $chat_id]);

        $data['text'] = "OK";
        return Request::sendMessage($data);
    }
}