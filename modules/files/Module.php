<?php

namespace app\modules\files;

use Yii;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\base\{Module as BaseModule, InvalidConfigException};
use Imagine\Image\ImageInterface;
use app\modules\files\interfaces\{ThumbConfigInterface, UploadModelInterface};
use app\modules\files\components\{LocalUploadComponent, ThumbConfig};

/**
 * Files module class.
 *
 * @property null|string|array $loginUrl
 * @property array $accessRoles
 * @property array|null $authenticator
 * @property array|null $rateLimiter
 * @property array|null $contentNegotiator
 * @property string $fileAttributeName
 * @property array $thumbsConfig
 * @property string $thumbFilenameTemplate
 * @property array $thumbStubUrls
 * @property bool $enableCsrfValidation
 * @property View $_view
 *
 * @package Itstructure\FilesModule
 */
class Module extends BaseModule
{
    const DEFAULT_THUMB_ALIAS = 'default';
    const SMALL_THUMB_ALIAS   = 'small';
    const MEDIUM_THUMB_ALIAS  = 'medium';
    const LARGE_THUMB_ALIAS   = 'large';

    const FILE_MANAGER_SRC   = '/files/managers/filemanager';
    const UPLOAD_MANAGER_SRC = '/files/managers/uploadmanager';
    const FILE_INFO_SRC      = '/files/fileinfo/index';
    const LOCAL_SAVE_SRC     = '/files/api/local-upload/save';
    const DELETE_SRC         = '/files/api/local-upload/delete';

    const BACK_URL_PARAM = '__backUrl';

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
     * Auth filter.
     *
     * @var array|null
     */
    public $authenticator = null;

    /**
     * Rate limit filter.
     *
     * @var array|null
     */
    public $rateLimiter = null;

    /**
     * Content negotiator filter.
     *
     * @var array|null
     */
    public $contentNegotiator = null;

    /**
     * Name of the file field.
     *
     * @var string
     */
    public $fileAttributeName = 'file';

    /**
     * @var array of thumbnails.
     */
    public $thumbsConfig = [
        self::SMALL_THUMB_ALIAS => [
            'name' => 'Small size',
            'size' => [120, 80],
        ],
        self::MEDIUM_THUMB_ALIAS => [
            'name' => 'Medium size',
            'size' => [400, 300],
        ],
        self::LARGE_THUMB_ALIAS => [
            'name' => 'Large size',
            'size' => [800, 600],
        ],
    ];

    /**
     * Thumbnails name template.
     * Values can be the next: {original}, {width}, {height}, {alias}, {extension}
     *
     * @var string
     */
    public $thumbFilenameTemplate = '{original}-{width}-{height}-{alias}.{extension}';

    /**
     * Default thumbnail stub urls according with file type.
     *
     * @var string
     */
    public $thumbStubUrls = [
        UploadModelInterface::FILE_TYPE_IMAGE => 'images'.DIRECTORY_SEPARATOR.'image.png',
        UploadModelInterface::FILE_TYPE_AUDIO => 'images'.DIRECTORY_SEPARATOR.'audio.png',
        UploadModelInterface::FILE_TYPE_VIDEO => 'images'.DIRECTORY_SEPARATOR.'video.png',
        UploadModelInterface::FILE_TYPE_TEXT => 'images'.DIRECTORY_SEPARATOR.'text.png',
        UploadModelInterface::FILE_TYPE_APP => 'images'.DIRECTORY_SEPARATOR.'app.png',
        UploadModelInterface::FILE_TYPE_OTHER => 'images'.DIRECTORY_SEPARATOR.'other.png',
    ];

    /**
     * Csrf validation.
     *
     * @var bool
     */
    public $enableCsrfValidation = false;

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
                $this->getLocalUploadComponentConfig(),
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
     * Set thumb configuration.
     *
     * @param string $alias
     * @param array  $config
     *
     * @throws InvalidConfigException
     *
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
     * Default thumb config
     *
     * @return array
     */
    public static function getDefaultThumbConfig(): array
    {
        return [
            'name' => 'Default size',
            'size' => [128, 128],
        ];
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
     *
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
