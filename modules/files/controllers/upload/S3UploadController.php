<?php

namespace app\modules\files\controllers\upload;

use app\modules\files\components\S3UploadComponent;
use app\modules\files\interfaces\UploadComponentInterface;

/**
 * Class S3UploadController
 * Upload controller class to upload files in amazon s3 buckets.
 *
 * @property S3UploadComponent $uploadComponent
 *
 * @package Itstructure\FilesModule\controllers\upload
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class S3UploadController extends CommonUploadController
{
    /**
     * Get s3 upload component.
     * @return UploadComponentInterface|S3UploadComponent
     */
    protected function getUploadComponent(): UploadComponentInterface
    {
        if (null === $this->uploadComponent){
            $this->uploadComponent = $this->module->get('s3-upload-component');
        }

        return $this->uploadComponent;
    }
}
