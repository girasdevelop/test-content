<?php

namespace app\modules\files\models;

use Yii;
use yii\imagine\Image;
use yii\web\UploadedFile;
use yii\base\InvalidConfigException;
use yii\helpers\{BaseFileHelper, Inflector};
use Imagine\Image\ImageInterface;

/**
 * Class LocalUpload
 *
 * @property string $localUploadRoot
 * @property array $localUploadDirs
 * @property bool $renameFiles
 * @property string $directorySeparator
 * @property array $fileExtensions
 * @property int $fileMaxSize
 * @property array $thumbs
 * @property string $thumbFilenameTemplate
 * @property UploadedFile $file
 * @property string $localUploadDir
 *
 * @package Itstructure\FilesModule\models
 */
class LocalUpload extends BaseUpload
{
    /**
     * Directories for local uploaded files.
     *
     * @var array
     */
    public $uploadDirs;

    /**
     * Directory for local uploaded files.
     *
     * @var string
     */
    private $uploadDir;

    /**
     * Initialize.
     */
    public function init()
    {
        if (null === $this->uploadRoot){
            throw new InvalidConfigException('The uploadRoot is not defined.');
        }

        if (!is_array($this->uploadDirs) || empty($this->uploadDirs)){
            throw new InvalidConfigException('The localUploadDirs is not defined.');
        }
    }

    /**
     * Set params for local uploaded file by its type.
     *
     * @param string $type
     */
    protected function setParamsByType(string $type): void
    {
        if (strpos($type, self::TYPE_IMAGE) !== false) {
            $uploadDir = $this->uploadDirs[self::TYPE_IMAGE];

        } elseif (strpos($type, self::TYPE_AUDIO) !== false) {
            $uploadDir = $this->uploadDirs[self::TYPE_AUDIO];

        } elseif (strpos($type, self::TYPE_VIDEO) !== false) {
            $uploadDir = $this->uploadDirs[self::TYPE_VIDEO];

        } elseif (strpos($type, self::TYPE_APP) !== false) {
            $uploadDir = $this->uploadDirs[self::TYPE_APP];

        } elseif (strpos($type, self::TYPE_TEXT) !== false) {
            $uploadDir = $this->uploadDirs[self::TYPE_TEXT];

        } else {
            $uploadDir = $this->uploadDirs[self::TYPE_OTHER];
        }

        $this->uploadDir = trim($uploadDir, $this->directorySeparator) . $this->directorySeparator . substr(md5(time()), 0, 2);

        $this->mediafileModel->type = $type;
    }

    /**
     * Set path to upload file.
     *
     * @return void
     */
    protected function setUploadPath(): void
    {
        $this->uploadPath = trim($this->uploadRoot, $this->directorySeparator) . $this->directorySeparator . $this->uploadDir;
    }

    /**
     * Set file directory path for database.
     *
     * @return void
     */
    protected function setDatabaseDir(): void
    {
        $this->databaseDir = $this->uploadDir . $this->directorySeparator . $this->outFileName;
    }

    /**
     * Save file in local directory or send file to remote storage.
     *
     * @param string $uploadPath
     * @param string $outFileName
     *
     * @return bool
     */
    protected function sendFile(string $uploadPath, string $outFileName): bool
    {
        BaseFileHelper::createDirectory($this->uploadPath, 0777);

        return $this->file->saveAs($this->uploadPath . $this->directorySeparator . $this->outFileName);
    }
}
