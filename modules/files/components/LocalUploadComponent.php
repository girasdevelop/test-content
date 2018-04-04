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
 * @property string $uploadRoot
 * @property array $uploadDirs
 * @property bool $renameFiles
 * @property array $fileExtensions
 * @property bool $checkExtensionByMimeType
 * @property int $fileMaxSize
 * @property array $thumbsConfig
 * @property string $thumbFilenameTemplate
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
        LocalUpload::FILE_TYPE_IMAGE => 'uploads'.DIRECTORY_SEPARATOR.'images',
        LocalUpload::FILE_TYPE_AUDIO => 'uploads'.DIRECTORY_SEPARATOR.'audio',
        LocalUpload::FILE_TYPE_VIDEO => 'uploads'.DIRECTORY_SEPARATOR.'video',
        LocalUpload::FILE_TYPE_APP => 'uploads'.DIRECTORY_SEPARATOR.'application',
        LocalUpload::FILE_TYPE_TEXT => 'uploads'.DIRECTORY_SEPARATOR.'text',
        LocalUpload::FILE_TYPE_OTHER => 'uploads'.DIRECTORY_SEPARATOR.'other',
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
        'png', 'jpg', 'jpeg', 'gif',
        'mp3', 'mp4', 'ogg', 'ogv', 'oga', 'ogx', 'webm',
        'doc', 'docx', 'rtf', 'pdf', 'txt', 'rar', 'zip', 'mcd'
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
    public $fileMaxSize = 1024*1024*10;

    /**
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
