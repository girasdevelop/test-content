<?php

namespace app\modules\files\controllers\album;

use app\modules\files\models\album\VideoAlbum;

/**
 * VideoAlbumController extends the base abstract AlbumController.
 *
 * @package Itstructure\FilesModule\controllers\album
 */
class VideoAlbumController extends AlbumController
{
    /**
     * Returns the name of the VideoAlbum model.
     *
     * @return string
     */
    protected function getModelName():string
    {
        return VideoAlbum::class;
    }
}
