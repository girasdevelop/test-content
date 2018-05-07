<?php

return [
    'menuItems' => [
        'settings' => [
            'title' => 'Settings',
            'icon' => 'fa fa-cog',
            'url' => '/admin/settings',
        ],
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
        'albums' => [
            'title' => 'Albums',
            'icon' => 'fa fa-book',
            'url' => '#',
            'subItems' => [
                'imageAlbums' => [
                    'title' => 'Image albums',
                    'icon' => 'fa fa-picture-o',
                    'url' => '/mfuploader/image-album',
                ],
                'audioAlbums' => [
                    'title' => 'Audio albums',
                    'icon' => 'fa fa-headphones',
                    'url' => '/mfuploader/audio-album',
                ],
                'videoAlbums' => [
                    'title' => 'Video albums',
                    'icon' => 'fa fa-video-camera',
                    'url' => '/mfuploader/video-album',
                ],
                'appAlbums' => [
                    'title' => 'Application albums',
                    'icon' => 'fa fa-microchip',
                    'url' => '/mfuploader/application-album',
                ],
                'textAlbums' => [
                    'title' => 'Text albums',
                    'icon' => 'fa fa-file-text',
                    'url' => '/mfuploader/text-album',
                ],
                'otherAlbums' => [
                    'title' => 'Other albums',
                    'icon' => 'fa fa-file',
                    'url' => '/mfuploader/other-album',
                ],
            ]
        ],
    ],
];
