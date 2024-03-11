<?php
namespace frontend\models\AdminTelegramCommands\AdminCommands;

use Longman\TelegramBot\Conversation;

class StatisticsdyCommand extends StatisticsmhCommand
{
    /**
     * @var string
     */
    protected $name = 'statisticsdy';

    /**
     * @var string
     */
    protected $description = 'get dayly statistics';

    /**
     * @var string
     */
    protected $usage = '/statisticsdy';

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
        return date('Y-m-d');
    }
}