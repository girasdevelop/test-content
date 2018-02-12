<?php

use app\controllers\admin\CatalogController;
use Itstructure\AdminModule\Module as AdminModule;

return [
    'modules' => [
        'admin' => [
            'class' => AdminModule::class,
            'viewPath' => '@app/views/admin',
            'controllerMap' => [
                'catalog' => CatalogController::class,
            ],
            'components' => [
                'view' => require __DIR__ . '/view-component.php',
                'multilanguage-validate-component' => require __DIR__ .'/multilanguage-validate-component.php',
            ],
            'isMultilanguage' => true,
        ],
    ],
];
