<?php

namespace app\modules\files\traits;

/**
 * Trait ResponseTrait
 *
 * @package Itstructure\FilesModule\traits
 */
trait ResponseTrait
{
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

        \Yii::$app->response->statusCode = $statusCode;

        return [
            'meta' => [
                'status' => $statusCode == 200 ? 'success' : 'fail',
                'message' => $message,
            ],
            'data' => $data,
        ];
    }
}