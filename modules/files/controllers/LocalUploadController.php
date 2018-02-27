<?php

namespace app\modules\files\controllers;

use Yii;
use yii\helpers\Url;
use yii\base\{InvalidConfigException, UnknownMethodException};
use yii\web\{UploadedFile, BadRequestHttpException, NotFoundHttpException};
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
            'delete' => ['POST'],
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
        try {

            $file = UploadedFile::getInstanceByName($this->localUploadComponent->fileAttributeName);

            if (!$file){
                return $this->getFailResponse(
                    'File is absent.'
                );
            }

            $request = Yii::$app->request;

            $id = null !== $request->post('id') && !empty($request->post('id')) ? $request->post('id') : null;

            $this->uploadModel = $this->localUploadComponent->setModelForUpload(
                $this->setMediafileModel($id)
            );

            $this->uploadModel->setAttributes($request->post(), false);
            $this->uploadModel->setFile($file);


            if (!$this->uploadModel->save()){
                return $this->getFailResponse(
                    'Error to upload file.',
                    $this->uploadModel->errors
                );
            }

            if ($this->uploadModel->isImage()){
                $this->uploadModel->createThumbs();
            }

            $response['files'][] = [
                'id'            => $this->uploadModel->id,
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

        } catch (\Exception|InvalidConfigException|UnknownMethodException|NotFoundHttpException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete the media model entry with files.
     *
     * @throws BadRequestHttpException
     *
     * @return array
     */
    public function actionDelete()
    {
        try {
            $deleted = $this->deleteMediafileEntry(Yii::$app->request->post('id'));

            if (!$deleted){
                return $this->getFailResponse(
                    'Error to delete file.'
                );
            }

            return $this->getSuccessResponse(
                'Deleted '.$deleted.' files.'
            );

        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Find the media model entry.
     *
     * @param int $id
     *
     * @throws UnknownMethodException
     * @throws NotFoundHttpException
     *
     * @return Mediafile
     */
    private function findMediafileModel(int $id): Mediafile
    {
        $modelObject = new Mediafile();

        if (!method_exists($modelObject, 'findOne')){
            $class = (new\ReflectionClass($modelObject));
            throw new UnknownMethodException('Method findOne does not exists in ' . $class->getNamespaceName() . '\\' . $class->getShortName().' class.');
        }

        $result = call_user_func([
            $modelObject,
            'findOne',
        ], $id);

        if ($result !== null) {
            return $result;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Returns an intermediate model for working with the main.
     *
     * @param int|null $id
     *
     * @throws UnknownMethodException
     * @throws NotFoundHttpException
     *
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

    /**
     * Delete mediafile record with files.
     *
     * @param array|int|string $id
     *
     * @return bool|int
     */
    private function deleteMediafileEntry($id)
    {
        if (is_array($id)){
            $i = 0;
            foreach ($id as $item) {
                if (!$this->deleteMediafileEntry((int)$item)){
                    return false;
                }
                $i += 1;
            }
            return $i;

        } else {

            $this->uploadModel = $this->localUploadComponent->setModelForDelete(
                $this->findMediafileModel((int)$id)
            );

            if (!$this->uploadModel->delete()){
                return false;
            }

            return 1;
        }
    }
}
