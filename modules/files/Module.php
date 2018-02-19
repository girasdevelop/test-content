<?php

namespace app\modules\files;

use Yii;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\base\{Module as BaseModule, InvalidConfigException};
use app\modules\files\components\FileUploadComponent;

/**
 * Files module class.
 *
 * @property null|string|array $loginUrl
 * @property array $accessRoles
 * @property View $_view
 *
 * @package Itstructure\FilesModule
 */
class Module extends BaseModule
{
    /**
     * Login url.
     *
     * @var null|string|array
     */
    public $loginUrl = null;

    /**
     * Array of roles to module access.
     *
     * @var array
     */
    public $accessRoles = ['@'];

    /**
     * View component to render content.
     *
     * @var View
     */
    private $_view = null;

    /**
     * Module translations.
     *
     * @var array|null
     */
    private static $_translations = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::setAlias('@files', static::getBaseDir());

        if (null !== $this->loginUrl) {
            \Yii::$app->getUser()->loginUrl = $this->loginUrl;
        }

        static::registerTranslations();

        /**
         * Set Rbac validate component
         */
        $this->setComponents(
            ArrayHelper::merge(
                $this->getFilesUploadComponentConfig(),
                $this->components
            )
        );
    }

    /**
     * Get the view.
     *
     * @return View
     */
    public function getView()
    {
        if (null === $this->_view) {
            $this->_view = $this->get('view');
        }

        return $this->_view;
    }

    /**
     * Returns module root directory.
     *
     * @return string
     */
    public static function getBaseDir(): string
    {
        return __DIR__;
    }

    /**
     * Module translator.
     *
     * @param       $category
     * @param       $message
     * @param array $params
     * @param null  $language
     *
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        if (null === static::$_translations){
            static::registerTranslations();
        }

        return Yii::t('modules/files/' . $category, $message, $params, $language);
    }

    /**
     * Set i18N component.
     *
     * @return void
     */
    private function registerTranslations(): void
    {
        static::$_translations = [
            'modules/files/*' => [
                'class'          => 'yii\i18n\PhpMessageSource',
                'forceTranslation' => true,
                'sourceLanguage' => Yii::$app->language,
                'basePath'       => '@files/messages',
                'fileMap'        => [
                    'modules/files/main' => 'main.php',
                    'modules/files/files' => 'files.php',
                ],
            ]
        ];

        Yii::$app->i18n->translations = ArrayHelper::merge(
            static::$_translations,
            Yii::$app->i18n->translations
        );
    }

    /**
     * File upload component config.
     *
     * @return array
     */
    private function getFilesUploadComponentConfig(): array
    {
        return [
            'file-upload-component' => [
                'class' => FileUploadComponent::class,
            ]
        ];
    }
}
