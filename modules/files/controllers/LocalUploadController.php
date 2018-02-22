<?php

namespace app\modules\files\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\{Url, ArrayHelper};
use yii\web\{UploadedFile, BadRequestHttpException};
use app\modules\files\models\{Mediafile, LocalUpload};
use app\modules\files\components\LocalUploadComponent;

class LocalUploadController extends RestController
{
    /**
     * @var LocalUploadComponent
     */
    private $localUploadComponent;

    /**
     * Initialize.
     */
    public function init()
    {
        $this->localUploadComponent = $this->module->get('local-upload-component');

        $this->enableCsrfValidation = $this->localUploadComponent->enableCsrfValidation;

        $this->authenticator = $this->module->authenticator;
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'upload' => ['post'],
                ],
            ],
        ]);
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

        /* @var LocalUpload $uploadModel */
        $uploadModel = $this->localUploadComponent->setModel(new Mediafile());

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
