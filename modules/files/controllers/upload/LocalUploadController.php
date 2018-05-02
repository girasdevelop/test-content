<?php

namespace app\modules\files\controllers\upload;

use app\modules\files\components\LocalUploadComponent;
use app\modules\files\interfaces\UploadComponentInterface;

/**
 * Class LocalUploadController
 * Upload controller class to upload files in local directory.
 *
 * @property LocalUploadComponent $uploadComponent
 *
 * @package Itstructure\FilesModule\controllers\upload
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class LocalUploadController extends CommonUploadController
{
    /**
     * Get local upload component.
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
