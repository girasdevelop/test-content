<?php

namespace app\modules\files\interfaces;

use app\modules\files\models\Mediafile;

/**
 * Interface UploadComponentInterface
 *
 * @package Itstructure\FilesModule\interfaces
 */
interface UploadComponentInterface
{
    /**
     * Search model data.
     *
     * @param $model $mediafileModel
     *
     * @return UploadModelInterface
     */
    public function setModel(Mediafile $mediafileModel): UploadModelInterface;
}
