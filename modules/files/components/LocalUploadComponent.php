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
 * @property string $directorySeparator
 * @property array $fileExtensions
 * @property int $fileMaxSize
 * @property string $fileAttributeName
 * @property array $thumbs
 * @property string $thumbFilenameTemplate
 * @property string $thumbStubUrl
 * @property bool $enableCsrfValidation
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
        LocalUpload::TYPE_IMAGE => 'uploads'.DIRECTORY_SEPARATOR.'images',
        LocalUpload::TYPE_AUDIO => 'uploads'.DIRECTORY_SEPARATOR.'audio',
        LocalUpload::TYPE_VIDEO => 'uploads'.DIRECTORY_SEPARATOR.'video',
        LocalUpload::TYPE_APP => 'uploads'.DIRECTORY_SEPARATOR.'application',
        LocalUpload::TYPE_TEXT => 'uploads'.DIRECTORY_SEPARATOR.'text',
        LocalUpload::TYPE_OTHER => 'uploads'.DIRECTORY_SEPARATOR.'other',
    ];

    /**
     * Rename file after upload.
     *
     * @var bool
     */
    public $renameFiles = true;

    /**
     * Directory separator.
     *
     * @var string
     */
    public $directorySeparator = DIRECTORY_SEPARATOR;

    /**
     * File extensions.
     *
     * @var array
     */
    public $fileExtensions = [
        'png', 'jpg', 'jpeg', 'pjpg', 'pjpeg', 'gif',
        'mpe', 'mpeg', 'mpg', 'mp3', 'wma', 'avi',
        'flv', 'mp4',
        'doc', 'docx', 'rtf', 'pdf', 'txt', 'rar', 'zip'
    ];

    /**
     * Maximum file size.
     *
     * @var int
     */
    public $fileMaxSize = 1024*1024*5;

    /**
     * Name of the file field.
     *
     * @var string
     */
    public $fileAttributeName = 'file';

    /**
     * @var array
     */
    public $thumbs = [];

    /**
     * Thumbnails name template.
     * Values can be the next: {original}, {width}, {height}, {alias}, {extension}
     *
     * @var string
     */
    public $thumbFilenameTemplate = '{original}-{width}-{height}-{alias}.{extension}';

    /**
     * Default thumbnail stub url.
     *
     * @var string
     */
    public $thumbStubUrl;

    /**
     * Csrf validation.
     *
     * @var bool
     */
    public $enableCsrfValidation = false;

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
            'directorySeparator' => $this->directorySeparator,
            'fileExtensions' => $this->fileExtensions,
            'fileMaxSize' => $this->fileMaxSize,
            'thumbs' => $this->thumbs,
            'thumbFilenameTemplate' => $this->thumbFilenameTemplate,
            'thumbStubUrl' => $this->thumbStubUrl
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
            'directorySeparator' => $this->directorySeparator,
        ]);

        return $object;
    }
}
