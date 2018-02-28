<?php

namespace app\modules\files\controllers\api;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * Class RestController
 * Common rest controller.
 *
 * @property array|null $authenticator
 * @property array|null $rateLimiter
 * @property array|null $contentNegotiator
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
