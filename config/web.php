<?php

use Itstructure\MFUploader\Module as MFUModule;
use Itstructure\MFUploader\controllers\ManagersController;
use Itstructure\MFUploader\controllers\upload\{LocalUploadController, S3UploadController};
use Itstructure\MFUploader\controllers\album\{
    ImageAlbumController,
    AudioAlbumController,
    VideoAlbumController,
    ApplicationAlbumController,
    TextAlbumController,
    OtherAlbumController
};
use Itstructure\MFUploader\components\{LocalUploadComponent, S3UploadComponent};

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'DKV574pMvwzLfHxbPhPy_2eBjtO40695',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
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
                '/users' => 'users/profile',
                '/admin' => 'admin/settings',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'local-upload',
                ],
                '<controller>/<action>' => '<controller>/<action>',
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
                'settings*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'settings' => 'settings.php',
                    ],
                ],
            ]
        ],
    ],
    'defaultRoute' => 'site',
    'params' => $params,
    'modules' => [
        'mfuploader' => [
            'class' => MFUModule::class,
            'layout' => '@admin/views/layouts/main-admin.php',
            'controllerMap' => [
                'upload/local-upload' => LocalUploadController::class,
                'upload/s3-upload' => S3UploadController::class,
                'managers' => ManagersController::class,
                'image-album' => ImageAlbumController::class,
                'audio-album' => AudioAlbumController::class,
                'video-album' => VideoAlbumController::class,
                'application-album' => ApplicationAlbumController::class,
                'text-album' => TextAlbumController::class,
                'other-album' => OtherAlbumController::class,
            ],
            'accessRoles' => ['@', '?'],
            'defaultStorageType' => MFUModule::STORAGE_TYPE_LOCAL,
            'components' => [
                'local-upload-component' => [
                    'class' => LocalUploadComponent::class,
                    'checkExtensionByMimeType' => false
                ],
                's3-upload-component' => [
                    'class' => S3UploadComponent::class,
                    'checkExtensionByMimeType' => false,
                    'credentials' => require __DIR__ . '/aws-credentials.php',
                    'region' => 'us-west-2',
                    's3DefaultBucket' => 'filesmodule2',
                ],
                'view' => require __DIR__ . '/admin/view-component.php',
            ],
        ]
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
