<?php
namespace frontend\models\AdminTelegramCommands\AdminCommands;

use Longman\TelegramBot\Conversation;

class StatisticswkCommand extends StatisticsmhCommand
{
    /**
     * @var string
     */
    protected $name = 'statisticswk';

    /**
     * @var string
     */
    protected $description = 'get weekly statistics';

    /**
     * @var string
     */
    protected $usage = '/statisticswk';

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
        return date('Y-m-d', strtotime('-1 Monday'));
    }
}