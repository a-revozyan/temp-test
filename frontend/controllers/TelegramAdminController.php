<?php

namespace frontend\controllers;

use common\helpers\GeneralHelper;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use yii\filters\VerbFilter;

class TelegramAdminController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'set' => ['GET'],
                'hook' => ['POST'],
            ]
        ];

        $behaviors['authenticator']['except'] = ["*"];
        return $behaviors;
    }

    public function actionSet()
    {
        $bot_api_key  = GeneralHelper::env('admin_telegram_bot_token');
        $bot_username = GeneralHelper::env('admin_telegram_bot_username');
        $hook_url     = GeneralHelper::env('frontend_project_website') . '/telegram-admin/hook';

        try {
            // Create Telegram API object
            $telegram = new Telegram($bot_api_key, $bot_username);

            // Set webhook
            $result = $telegram->setWebhook($hook_url);
            if ($result->isOk()) {
                return $result->getDescription();
            }
        } catch (TelegramException $e) {
            // log telegram errors
             return $e->getMessage();
        }
    }

    public function actionHook()
    {
        $bot_api_key  = GeneralHelper::env('admin_telegram_bot_token');
        $bot_username = GeneralHelper::env('admin_telegram_bot_username');

        try {
            // Create Telegram API object
            $telegram = new Telegram($bot_api_key, $bot_username);

            $commands_paths = [
                __DIR__ . '/../models/AdminTelegramCommands/AdminCommands',
            ];

            $servername = GeneralHelper::env('admin_telegram_bot_mysql_servername');
            $username = GeneralHelper::env('admin_telegram_bot_mysql_username');
            $password = GeneralHelper::env('admin_telegram_bot_mysql_password');
            $database = GeneralHelper::env('admin_telegram_bot_mysql_database');
            $conn = new \PDO("mysql:host=$servername;port=3306;dbname=$database", $username, $password, [
                \PDO::MYSQL_ATTR_SSL_CA => true,
                \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ]);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $telegram->enableExternalMySql($conn);

            $telegram->enableAdmins(explode(',', GeneralHelper::env('admin_chat_ids')));
            $telegram = $telegram->addCommandsPaths($commands_paths);

            // Handle telegram webhook request
            $telegram->handle();
        } catch (TelegramException $e) {
            // Silence is golden!
            // log telegram errors
             return $e->getMessage();
        }
    }
}