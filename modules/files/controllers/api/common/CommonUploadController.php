<?php

namespace app\modules\files\controllers\api\common;

use Yii;
use yii\rest\Controller as RestController;
use yii\base\{InvalidConfigException, UnknownMethodException};
use yii\web\{UploadedFile, BadRequestHttpException, NotFoundHttpException};
use app\modules\files\Module;
use app\modules\files\components\LocalUploadComponent;
use app\modules\files\assets\UploadmanagerAsset;
use app\modules\files\models\Mediafile;
use app\modules\files\models\upload\BaseUpload;
use app\modules\files\traits\{BehaviorsTrait, ResponseTrait, MediaFilesTrait};
use app\modules\files\interfaces\{UploadComponentInterface, UploadModelInterface};

/**
 * Class CommonUploadController
 * Common upload controller class to upload files in local directory.
 *
 * @property UploadComponentInterface|LocalUploadComponent $uploadComponent
 * @property UploadModelInterface|BaseUpload $uploadModel
 * @property Module $module
 *
 * @package Itstructure\FilesModule\controllers\api\common
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
abstract class CommonUploadController extends RestController
{
    use BehaviorsTrait, ResponseTrait, MediaFilesTrait;

    /**
     * @var null|UploadComponentInterface|LocalUploadComponent
     */
    protected $uploadComponent = null;

    /**
     * @var UploadModelInterface|BaseUpload
     */
    private $uploadModel;

    /**
     * @return UploadComponentInterface|LocalUploadComponent
     */
    abstract protected function getUploadComponent(): UploadComponentInterface;

    /**
     * Initialize.
     */
    public function init()
    {
        $this->enableCsrfValidation = $this->module->enableCsrfValidation;
        $this->authenticator        = $this->module->authenticator;
        $this->rateLimiter          = $this->module->rateLimiter;
        $this->contentNegotiator    = $this->module->contentNegotiator;
    }

    /**
     * @return array
     */
    public function verbs()
    {
        return [
            'upload' => ['POST'],
            'delete' => ['POST'],
        ];
    }

    /**
     * Set upload model.
     * @param UploadModelInterface $model
     * @return void
     */
    public function setUploadModel(UploadModelInterface $model): void
    {
        $this->uploadModel = $model;
    }

    /**
     * Returns upload model.
     * @return UploadModelInterface
     */
    public function getUploadModel(): UploadModelInterface
    {
        return $this->uploadModel;
    }

    /**
     * Provides upload file.
     * @throws BadRequestHttpException
     * @return array
     */
    public function actionSave()
    {
        try {
            $request = Yii::$app->request;

            $this->uploadModel = $this->getUploadComponent()->setModelForSave(
                $this->setMediafileModel(!empty($request->post('id')) ? $request->post('id') : null)
            );

            $this->uploadModel->setAttributes($request->post(), false);
            $this->uploadModel->setFile(UploadedFile::getInstanceByName($this->module->fileAttributeName));

            if (!$this->uploadModel->save()){
                return $this->getFailResponse(Module::t('actions', 'Error to save file.'), [
                    'errors' => $this->uploadModel->errors
                ]);
            }

            if ($this->uploadModel->mediafileModel->isImage()){
                $this->uploadModel->createThumbs();
            }

            $response['files'][] = $this->getUploadResponse();

            return $this->getSuccessResponse(Module::t('actions', 'File saved.'), $response);

        } catch (\Exception|InvalidConfigException|UnknownMethodException|NotFoundHttpException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete the media model entry with files.
     * @throws BadRequestHttpException
     * @return array
     */
    public function actionDelete()
    {
        try {
            $deleted = $this->deleteMediafileEntry(Yii::$app->request->post('id'), $this->module);

            if (!$deleted){
                return $this->getFailResponse(
                    Module::t('actions', 'Error to delete file.')
                );
            }

            return $this->getSuccessResponse(
                Module::t('actions', 'Deleted {0} files.', $deleted)
            );

        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Response with uploaded file data.
     * @return array
     */
    private function getUploadResponse(): array
    {
        return [
            'id'            => $this->uploadModel->id,
            'url'           => $this->uploadModel->mediafileModel->url,
            'thumbnailUrl'  => $this->uploadModel->mediafileModel->getDefaultThumbUrl(UploadmanagerAsset::register($this->view)->baseUrl),
            'name'          => $this->uploadModel->mediafileModel->filename,
            'type'          => $this->uploadModel->mediafileModel->type,
            'size'          => $this->uploadModel->mediafileModel->size,
        ];
    }

    /**
     * Returns an intermediate model for working with the main.
     * @param int|null $id
     * @throws UnknownMethodException
     * @throws NotFoundHttpException
     * @return Mediafile
     */
    private function setMediafileModel(int $id = null): Mediafile
    {
        if (null === $id){
            return new Mediafile();
        } else {
            return $this->findMediafileModel($id);
        }
    }
}
