<?php
namespace frontend\models\AdminTelegramCommands\AdminCommands;

use backapi\models\searchs\HomeStatisticsSearch;
use common\helpers\GeneralHelper;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Yii;
use yii\helpers\VarDumper;

class StatisticsmhCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'statisticsmh';

    /**
     * @var string
     */
    protected $description = 'get monthly statistics';

    /**
     * @var string
     */
    protected $usage = '/statisticsmh';

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
    protected $private_only = false;

    /**
     * Conversation Object
     *
     * @var Conversation
     */
    protected $conversation;

    public function getBeginDate()
    {
        return date('Y-m-01');
    }


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

        if (!in_array($chat_id, GeneralHelper::env('admin_chat_ids')))
            return Request::emptyResponse();

        // Preparing response
        $data = [
            'chat_id' => $chat_id,
            // Remove any keyboard by default
            'reply_markup' => Keyboard::remove(['selective' => true]),
        ];

        $search_model = new HomeStatisticsSearch();

        if (empty($text) or !str_contains($text, ','))
            $text = $text.",";
        [$begin_date, $end_date] = explode(',', $text);
        if (empty($begin_date))
            $begin_date = $this->getBeginDate();
        if (empty($end_date) and get_class($this) == StatisticsdyCommand::class)
            $end_date = $begin_date;

        $statistics = $search_model->search(['begin_date' => $begin_date, 'end_date' => $end_date]);

        $total_count = $statistics['product_count']['total'];
        $total_amount = $statistics['amount_sum']['total'];
        $new_users = $statistics['new_users'];
        $average_osago_check = $statistics['average_osago_check'];

        $osago_count = $statistics['product_count']['osago'];
        $accident_count = $statistics['product_count']['accident'];
        $kasko_count = $statistics['product_count']['kasko'];
        $kbsp_count = $statistics['product_count']['kbsp'];

        $data['text'] = <<<HTML
<b>Кол-во:</b> $total_count шт.
<b>Сумма:</b> $total_amount сум
<b>ОСАГО:</b> $osago_count шт.
<b>Accident:</b> $accident_count шт.
<b>КАСКО:</b> $kasko_count шт.
<b>КАСКО под:</b> $kbsp_count шт.
<b>Кол-во новых пользователей:</b> +$new_users 
<b>Средний чек ОСАГО:</b> $average_osago_check сум
HTML;

        $data['parse_mode'] = 'html';
        return Request::sendMessage($data);
    }
}