<?php

namespace app\modules\files\models;

use Yii;
use yii\imagine\Image;
use yii\base\InvalidConfigException;
use yii\helpers\{BaseFileHelper, Inflector};

/**
 * Class LocalUpload
 *
 * @property array $uploadDirs
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

        $this->uploadRoot = trim($this->uploadRoot, $this->directorySeparator);
    }

    /**
     * Set some params for upload.
     * It is needed to set the next parameters:
     * $this->uploadDir
     * $this->uploadPath
     * $this->outFileName
     * $this->databaseDir
     * $this->mediafileModel->type
     *
     * @return void
     */
    protected function setParamsForUpload(): void
    {
        if (strpos($this->file->type, self::TYPE_IMAGE) !== false) {
            $uploadDir = $this->uploadDirs[self::TYPE_IMAGE];

        } elseif (strpos($this->file->type, self::TYPE_AUDIO) !== false) {
            $uploadDir = $this->uploadDirs[self::TYPE_AUDIO];

        } elseif (strpos($this->file->type, self::TYPE_VIDEO) !== false) {
            $uploadDir = $this->uploadDirs[self::TYPE_VIDEO];

        } elseif (strpos($this->file->type, self::TYPE_APP) !== false) {
            $uploadDir = $this->uploadDirs[self::TYPE_APP];

        } elseif (strpos($this->file->type, self::TYPE_TEXT) !== false) {
            $uploadDir = $this->uploadDirs[self::TYPE_TEXT];

        } else {
            $uploadDir = $this->uploadDirs[self::TYPE_OTHER];
        }

        if (!empty($this->subDir)){
            $uploadDir = trim($uploadDir, $this->directorySeparator) .
                         $this->directorySeparator .
                         $this->subDir;
        }

        $this->uploadDir = trim($uploadDir, $this->directorySeparator) .
                           $this->directorySeparator . substr(md5(time()), 0, 2) .
                           $this->directorySeparator . substr(md5(time()+1), 0, 4);

        $this->uploadPath = $this->uploadRoot . $this->directorySeparator . $this->uploadDir;

        $this->outFileName = $this->renameFiles ?
            md5(time()+2).'.'.$this->file->extension :
            Inflector::slug($this->file->baseName).'.'. $this->file->extension;

        $this->databaseDir = $this->uploadDir . $this->directorySeparator . $this->outFileName;

        $this->mediafileModel->type = $this->file->type;
    }

    /**
     * Save file in local directory or send file to remote storage.
     *
     * @return bool
     */
    protected function sendFile(): bool
    {
        BaseFileHelper::createDirectory($this->uploadPath, 0777);

        return $this->file->saveAs($this->uploadPath . $this->directorySeparator . $this->outFileName);
    }

    /**
     * Create thumb.
     *
     * @param string $alias
     * @param int    $width
     * @param int    $height
     * @param string $mode
     *
     * @return string
     */
    protected function createThumb(string $alias, int $width, int $height, string $mode): string
    {
        $originalFile = pathinfo($this->mediafileModel->url);

        $thumbUrl = $originalFile['dirname'] .
                    $this->directorySeparator .
                    $this->getThumbFilename($originalFile['filename'], $originalFile['extension'], $alias, $width, $height);

        Image::thumbnail($this->uploadRoot.$this->directorySeparator.$this->mediafileModel->url, $width, $height, $mode)
            ->save($this->uploadRoot.$this->directorySeparator.$thumbUrl);

        return $thumbUrl;
    }
}
