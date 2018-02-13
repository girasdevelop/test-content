<?php

use app\controllers\admin\CatalogController;
use Itstructure\RbacModule\controllers\{RolesController, PermissionsController};
use Itstructure\AdminModule\Module as AdminModule;
use Itstructure\RbacModule\Module as RbacModule;

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
        'rbac' => [
            'class' => RbacModule::class,
            'layout' => '@admin/views/layouts/main-admin.php',
            'controllerMap' => [
                'roles' => RolesController::class,
                'permissions' => PermissionsController::class,
            ],
            'components' => [
                'view' => require __DIR__ . '/view-component.php',
            ]
        ],
    ],
];
