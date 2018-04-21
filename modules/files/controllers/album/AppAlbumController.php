<?php

namespace app\modules\files\controllers\album;

use app\modules\files\models\album\AppAlbum;

/**
 * AppAlbumController extends the base abstract AlbumController.
 *
 * @package Itstructure\FilesModule\controllers\album
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class AppAlbumController extends AlbumController
{
    /**
     * Returns the name of the AppAlbum model.
     * @return string
     */
    protected function getModelName():string
    {
        return AppAlbum::class;
    }

    /**
     * Returns the type of application album.
     * @return string
     */
    protected function getAlbumType():string
    {
        return AppAlbum::ALBUM_TYPE_APP;
    }
}
