<?php

namespace app\modules\files\controllers\upload\common;

use Yii;
use yii\filters\{AccessControl, ContentNegotiator, VerbFilter};
use yii\base\{InvalidConfigException, UnknownMethodException};
use yii\web\{Controller, Response, UploadedFile, BadRequestHttpException, NotFoundHttpException};
use app\modules\files\Module;
use app\modules\files\components\LocalUploadComponent;
use app\modules\files\assets\UploadmanagerAsset;
use app\modules\files\models\Mediafile;
use app\modules\files\models\upload\BaseUpload;
use app\modules\files\traits\{ResponseTrait, MediaFilesTrait};
use app\modules\files\interfaces\{UploadComponentInterface, UploadModelInterface};

/**
 * Class CommonUploadController
 * Common upload controller class to upload files in local directory.
 *
 * @property UploadComponentInterface|LocalUploadComponent $uploadComponent
 * @property UploadModelInterface|BaseUpload $uploadModel
 * @property Module $module
 *
 * @package Itstructure\FilesModule\controllers\upload\common
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
abstract class CommonUploadController extends Controller
{
    use ResponseTrait, MediaFilesTrait;

    /**
     * @var string|array the configuration for creating the serializer that formats the response data.
     */
    public $serializer = 'yii\rest\Serializer';

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
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->accessRoles,
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    \Yii::$app->response->statusCode = 403;
                    \Yii::$app->response->data = $this->getFailResponse('Forbidden', [
                        'errors' => Yii::t('yii', 'You are not allowed to perform this action.')
                    ]);
                }
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        return $this->serializeData($result);
    }

    /**
     * @return array
     */
    public function verbs()
    {
        return [
            'save' => ['POST'],
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
     * Serializes the specified data.
     * The default implementation will create a serializer based on the configuration given by [[serializer]].
     * It then uses the serializer to serialize the given data.
     * @param mixed $data the data to be serialized
     * @return mixed the serialized data.
     */
    protected function serializeData($data)
    {
        return Yii::createObject($this->serializer)->serialize($data);
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
