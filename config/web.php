<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'language' => 'hr-HR', // Postavljanje jezika na hrvatski
    'name' => 'Moja Web Trgovina', // Naziv aplikacije
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'container' => [
           'definitions' => [
            // Globalno pravilo za Bootstrap 5 Breadcrumbs widget
            \yii\bootstrap5\Breadcrumbs::class => [
                'homeLink' => [
                    'label' => 'Početna', // Fiksna riječ za cijelu aplikaciju
                    'url' => ['/site/index'],
                ],
            ],
        ],
        'singletons' => 
        [
            \yii\mail\MailerInterface::class => [
                'class' => \yii\symfonymailer\Mailer::class,
                // send all mails to a file by default.
                'useFileTransport' => true,
                'viewPath' => '@app/mail',
            ],
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'SNcERtjFshIrg3TmhlXz67cCdSFLEe3a',
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'user' => [
            'identityClass' => \app\models\User::class,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'i18n' => [
        'translations' => [
            'app*' => [
                'class' => \yii\i18n\PhpMessageSource::class,
                'basePath' => '@app/messages', // Putanja do vaše nove mape
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'app' => 'app.php',
                ],
            ],
        ],
    ],

        'mailer' => \yii\mail\MailerInterface::class,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
