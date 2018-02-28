<?php

namespace app\modules\files\controllers\api;

use app\modules\files\components\LocalUploadComponent;
use app\modules\files\interfaces\UploadComponentInterface;

/**
 * Class LocalUploadController
 * Upload controller class to upload files in local directory.
 *
 * @property LocalUploadComponent $localUploadComponent
 *
 * @package Itstructure\FilesModule\controllers
 */
class LocalUploadController extends CommonUploadController
{
    /**
     * Get local upload component.
     *
     * @return UploadComponentInterface|LocalUploadComponent
     */
    protected function getUploadComponent(): UploadComponentInterface
    {
        if (null === $this->uploadComponent){
            $this->uploadComponent = $this->module->get('local-upload-component');
        }

        return $this->uploadComponent;
    }
}
