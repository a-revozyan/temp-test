<?php
$params = require __DIR__ . '/../../common/config/params.php';
if (YII_ENV == 'staging') $params = array_merge($params, require __DIR__ . '/../../common/config/params-staging.php');
if (YII_ENV == 'dev') $params =  array_merge($params, require __DIR__ . '/../../common/config/params-local.php');

$params =  array_merge($params, require __DIR__ . '/params.php');
if (YII_ENV == 'staging') $params = array_merge($params, require __DIR__ . '/params-staging.php');
if (YII_ENV == 'dev') $params = array_merge($params, require __DIR__ . '/params-local.php');

return [
    'id' => 'app-saas',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'saas\controllers',
    'bootstrap' => ['log'],
    'components' => [
        'telegram' => [
            'class' => 'aki\telegram\Telegram',
            'botToken' => '1166217116:AAF2WNJkzrGA90vTPVyeHjXv-qp08VIj754',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false,
            'identityCookie' => ['name' => '_identity-saas', 'httpOnly' => true],
        ],
        'request' => [
            'csrfParam' => '_csrf-saas',
            'baseUrl' => '/',
        ],
        'session' => [
            // this is the name of the session cookie used for login on the saas
            'name' => 'advanced-saas',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'class' => 'codemix\localeurls\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableDefaultLanguageUrlCode' => false,
            'ignoreLanguageUrlPatterns' => [
                '#^site/#' => '#^site/#',
            ],
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'site'],
                '/' => 'site/test',
            ],
        ],

    ],
    'params' => $params,
];
