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
}
