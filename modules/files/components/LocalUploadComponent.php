<?php

namespace app\modules\files\components;

use Yii;
use yii\base\{Component, InvalidConfigException};
use app\modules\files\models\Mediafile;
use app\modules\files\models\upload\LocalUpload;
use app\modules\files\interfaces\{UploadModelInterface, UploadComponentInterface};

/**
 * Class LocalUploadComponent
 * Component class to upload files in local space.
 *
 * @property string $uploadRoot Root directory for local uploaded files.
 * @property array $uploadDirs Directory for uploaded files.
 * @property bool $renameFiles Rename file after upload.
 * @property array $fileExtensions File extensions.
 * @property bool $checkExtensionByMimeType Check extension by MIME type (they are must match).
 * @property int $fileMaxSize Maximum file size.
 * @property array $thumbsConfig Thumbs config with their types and sizes.
 * @property string $thumbFilenameTemplate Thumbnails name template.
 * Values can be the next: {original}, {width}, {height}, {alias}, {extension}
 *
 * @package Itstructure\FilesModule\components
 */
class LocalUploadComponent extends Component implements UploadComponentInterface
{
    /**
     * Root directory for local uploaded files.
     *
     * @var string
     */
    public $uploadRoot;

    /**
     * Directory for uploaded files.
     *
     * @var string
     */
    public $uploadDirs = [
        UploadModelInterface::FILE_TYPE_IMAGE => 'uploads'.DIRECTORY_SEPARATOR.'images',
        UploadModelInterface::FILE_TYPE_AUDIO => 'uploads'.DIRECTORY_SEPARATOR.'audio',
        UploadModelInterface::FILE_TYPE_VIDEO => 'uploads'.DIRECTORY_SEPARATOR.'video',
        UploadModelInterface::FILE_TYPE_APP => 'uploads'.DIRECTORY_SEPARATOR.'application',
        UploadModelInterface::FILE_TYPE_TEXT => 'uploads'.DIRECTORY_SEPARATOR.'text',
        UploadModelInterface::FILE_TYPE_OTHER => 'uploads'.DIRECTORY_SEPARATOR.'other',
    ];

    /**
     * Rename file after upload.
     *
     * @var bool
     */
    public $renameFiles = true;

    /**
     * File extensions.
     *
     * @var array
     */
    public $fileExtensions = [
        UploadModelInterface::FILE_TYPE_THUMB => [
            'png', 'jpg', 'jpeg', 'gif',
        ],
        UploadModelInterface::FILE_TYPE_IMAGE => [
            'png', 'jpg', 'jpeg', 'gif',
        ],
        UploadModelInterface::FILE_TYPE_AUDIO => [
            'mp3',
        ],
        UploadModelInterface::FILE_TYPE_VIDEO => [
            'mp4', 'ogg', 'ogv', 'oga', 'ogx', 'webm',
        ],
        UploadModelInterface::FILE_TYPE_APP => [
            'doc', 'docx', 'rtf', 'pdf', 'rar', 'zip', 'jar', 'mcd', 'xls',
        ],
        UploadModelInterface::FILE_TYPE_TEXT => [
            'txt',
        ],
        UploadModelInterface::FILE_TYPE_OTHER => null,
    ];

    /**
     * Check extension by MIME type (they are must match).
     *
     * @var bool
     */
    public $checkExtensionByMimeType = true;

    /**
     * Maximum file size.
     *
     * @var int
     */
    public $fileMaxSize = 1024*1024*64;

    /**
     * Thumbs config with their types and sizes.
     *
     * @var array
     */
    public $thumbsConfig = [];

    /**
     * Thumbnails name template.
     * Values can be the next: {original}, {width}, {height}, {alias}, {extension}
     *
     * @var string
     */
    public $thumbFilenameTemplate = '{original}-{width}-{height}-{alias}.{extension}';

    /**
     * Initialize.
     */
    public function init()
    {
        if (null === $this->uploadRoot){
            $this->uploadRoot = Yii::getAlias('@webroot');
        }

        if (null === $this->uploadRoot){
            throw new InvalidConfigException('The uploadRoot is not defined.');
        }
    }

    /**
     * Sets a mediafile model for upload file.
     *
     * @param Mediafile $mediafileModel
     *
     * @return UploadModelInterface
     */
    public function setModelForSave(Mediafile $mediafileModel): UploadModelInterface
    {
        /* @var UploadModelInterface $object */
        $object = Yii::createObject([
            'class' => LocalUpload::class,
            'mediafileModel' => $mediafileModel,
            'uploadRoot' => $this->uploadRoot,
            'uploadDirs' => $this->uploadDirs,
            'renameFiles' => $this->renameFiles,
            'fileExtensions' => $this->fileExtensions,
            'checkExtensionByMimeType' => $this->checkExtensionByMimeType,
            'fileMaxSize' => $this->fileMaxSize,
            'thumbsConfig' => $this->thumbsConfig,
            'thumbFilenameTemplate' => $this->thumbFilenameTemplate,
        ]);

        return $object;
    }

    /**
     * Sets a mediafile model for delete file.
     *
     * @param Mediafile $mediafileModel
     *
     * @return UploadModelInterface
     */
    public function setModelForDelete(Mediafile $mediafileModel): UploadModelInterface
    {
        /* @var UploadModelInterface $object */
        $object = Yii::createObject([
            'class' => LocalUpload::class,
            'mediafileModel' => $mediafileModel,
            'uploadRoot' => $this->uploadRoot,
        ]);

        return $object;
    }
}
