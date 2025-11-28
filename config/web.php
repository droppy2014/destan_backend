<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '4Y6RcQhoW7IexwhHpjIK03tgOOJE_fyg',
            'parsers' => [
                'application/json' => \yii\web\JsonParser::class,
            ],
        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
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
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'api/posts/create' => 'api/post/create',
                'api/posts' => 'api/post/index',
            ],
        ],
        'response' => [
            'class' => \yii\web\Response::class,
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $headers = $response->headers;

                // Разрешаем фронту
                $headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
                $headers->set('Access-Control-Allow-Credentials', 'true');
                $headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
                $headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

                // опционально: если хочешь, чтобы все ответы API были JSON,
                // можно условно проверять URL и ставить формат:
                // if (strpos(Yii::$app->request->url, '/api/') === 0) {
                //     $response->format = \yii\web\Response::FORMAT_JSON;
                // }
            },
        ],
    ],
    'container' => [
        'singletons' => [
            \app\repositories\PostRepositoryInterface::class => \app\repositories\PostRepository::class,
            \app\services\PostService::class => \app\services\PostService::class,
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            'allowedIPs' => ['176.117.195.118','127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
