<?php

return [
    'menuItems' => [
        'users' => [
            'title' => 'Users',
            'icon' => 'fa fa-users',
            'url' => '/users/profile',
        ],
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
