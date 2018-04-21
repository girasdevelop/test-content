<?php

use app\modules\files\Module;

/**
 * Thumbs config with their types and sizes.
 */
return [
    Module::SMALL_THUMB_ALIAS => [
        'name' => 'Small size',
        'size' => [120, 80],
    ],
    Module::MEDIUM_THUMB_ALIAS => [
        'name' => 'Medium size',
        'size' => [300, 240],
    ],
    Module::LARGE_THUMB_ALIAS => [
        'name' => 'Large size',
        'size' => [800, 600],
    ],
];