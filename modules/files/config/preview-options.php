<?php

use app\modules\files\Module;
use app\modules\files\interfaces\UploadModelInterface;

/**
 * Preview options for som types of mediafiles according with their location.
 */
return [
    UploadModelInterface::FILE_TYPE_IMAGE => [
        'existing' => [
            'mainTag' => [
                'alias' => Module::MEDIUM_THUMB_ALIAS
            ]
        ],
        'fileinfo' => [
            'mainTag' => [
                'alias' => Module::DEFAULT_THUMB_ALIAS
            ]
        ],
        'fileitem' => [
            'mainTag' => [
                'alias' => Module::DEFAULT_THUMB_ALIAS
            ]
        ],
    ],
    UploadModelInterface::FILE_TYPE_AUDIO => [
        'existing' => [
            'mainTag' => [
                'width' => Module::ORIGINAL_PREVIEW_WIDTH
            ]
        ],
        'fileinfo' => [
            'mainTag' => [
                'width' => Module::ORIGINAL_PREVIEW_WIDTH
            ]
        ],
        'fileitem' => [
            'mainTag' => [
                'width' => Module::ORIGINAL_PREVIEW_WIDTH
            ]
        ],
    ],
    UploadModelInterface::FILE_TYPE_VIDEO => [
        'existing' => [
            'mainTag' => [
                'width' => Module::ORIGINAL_PREVIEW_WIDTH,
                'height' => Module::ORIGINAL_PREVIEW_HEIGHT,
            ]
        ],
        'fileinfo' => [
            'mainTag' => [
                'width' => Module::ORIGINAL_PREVIEW_WIDTH,
                'height' => Module::ORIGINAL_PREVIEW_HEIGHT,
            ]
        ],
        'fileitem' => [
            'mainTag' => [
                'width' => Module::ORIGINAL_PREVIEW_WIDTH,
                'height' => Module::ORIGINAL_PREVIEW_HEIGHT,
            ]
        ],
    ],
    UploadModelInterface::FILE_TYPE_APP => [
        'fileitem' => [
            'mainTag' => [
                'width' => Module::SCANTY_PREVIEW_SIZE,
            ]
        ],
    ],
    UploadModelInterface::FILE_TYPE_TEXT => [
        'fileitem' => [
            'mainTag' => [
                'width' => Module::SCANTY_PREVIEW_SIZE,
            ]
        ],
    ],
    UploadModelInterface::FILE_TYPE_OTHER => [
        'fileitem' => [
            'mainTag' => [
                'width' => Module::SCANTY_PREVIEW_SIZE,
            ]
        ],
    ],
];
