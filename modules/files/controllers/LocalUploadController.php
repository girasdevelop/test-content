<?php

namespace app\modules\files\controllers;

use Yii;
use yii\helpers\Url;
use yii\base\InvalidConfigException;
use yii\web\{UploadedFile, BadRequestHttpException};
use app\modules\files\models\Mediafile;
use app\modules\files\components\LocalUploadComponent;

/**
 * Class LocalUploadController
 * Upload controller class to upload files in local directory.
 *
 * @property LocalUploadComponent $localUploadComponent
 *
 * @package Itstructure\FilesModule\controllers
 */
class LocalUploadController extends CommonRestController
{
    /**
     * @var LocalUploadComponent LocalUploadComponent
     */
    private $localUploadComponent;

    /**
     * Initialize.
     */
    public function init()
    {
        $this->localUploadComponent = $this->module->get('local-upload-component');

        $this->enableCsrfValidation = $this->localUploadComponent->enableCsrfValidation;

        $this->authenticator     = $this->module->authenticator;
        $this->rateLimiter       = $this->module->rateLimiter;
        $this->contentNegotiator = $this->module->contentNegotiator;
    }

    /**
     * @return array
     */
    public function verbs()
    {
        return [
            'upload' => ['POST'],
        ];
    }

    /**
     * Provides upload file.
     *
     * @throws BadRequestHttpException
     *
     * @return array
     */
    public function actionUpload()
    {
        $file = UploadedFile::getInstanceByName($this->localUploadComponent->fileAttributeName);

        if (!$file){
            return $this->getFailResponse(
                'File is absent.'
            );
        }

        $this->uploadModel = $this->localUploadComponent->setModel(new Mediafile());

        $this->uploadModel->setAttributes(Yii::$app->request->post(), false);
        $this->uploadModel->setFile($file);

        try {

            if (!$this->uploadModel->save()){
                return $this->getFailResponse(
                    'Error to upload file.',
                    $this->uploadModel->errors
                );
            }

            if ($this->uploadModel->isImage()){
                $this->uploadModel->createThumbs();
            }

        } catch (\Exception|InvalidConfigException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }

        $response['files'][] = [
            'url'           => $this->uploadModel->mediafileModel->url,
            'thumbnailUrl'  => $this->uploadModel->getDefaultThumbUrl(),
            'name'          => $this->uploadModel->mediafileModel->filename,
            'type'          => $this->uploadModel->mediafileModel->type,
            'size'          => $this->uploadModel->mediafileModel->size,
            'deleteUrl'     => Url::to(['local-upload/delete', 'id' => $this->uploadModel->id]),
        ];

        return $this->getSuccessResponse(
            'File uploaded.',
            $response
        );
    }
}
