<?php

namespace app\modules\files\controllers\album;

use app\modules\files\models\album\ImageAlbum;

/**
 * ImageAlbumController extends the base abstract AlbumController.
 *
 * @package Itstructure\FilesModule\controllers\album
 */
class ImageAlbumController extends AlbumController
{
    /**
     * Returns the name of the ImageAlbum model.
     *
     * @return string
     */
    protected function getModelName():string
    {
        return ImageAlbum::class;
    }

    /**
     * Returns the type of image album.
     *
     * @return string
     */
    protected function getAlbumType():string
    {
        return ImageAlbum::ALBUM_TYPE_IMAGE;
    }
}
