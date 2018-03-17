<?php

namespace app\modules\files\controllers\api\common;

use Yii;
use yii\base\{InvalidConfigException, UnknownMethodException};
use yii\web\{UploadedFile, BadRequestHttpException, NotFoundHttpException};
use app\modules\files\components\LocalUploadComponent;
use app\modules\files\models\Mediafile;
use app\modules\files\models\upload\LocalUpload;
use app\modules\files\interfaces\{UploadComponentInterface, UploadModelInterface};

/**
 * Class CommonUploadController
 * Common upload controller class to upload files in local directory.
 *
 * @property UploadComponentInterface|LocalUploadComponent $uploadComponent
 * @property UploadModelInterface|LocalUpload $uploadModel
 *
 * @package Itstructure\FilesModule\controllers\api\common
 */
abstract class CommonUploadController extends CommonRestController
{
    /**
     * @var null|UploadComponentInterface|LocalUploadComponent
     */
    protected $uploadComponent = null;

    /**
     * @var UploadModelInterface|LocalUpload
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
        $this->enableCsrfValidation = $this->getUploadComponent()->enableCsrfValidation;

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
     * Set upload model.
     *
     * @param UploadModelInterface $model
     *
     * @return void
     */
    public function setUploadModel(UploadModelInterface $model): void
    {
        $this->uploadModel = $model;
    }

    /**
     * Returns upload model.
     *
     * @return UploadModelInterface
     */
    public function getUploadModel(): UploadModelInterface
    {
        return $this->uploadModel;
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

            $file = UploadedFile::getInstanceByName($this->getUploadComponent()->fileAttributeName);

            if (!$file){
                return $this->getFailResponse('File is absent.');
            }

            $request = Yii::$app->request;

            $this->uploadModel = $this->getUploadComponent()->setModelForSave(
                $this->setMediafileModel(!empty($request->post('id')) ? $request->post('id') : null)
            );

            $this->uploadModel->setAttributes($request->post(), false);
            $this->uploadModel->setFile($file);


            if (!$this->uploadModel->save()){
                return $this->getFailResponse('Error to upload file.', $this->uploadModel->errors);
            }

            if ($this->uploadModel->isImage()){
                $this->uploadModel->createThumbs();
            }

            $response['files'][] = $this->getUploadResponse();

            return $this->getSuccessResponse('File uploaded.', $response);

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
     * Response with uploaded file data.
     *
     * @return array
     */
    private function getUploadResponse(): array
    {
        return [
            'id'            => $this->uploadModel->id,
            'url'           => $this->uploadModel->mediafileModel->url,
            'thumbnailUrl'  => $this->uploadModel->getDefaultThumbUrl(),
            'name'          => $this->uploadModel->mediafileModel->filename,
            'type'          => $this->uploadModel->mediafileModel->type,
            'size'          => $this->uploadModel->mediafileModel->size,
        ];
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

            $this->uploadModel = $this->getUploadComponent()->setModelForDelete(
                $this->findMediafileModel((int)$id)
            );

            if (!$this->uploadModel->delete()){
                return false;
            }

            return 1;
        }
    }
}
