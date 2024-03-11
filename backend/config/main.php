<?php
$params = require __DIR__ . '/../../common/config/params.php';
if (YII_ENV == 'staging') $params = array_merge($params, require __DIR__ . '/../../common/config/params-staging.php');
if (YII_ENV == 'dev') $params =  array_merge($params, require __DIR__ . '/../../common/config/params-local.php');

$params =  array_merge($params, require __DIR__ . '/params.php');
if (YII_ENV == 'staging') $params = array_merge($params, require __DIR__ . '/params-staging.php');
if (YII_ENV == 'dev') $params = array_merge($params,require __DIR__ . '/params-local.php');

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
        ],
    ],
    'components' => [
        'telegram' => [
            'class' => 'aki\telegram\Telegram',
            'botToken' => '1166217116:AAF2WNJkzrGA90vTPVyeHjXv-qp08VIj754',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'user' => [
            'identityClass' => 'mdm\admin\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
            'baseUrl' => '',
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        
    ],
    // 'as access' => [
    //     'class' => 'mdm\admin\components\AccessControl',
    //     'allowActions' => [
    //         //'site/*',
    //         'admin/user/login',
    //         'site/logout',
    //         'site/index',
    //         'gii/*',
    //         // 'some-controller/some-action',
    //         // The actions listed here will be allowed to everyone including guests.
    //         // So, 'admin/*' should not appear here in the production, of course.
    //         // But in the earlier stages of your development, you may probably want to
    //         // add a lot of actions here until you finally completed setting up rbac,
    //         // otherwise you may not even take a first step.
    //     ]
    // ],
    'params' => $params,
];
