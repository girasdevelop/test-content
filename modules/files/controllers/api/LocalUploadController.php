<?php

namespace app\modules\files\controllers\api;

use app\modules\files\components\LocalUploadComponent;
use app\modules\files\interfaces\UploadComponentInterface;
use app\modules\files\controllers\api\common\CommonUploadController;

/**
 * Class LocalUploadController
 * Upload controller class to upload files in local directory.
 *
 * @property LocalUploadComponent $localUploadComponent
 *
 * @package Itstructure\FilesModule\controllers
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
