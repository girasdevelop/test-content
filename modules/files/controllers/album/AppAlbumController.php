<?php

namespace app\modules\files\controllers\album;

use app\modules\files\models\album\AppAlbum;

/**
 * AppAlbumController extends the base abstract AlbumController.
 *
 * @package Itstructure\FilesModule\controllers\album
 */
class AppAlbumController extends AlbumController
{
    /**
     * Returns the name of the AppAlbum model.
     *
     * @return string
     */
    protected function getModelName():string
    {
        return AppAlbum::class;
    }
}
