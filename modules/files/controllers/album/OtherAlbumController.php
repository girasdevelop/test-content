<?php

namespace app\modules\files\controllers\album;

use app\modules\files\models\album\OtherAlbum;

/**
 * OtherAlbumController extends the base abstract AlbumController.
 *
 * @package Itstructure\FilesModule\controllers\album
 */
class OtherAlbumController extends AlbumController
{
    /**
     * Returns the name of the OtherAlbum model.
     *
     * @return string
     */
    protected function getModelName():string
    {
        return OtherAlbum::class;
    }

    /**
     * Returns the type of other album.
     *
     * @return string
     */
    protected function getAlbumType():string
    {
        return OtherAlbum::ALBUM_TYPE_OTHER;
    }
}
