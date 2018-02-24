<?php

namespace app\modules\files\controllers;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use app\modules\files\models\LocalUpload;
use app\modules\files\interfaces\UploadModelInterface;

/**
 * Class RestController
 * Common rest controller.
 *
 * @property array|null $authenticator
 * @property array|null $rateLimiter
 * @property array|null $contentNegotiator
 * @property UploadModelInterface|LocalUpload $uploadModel
 *
 * @package Itstructure\FilesModule\controllers
 */
class CommonRestController extends Controller
{
    /**
     * Auth filter.
     *
     * @var array|null
     */
    protected $authenticator = null;

    /**
     * Rate limit filter.
     *
     * @var array|null
     */
    protected $rateLimiter = null;

    /**
     * Content negotiator filter.
     *
     * @var array|null
     */
    protected $contentNegotiator = null;

    /**
     * @var UploadModelInterface|LocalUpload
     */
    private $uploadModel;

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        if (null !== $this->authenticator){

            $this->checkConfigureFormat($this->authenticator);

            $behaviors = ArrayHelper::merge($behaviors, [
                'authenticator' => $this->authenticator
            ]);
        }

        if (null !== $this->rateLimiter){

            $this->checkConfigureFormat($this->rateLimiter);

            $behaviors = ArrayHelper::merge($behaviors, [
                'rateLimiter' => $this->rateLimiter
            ]);
        }

        if (null !== $this->contentNegotiator){

            $this->checkConfigureFormat($this->contentNegotiator);

            $behaviors = ArrayHelper::merge($behaviors, [
                'contentNegotiator' => $this->contentNegotiator
            ]);
        }

        return $behaviors;
    }

    /**
     * Set upload model.
     *
     * @param UploadModelInterface $model
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
     * Returns success response.
     *
     * @param string     $message
     * @param array|null $data
     *
     * @return array
     */
    protected function getSuccessResponse(string $message, array $data = null): array
    {
        return $this->getStatusResponse($message, 200, $data);
    }

    /**
     * Returns fail response.
     *
     * @param string     $message
     * @param array|null $data
     *
     * @return array
     */
    protected function getFailResponse(string $message, array $data = null): array
    {
        return $this->getStatusResponse($message, 400, $data);
    }

    /**
     * Returns status, message and code.
     *
     * @param string     $message
     * @param int        $statusCode
     * @param array|null $data
     *
     * @return array
     */
    private function getStatusResponse(string $message, int $statusCode, array $data = null): array
    {
        if (null === $data) {
            $data = (object)[];
        }

        Yii::$app->response->statusCode = $statusCode;

        return [
            'meta' => [
                'status' => $statusCode == 200 ? 'success' : 'fail',
                'message' => $message,
            ],
            'data' => $data,
        ];
    }

    /**
     * @param $parameter
     *
     * @throws InvalidConfigException
     */
    private function checkConfigureFormat($parameter)
    {
        if (!is_array($parameter)){
            throw new InvalidConfigException('Parameter '.$parameter.' must be an array.');
        }

        if (!array_key_exists('class', $parameter)){
            throw new InvalidConfigException('Parameter '.$parameter.' must contain a class attribute.');
        }
    }
}
