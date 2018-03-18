<?php

namespace app\modules\files\traits;

use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * Trait BehaviorsTrait
 *
 * @property array|null $authenticator
 * @property array|null $rateLimiter
 * @property array|null $contentNegotiator
 *
 * @package Itstructure\FilesModule\traits
 */
trait BehaviorsTrait
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