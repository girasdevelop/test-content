<?php

namespace app\modules\files\controllers;

use Yii;
use yii\web\Response;
use yii\rest\Controller;
use yii\base\InvalidConfigException;

class RestController extends Controller
{
    /**
     * Auth filter.
     *
     * @var \yii\filters\auth\AuthMethod|null
     */
    protected $authenticator = null;

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        if (null !== $this->authenticator){
            $this->checkConfigureFormat($this->authenticator);
            $behaviors['authenticator'] = $this->authenticator;
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
