<?php
$params = require __DIR__ . '/../../common/config/params.php';
if (YII_ENV == 'staging') $params = array_merge($params, require __DIR__ . '/../../common/config/params-staging.php');
if (YII_ENV == 'dev') $params =  array_merge($params, require __DIR__ . '/../../common/config/params-local.php');

$params =  array_merge($params, require __DIR__ . '/params.php');
if (YII_ENV == 'staging') $params = array_merge($params, require __DIR__ . '/params-staging.php');
if (YII_ENV == 'dev') $params = array_merge($params,require __DIR__ . '/params-local.php');

return [
    'id' => 'app-backapi',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backapi\controllers',
    'bootstrap' => ['log'],
    'modules' => [

    ],
    'components' => [
        'telegram' => [
            'class' => 'aki\telegram\Telegram',
            'botToken' => '1166217116:AAF2WNJkzrGA90vTPVyeHjXv-qp08VIj754',
        ],
        'user' => [
            'identityClass' => 'backapi\models\User',
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-backapi', 'httpOnly' => true],
            'enableSession' => false,
            'loginUrl' => null
        ],
        'request' => [
            'csrfParam' => '_csrf-backapi',
            'baseUrl' => '',
            'parsers' => [
                'multipart/form-data' => 'yii\web\MultipartFormDataParser'
            ]
        ],
        'response' => [
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],

            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null and is_array($response->data) and array_key_exists('status', $response->data)) {
                    if ($response->data["status"] == 401)
                        $response->data = [
                            'error' => ['message' => Yii::t('app', 'please_login_message')]
                        ];

                    if (in_array($response->data["status"], [404, 400]))
                        $response->data = [
                            'error' => ['message' => $response->data['message']]
                        ];
                }
            },
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backapi
            'name' => 'advanced-backapi',
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
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backapi/messages', // if advanced application, set @frontend/messages
                    'sourceLanguage' => 'en',
                    'fileMap' => [
                        //'main' => 'main.php',
                    ],
                ],
            ],
        ],
        
    ],
    'aliases' => [
        '@backapi' => '@common/../backapi',
    ],
    'params' => $params,
];
