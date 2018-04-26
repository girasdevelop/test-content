<?php

namespace app\modules\files\interfaces;

use app\modules\files\models\Mediafile;

/**
 * Interface S3UploadComponentInterface
 *
 * @package Itstructure\FilesModule\interfaces
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
interface S3UploadComponentInterface
{
    /**
     * Sets a mediafile model for upload file.
     * @param $model $mediafileModel
     * @return S3UploadModelInterface
     */
    public function setModelForSave(Mediafile $mediafileModel): S3UploadModelInterface;

    /**
     * Sets a mediafile model for delete file.
     * @param $model $mediafileModel
     * @return S3UploadModelInterface
     */
    public function setModelForDelete(Mediafile $mediafileModel): S3UploadModelInterface;
}
