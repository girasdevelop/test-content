<?php

namespace app\modules\files\components;

use Yii;
use yii\base\{Component, InvalidConfigException};
use app\modules\files\models\{Mediafile, LocalUpload};
use app\modules\files\interfaces\{UploadModelInterface, UploadComponentInterface};

/**
 * Class LocalUploadComponent
 * Component class to upload files in local space.
 *
 * @property string $localUploadRoot
 * @property array $localUploadDirs
 * @property bool $renameFiles
 * @property string $directorySeparator
 * @property array $fileExtensions
 * @property int $fileMaxSize
 * @property string $fileAttributeName
 * @property array $thumbs
 * @property string $thumbFilenameTemplate
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
        LocalUpload::TYPE_IMAGE => 'uploads\\images',
        LocalUpload::TYPE_AUDIO => 'uploads\\audio',
        LocalUpload::TYPE_VIDEO => 'uploads\\video',
        LocalUpload::TYPE_APP => 'uploads\\application',
        LocalUpload::TYPE_TEXT => 'uploads\\text',
        LocalUpload::TYPE_OTHER => 'uploads\\other',
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
    public $thumbFilenameTemplate = '{original}-{alias}.{extension}';

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
            $this->localUploadRoot = Yii::getAlias('@webroot');
        }

        if (null === $this->uploadRoot){
            throw new InvalidConfigException('The localUploadRoot is not defined.');
        }
    }

    /**
     * Sets a mediafile model.
     *
     * @param Mediafile $mediafileModel
     *
     * @return UploadModelInterface
     */
    public function setModel(Mediafile $mediafileModel): UploadModelInterface
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
        ]);

        return $object;
    }
}
