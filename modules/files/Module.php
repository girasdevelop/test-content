<?php

namespace app\modules\files;

use Yii;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\base\{Module as BaseModule, InvalidConfigException};
use Imagine\Image\ImageInterface;
use app\modules\files\interfaces\ThumbConfigInterface;
use app\modules\files\components\{LocalUploadComponent, ThumbConfig};

/**
 * Files module class.
 *
 * @property null|string|array $loginUrl Login url.
 * @property array $accessRoles Array of roles to module access.
 * @property string $fileAttributeName Name of the file field to load using Ajax request.
 * @property array $previewOptions Preview options for som types of mediafiles according with their location.
 * @property array $thumbsConfig Thumbs config with their types and sizes.
 * @property string $thumbFilenameTemplate Thumbnails name template.
 * Values can be the next: {original}, {width}, {height}, {alias}, {extension}
 * @property array $thumbStubUrls Default thumbnail stub urls according with file type.
 * @property bool $enableCsrfValidation Csrf validation.
 * @property View $_view View component to render content.
 *
 * @package Itstructure\FilesModule
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class Module extends BaseModule
{
    const DEFAULT_THUMB_ALIAS = 'default';
    const ORIGINAL_THUMB_ALIAS = 'original';
    const SMALL_THUMB_ALIAS   = 'small';
    const MEDIUM_THUMB_ALIAS  = 'medium';
    const LARGE_THUMB_ALIAS   = 'large';

    const FILE_MANAGER_SRC   = '/files/managers/filemanager';
    const UPLOAD_MANAGER_SRC = '/files/managers/uploadmanager';
    const FILE_INFO_SRC      = '/files/fileinfo/index';
    const LOCAL_SAVE_SRC     = '/files/upload/local-upload/save';
    const DELETE_SRC         = '/files/upload/local-upload/delete';

    const BACK_URL_PARAM = '__backUrl';

    const ORIGINAL_PREVIEW_WIDTH = 300;
    const ORIGINAL_PREVIEW_HEIGHT = 240;
    const SCANTY_PREVIEW_SIZE = 50;

    const STORAGE_TYPE_LOCAL = 'local';

    /**
     * Login url.
     * @var null|string|array
     */
    public $loginUrl = null;

    /**
     * Array of roles to module access.
     * @var array
     */
    public $accessRoles = ['@'];

    /**
     * Name of the file field to load using Ajax request.
     * @var string
     */
    public $fileAttributeName = 'file';

    /**
     * Preview options for som types of mediafiles according with their location.
     * See how it's done in "preview-options" config file as an example.
     * @var array
     */
    public $previewOptions = [];

    /**
     * Thumbs config with their types and sizes.
     * See how it's done in "thumbs-config" config file as an example.
     * @var array of thumbnails.
     */
    public $thumbsConfig = [];

    /**
     * Thumbnails name template.
     * Values can be the next: {original}, {width}, {height}, {alias}, {extension}
     * @var string
     */
    public $thumbFilenameTemplate = '{original}-{width}-{height}-{alias}.{extension}';

    /**
     * Default thumbnail stub urls according with file type.
     * See how it's done in "thumb-stub-urls" config file as an example.
     * @var array
     */
    public $thumbStubUrls = [];

    /**
     * Csrf validation.
     * @var bool
     */
    public $enableCsrfValidation = true;

    /**
     * View component to render content.
     * @var View
     */
    private $_view = null;

    /**
     * Module translations.
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

        $this->previewOptions = ArrayHelper::merge(
            require __DIR__ . '/config/preview-options.php',
            $this->previewOptions
        );

        $this->thumbStubUrls = ArrayHelper::merge(
            require __DIR__ . '/config/thumb-stub-urls.php',
            $this->thumbStubUrls
        );

        $this->thumbsConfig = ArrayHelper::merge(
            require __DIR__ . '/config/thumbs-config.php',
            $this->thumbsConfig
        );

        /**
         * Set Rbac validate component
         */
        $this->setComponents(
            ArrayHelper::merge($this->getLocalUploadComponentConfig(), $this->components)
        );
    }

    /**
     * Get the view.
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
     * @return string
     */
    public static function getBaseDir(): string
    {
        return __DIR__;
    }

    /**
     * Set thumb configuration.
     * @param string $alias
     * @param array  $config
     * @throws InvalidConfigException
     * @return ThumbConfigInterface
     *
     */
    public static function configureThumb(string $alias, array $config): ThumbConfigInterface
    {
        if (!isset($config['name']) ||
            !isset($config['size']) ||
            !is_array($config['size']) ||
            !isset($config['size'][0]) ||
            !isset($config['size'][1])) {

            throw new InvalidConfigException('Error in thumb configuration.');
        }

        $thumbConfig = [
            'class'  => ThumbConfig::class,
            'alias'  => $alias,
            'name'   => $config['name'],
            'width'  => $config['size'][0],
            'height' => $config['size'][1],
            'mode'   => (isset($config['mode']) ? $config['mode'] : ImageInterface::THUMBNAIL_OUTBOUND),
        ];

        /* @var ThumbConfigInterface $object */
        $object = Yii::createObject($thumbConfig);

        return $object;
    }

    /**
     * Default thumb config
     * @return array
     */
    public static function getDefaultThumbConfig(): array
    {
        return [
            'name' => 'Default size',
            'size' => [150, 150],
        ];
    }

    /**
     * Get preview options for som types of mediafiles according with their location.
     * @param string $fileType
     * @param string $location
     * @return array
     */
    public function getPreviewOptions(string $fileType, string $location): array
    {
        if (null === $fileType || null === $location){
            return [];
        }

        if (!isset($this->previewOptions[$fileType]) || !is_array($this->previewOptions[$fileType])){
            return [];
        }

        if (!isset($this->previewOptions[$fileType][$location]) || !is_array($this->previewOptions[$fileType][$location])){
            return [];
        }

        return $this->previewOptions[$fileType][$location];
    }

    /**
     * Module translator.
     * @param       $category
     * @param       $message
     * @param array $params
     * @param null  $language
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
                    'modules/files/album' => 'album.php',
                    'modules/files/filemanager' => 'filemanager.php',
                    'modules/files/uploadmanager' => 'uploadmanager.php',
                    'modules/files/actions' => 'actions.php',
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
     * @return array
     */
    private function getLocalUploadComponentConfig(): array
    {
        return [
            'local-upload-component' => [
                'class' => LocalUploadComponent::class,
                'thumbsConfig' => $this->thumbsConfig,
                'thumbFilenameTemplate' => $this->thumbFilenameTemplate,
            ]
        ];
    }
}
