<?php

return [
    'menuItems' => [
        'roles' => [
            'title' => 'Roles',
            'icon' => 'fa fa-user-circle-o',
            'url' => '/rbac/roles',
        ],
        'permissions' => [
            'title' => 'Permissions',
            'icon' => 'fa fa-user-secret',
            'url' => '/rbac/permissions',
        ],
        'catalog' => [
            'title' => 'Catalog',
            'icon' => 'fa fa-database',
            'url' => '#',
            'subItems' => [
                'subitem' => [
                    'title' => 'Sub-catalog',
                    'icon' => 'fa fa-link',
                    'url' => '/admin/catalog',
                ]
            ]
        ],
    ],
];
