<?php

namespace app\modules\files\components;

use Yii;
use yii\base\{Model, Component, InvalidConfigException};
use yii\rbac\ManagerInterface;
use Itstructure\UsersModule\{
    interfaces\ModelInterface,
    models\ProfileValidate
};

/**
 * Class FileUploadComponent
 * Component class for validation user fields.
 *
 * @property array $rules
 * @property array $attributes
 * @property array $attributeLabels
 *
 * @package Itstructure\FilesModule\components
 */
class FileUploadComponent extends Component
{
    /**
     * Validate fields with rules.
     *
     * @var array
     */
    public $rules = [];

    /**
     * Attributes.
     *
     * @var array
     */
    public $attributes = [];

    /**
     * Attribute labels.
     *
     * @var array
     */
    public $attributeLabels = [];

    /**
     * Initialize.
     */
    public function init()
    {

    }

    /**
     * Sets a user model for ProfileValidateComponent validation model.
     *
     * @param Model $model
     *
     * @return ModelInterface
     */
    public function setModel(Model $model): ModelInterface
    {
        /** @var ModelInterface $object */
        $object = Yii::createObject([
            'class' => ProfileValidate::class,
            'profileModel' => $model,
            'rules' => $this->rules,
            'attributes' => $this->attributes,
            'attributeLabels' => $this->attributeLabels,
            'rbacManage' => $this->rbacManage,
            'customRewrite' => $this->customRewrite,
            'authManager' => $this->authManager,
        ]);

        return $object;
    }
}
