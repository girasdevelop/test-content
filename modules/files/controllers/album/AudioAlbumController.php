<?php

namespace app\modules\files\controllers\album;

use app\modules\files\models\album\AudioAlbum;

/**
 * AudioAlbumController extends the base abstract AlbumController.
 *
 * @package Itstructure\FilesModule\controllers\album
 */
class AudioAlbumController extends AlbumController
{
    /**
     * Returns the name of the AudioAlbum model.
     *
     * @return string
     */
    protected function getModelName():string
    {
        return AudioAlbum::class;
    }

    /**
     * Returns the type of audio album.
     *
     * @return string
     */
    protected function getAlbumType():string
    {
        return AudioAlbum::ALBUM_TYPE_AUDIO;
    }
}
