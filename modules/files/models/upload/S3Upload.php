<?php

namespace app\modules\files\models\upload;

use Yii;
use yii\imagine\Image;
use yii\base\InvalidConfigException;
use yii\helpers\{BaseFileHelper, Inflector};
use Aws\S3\{S3ClientInterface, S3Client};
use app\modules\files\Module;
use app\modules\files\components\ThumbConfig;
use app\modules\files\interfaces\{ThumbConfigInterface, S3UploadModelInterface};

/**
 * Class S3Upload
 *
 * @property array $uploadDirs Directories for local uploaded files.
 *
 * @package Itstructure\FilesModule\models
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class S3Upload extends BaseUpload implements S3UploadModelInterface
{
    const DIR_LENGTH_FIRST = 2;
    const DIR_LENGTH_SECOND = 4;

    const BUCKET_ROOT = 's3://';

    /**
     * Directories for local uploaded files.
     * @var array
     */
    public $uploadDirs;

    /**
     * @var string
     */
    public $s3bucket;

    /**
     * @var S3ClientInterface|S3Client
     */
    private $s3Client;

    /**
     * Initialize.
     */
    public function init()
    {
        if (null === $this->uploadRoot){
            throw new InvalidConfigException('The uploadRoot is not defined.');
        }

        if (null === $this->s3Client){
            throw new InvalidConfigException('S3 client is not defined.');
        }

        if (null === $this->s3bucket){
            throw new InvalidConfigException('S3 bucket is not defined.');
        }

        $this->s3Client->registerStreamWrapper();

        $this->uploadRoot = trim($this->uploadRoot, DIRECTORY_SEPARATOR);
    }

    /**
     * Set s3 client.
     * @param S3ClientInterface $s3Client
     */
    public function setS3Client(S3ClientInterface $s3Client): void
    {
        $this->s3Client = $s3Client;
    }

    /**
     * Get s3 client.
     * @return S3ClientInterface
     */
    public function getS3Client(): S3ClientInterface
    {
        return $this->s3Client;
    }

    /**
     * Get storage type - aws.
     * @return string
     */
    protected function getStorageType(): string
    {
        return Module::STORAGE_TYPE_S3;
    }

    /**
     * Set some params for upload.
     * It is needed to set the next parameters:
     * $this->uploadDir
     * $this->uploadPath
     * $this->outFileName
     * $this->databaseUrl
     * $this->mediafileModel->type
     * @throws InvalidConfigException
     * @return void
     */
    protected function setParamsForSave(): void
    {
        $uploadDir = trim($this->getUploadDirConfig($this->file->type), DIRECTORY_SEPARATOR);

        if (!empty($this->subDir)){
            $uploadDir = $uploadDir .
                         DIRECTORY_SEPARATOR .
                         trim($this->subDir, DIRECTORY_SEPARATOR);
        }

        $this->uploadDir = $uploadDir .
                           DIRECTORY_SEPARATOR . substr(md5(time()), 0, self::DIR_LENGTH_FIRST) .
                           DIRECTORY_SEPARATOR . substr(md5(time()+1), 0, self::DIR_LENGTH_SECOND);

        $this->uploadPath = $this->uploadRoot . DIRECTORY_SEPARATOR . $this->uploadDir;

        $this->outFileName = $this->renameFiles ?
            md5(time()+2).'.'.$this->file->extension :
            Inflector::slug($this->file->baseName).'.'. $this->file->extension;

        $this->databaseUrl = DIRECTORY_SEPARATOR . $this->uploadDir . DIRECTORY_SEPARATOR . $this->outFileName;

        $this->mediafileModel->type = $this->file->type;
    }

    /**
     * Set some params for upload.
     * It is needed to set the next parameters:
     * $this->directoryForDelete
     * @return void
     */
    protected function setParamsForDelete(): void
    {
        $originalFile = pathinfo($this->mediafileModel->url);

        $dirname = ltrim($originalFile['dirname'], DIRECTORY_SEPARATOR);

        $dirnameParent = substr($dirname, 0, -(self::DIR_LENGTH_SECOND+1));

        if (count(BaseFileHelper::findDirectories($dirnameParent)) == 1){
            $this->directoryForDelete = $this->uploadRoot . DIRECTORY_SEPARATOR . $dirnameParent;
        } else {
            $this->directoryForDelete = $this->uploadRoot . DIRECTORY_SEPARATOR . $dirname;
        }
    }

    /**
     * Save file in local directory or send file to remote storage.
     * @return bool
     */
    protected function sendFile(): bool
    {
        BaseFileHelper::createDirectory($this->uploadPath, 0777);

        return $this->file->saveAs($this->uploadPath . DIRECTORY_SEPARATOR . $this->outFileName);
    }

    /**
     * Delete local directory with original file and thumbs.
     * @return mixed
     */
    protected function deleteFiles()
    {
        BaseFileHelper::removeDirectory($this->directoryForDelete);

        return true;
    }

    /**
     * Create thumb.
     * @param ThumbConfigInterface|ThumbConfig $thumbConfig
     * @return string
     */
    protected function createThumb(ThumbConfigInterface $thumbConfig): string
    {
        $originalFile = pathinfo($this->mediafileModel->url);

        $thumbUrl = $originalFile['dirname'] .
                    DIRECTORY_SEPARATOR .
                    $this->getThumbFilename($originalFile['filename'],
                        $originalFile['extension'],
                        $thumbConfig->alias,
                        $thumbConfig->width,
                        $thumbConfig->height
                    );

        Image::thumbnail($this->uploadRoot . DIRECTORY_SEPARATOR . $this->mediafileModel->url,
            $thumbConfig->width,
            $thumbConfig->height,
            $thumbConfig->mode
        )->save($this->uploadRoot.DIRECTORY_SEPARATOR.$thumbUrl);

        return $thumbUrl;
    }

    /**
     * Get upload directory configuration by file type.
     * @param string $type
     * @throws InvalidConfigException
     * @return string
     */
    private function getUploadDirConfig(string $type): string
    {
        if (!is_array($this->uploadDirs) || empty($this->uploadDirs)){
            throw new InvalidConfigException('The localUploadDirs is not defined.');
        }

        if (strpos($type, self::FILE_TYPE_IMAGE) !== false) {
            return $this->uploadDirs[self::FILE_TYPE_IMAGE];

        } elseif (strpos($type, self::FILE_TYPE_AUDIO) !== false) {
            return $this->uploadDirs[self::FILE_TYPE_AUDIO];

        } elseif (strpos($type, self::FILE_TYPE_VIDEO) !== false) {
            return $this->uploadDirs[self::FILE_TYPE_VIDEO];

        } elseif (strpos($type, self::FILE_TYPE_APP) !== false) {
            return $this->uploadDirs[self::FILE_TYPE_APP];

        } elseif (strpos($type, self::FILE_TYPE_TEXT) !== false) {
            return $this->uploadDirs[self::FILE_TYPE_TEXT];

        } else {
            return $this->uploadDirs[self::FILE_TYPE_OTHER];
        }
    }
}
