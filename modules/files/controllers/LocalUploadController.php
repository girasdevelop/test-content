<?php

namespace app\modules\files\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\{UploadedFile, BadRequestHttpException};
use app\modules\files\models\{Mediafile, LocalUpload};
use app\modules\files\components\LocalUploadComponent;
use app\modules\files\interfaces\UploadModelInterface;

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

        $this->setUploadModel(
            $this->localUploadComponent->setModel(new Mediafile())
        );

        /* @var UploadModelInterface|LocalUpload $uploadModel */
        $uploadModel = $this->getUploadModel();
        $uploadModel->setAttributes(Yii::$app->request->post(), false);
        $uploadModel->setFile($file);

        try {
            $out =  $uploadModel->save();
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }

        if (false == $out){
            return $this->getFailResponse(
                'Error to upload file.',
                $uploadModel->errors
            );
        }

        if ($uploadModel->isImage()){
            $uploadModel->createThumbs($routes, $this->module->thumbs);
        }

        $response['files'][] = [
            'url'           => $uploadModel->mediafileModel->url,
            //'thumbnailUrl'  => $uploadModel->getDefaultThumbUrl($bundle->baseUrl),
            'name'          => $uploadModel->mediafileModel->filename,
            'type'          => $uploadModel->mediafileModel->type,
            'size'          => $uploadModel->mediafileModel->size,
            'deleteUrl'     => Url::to(['local-upload/delete', 'id' => $uploadModel->id]),
        ];

        return $this->getSuccessResponse(
            'File uploaded.',
            $response
        );
    }
}
