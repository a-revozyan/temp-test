<?php

$params = require __DIR__ . '/../../common/config/params.php';
if (YII_ENV == 'staging') $params = array_merge($params, require __DIR__ . '/../../common/config/params-staging.php');
if (YII_ENV == 'dev') $params =  array_merge($params, require __DIR__ . '/../../common/config/params-local.php');

$params =  array_merge($params, require __DIR__ . '/params.php');
if (YII_ENV == 'staging') $params = array_merge($params, require __DIR__ . '/params-staging.php');
if (YII_ENV == 'dev') $params = array_merge($params,require __DIR__ . '/params-local.php');


return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'jwt' => [
            'class' => \bizley\jwt\Jwt::class,
            'signer' => \bizley\jwt\Jwt::HS256,
            'signingKey' => [
                'key' => 'VTLZxxfNwBKfEe4OLhaAmbMfHHaOUYo9kTQaYPCICclL4OP9o3',
                'passphrase' => 'VTLZxxfNwBKfEe4OLhaAmbMfHHaOUYo9kTQaYPCICclL4OP9o3',
                'method' => \bizley\jwt\Jwt::METHOD_BASE64,
            ],
        ],
        'telegram' => [
            'class' => 'aki\telegram\Telegram',
//            'botToken' => '1166217116:AAF2WNJkzrGA90vTPVyeHjXv-qp08VIj754',
            'botToken' => '5818261792:AAFNzKrE48WWWA3uNrHRHLWeeSoE8vwmF2U',
        ],

        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => '',
            'parsers' => [
                'multipart/form-data' => 'yii\web\MultipartFormDataParser'
            ]
        ],
        // 'response' => [
        //     'format' => yii\web\Response::FORMAT_JSON,
        // ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false,
            'loginUrl' => null,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@frontend/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'server1.ahost.uz', 
                // 'host' => 'smtp.gmail.com',
                // your host, here using fake email server (https://mailtrap.io/). You can use gmail: 'host' => 'smtp.gmail.com'
                'username' => 'info@netkost.uz',
                'password' => 'eu56Do2Hs8',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/myfolder/my-file.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
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
                $exception = Yii::$app->errorHandler->exception;
                $response = $event->sender;
                $code = $response->data['code'] ?? null;

                if ($exception instanceof \common\custom\exceptions\BadRequestHttpException){
                    $response->data = [
                        'error' => ['message' => $response->data['message'], 'code' => $code, 'additional' => $exception->additionalData]
                    ];
                }
                elseif ($response->data !== null and is_array($response->data) and array_key_exists('status', $response->data)) {
                    $message = $response->data['message'] ?? null;
                    if ($response->data["status"] == 401)
                        $message = Yii::t('app', 'please_login_message');

                    if (in_array($response->data["status"], [404, 400, 401]))
                        $response->data = [
                            'error' => ['message' => $message, 'code' => $code]
                        ];
                }
            },
        ],
        
        'urlManager' => [
            'class' => 'codemix\localeurls\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'languages' => ['ru', 'uz', 'en'],
            'enableDefaultLanguageUrlCode' => true,
            'ignoreLanguageUrlPatterns' => [
                '#^payme/#' => '#^payme/#',
                '#^rest/#' => '#^rest/#',
                '#^osagoapi/#' => '#^osagoapi/#',
                '#^kaskoapi/#' => '#^kaskoapi/#',
                '#^travelapi/#' => '#^travelapi/#',
                '#^userapi/#' => '#^userapi/#',
                '#^casco-step/#' => '#^casco-step/#',
                '#^surveyer/#' => '#^surveyer/#',
                '#^click/#' => '#^click/#',
                '#^profile/#' => '#^profile/#',
                '#^general/#' => '#^general/#',
                '#^travel/#' => '#^travel/#',
                '#^auth-jwt/#' => '#^auth-jwt/#',
                '#^osago-step/#' => '#^osago-step/#',
                '#^payze/#' => '#^payze/#',
                '#^swagger-ui/#' => '#^swagger-ui/#',
                '#^zood-pay/#' => '#^zood-pay/#',
                '#^telegram/#' => '#^telegram/#',
                '#^kasko-by-subscription-step/#' => '#^kasko-by-subscription-step/#',
                '#^telegram-admin/#' => '#^telegram-admin/#',
                '#^travel-step/#' => '#^travel-step/#',
                '#^client/#' => '#^client/#',
                '#^partner-auto/#' => '#^partner-auto/#',
                '#^telegram-car-price/#' => '#^telegram-car-price/#',
                '#^client-auto/#' => '#^client-auto/#',
                '#^news/#' => '#^news/#',
                '#^agent-osago/#' => '#^agent-osago/#',
                '#^story/#' => '#^story/#',
            ],
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'payme'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'rest'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'osagoapi'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'kaskoapi'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'travelapi'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'userapi'],
                '/' => 'site/index',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'params' => $params,
];
