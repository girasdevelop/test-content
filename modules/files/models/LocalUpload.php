<?php

namespace app\modules\files\models;

use Yii;
use yii\imagine\Image;
use yii\base\InvalidConfigException;
use yii\helpers\{BaseFileHelper, Inflector};
use app\modules\files\components\ThumbConfig;
use app\modules\files\interfaces\ThumbConfigInterface;

/**
 * Class LocalUpload
 *
 * @property array $uploadDirs
 *
 * @package Itstructure\FilesModule\models
 */
class LocalUpload extends BaseUpload
{
    const DIR_LENGTH_FIRST = 2;
    const DIR_LENGTH_SECOND = 4;

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
     * @throws InvalidConfigException
     *
     * @return void
     */
    protected function setParamsForUpload(): void
    {
        if (!is_array($this->uploadDirs) || empty($this->uploadDirs)){
            throw new InvalidConfigException('The localUploadDirs is not defined.');
        }

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
                           $this->directorySeparator . substr(md5(time()), 0, self::DIR_LENGTH_FIRST) .
                           $this->directorySeparator . substr(md5(time()+1), 0, self::DIR_LENGTH_SECOND);

        $this->uploadPath = $this->uploadRoot . $this->directorySeparator . $this->uploadDir;

        $this->outFileName = $this->renameFiles ?
            md5(time()+2).'.'.$this->file->extension :
            Inflector::slug($this->file->baseName).'.'. $this->file->extension;

        $this->databaseDir = $this->uploadDir . $this->directorySeparator . $this->outFileName;

        $this->mediafileModel->type = $this->file->type;
    }

    /**
     * Set some params for upload.
     * It is needed to set the next parameters:
     * $this->directoryForDelete
     *
     * @return void
     */
    protected function setParamsForDelete(): void
    {
        $originalFile = pathinfo($this->mediafileModel->url);

        $dirname = $originalFile['dirname'];

        $dirnameParent = substr($dirname, 0, -(self::DIR_LENGTH_SECOND+1));

        if (count(BaseFileHelper::findDirectories($dirnameParent)) == 1){
            $this->directoryForDelete = $this->uploadRoot . $this->directorySeparator . $dirnameParent;
        } else {
            $this->directoryForDelete = $this->uploadRoot . $this->directorySeparator . $dirname;
        }
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
     * Delete local directory with original file and thumbs.
     *
     * @return mixed
     */
    protected function deleteFiles()
    {
        BaseFileHelper::removeDirectory($this->directoryForDelete);

        return true;
    }

    /**
     * Create thumb.
     *
     * @param ThumbConfigInterface|ThumbConfig $thumbConfig
     *
     * @return string
     */
    protected function createThumb(ThumbConfigInterface $thumbConfig): string
    {
        $originalFile = pathinfo($this->mediafileModel->url);

        $thumbUrl = $originalFile['dirname'] .
                    $this->directorySeparator .
                    $this->getThumbFilename($originalFile['filename'],
                        $originalFile['extension'],
                        $thumbConfig->alias,
                        $thumbConfig->width,
                        $thumbConfig->height
                    );

        Image::thumbnail($this->uploadRoot . $this->directorySeparator . $this->mediafileModel->url,
            $thumbConfig->width,
            $thumbConfig->height,
            $thumbConfig->mode
        )->save($this->uploadRoot.$this->directorySeparator.$thumbUrl);

        return $thumbUrl;
    }
}
