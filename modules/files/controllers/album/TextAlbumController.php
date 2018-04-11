<?php

namespace app\modules\files\controllers\album;

use app\modules\files\models\album\TextAlbum;

/**
 * TextAlbumController extends the base abstract AlbumController.
 *
 * @package Itstructure\FilesModule\controllers\album
 */
class TextAlbumController extends AlbumController
{
    /**
     * Returns the name of the TextAlbum model.
     *
     * @return string
     */
    protected function getModelName():string
    {
        return TextAlbum::class;
    }

    /**
     * Returns the type of text album.
     *
     * @return string
     */
    protected function getAlbumType():string
    {
        return TextAlbum::ALBUM_TYPE_TEXT;
    }
}
