<?php

namespace app\modules\files\models\upload;

use yii\imagine\Image;
use yii\base\{InvalidConfigException, InvalidValueException};
use yii\helpers\Inflector;
use Aws\S3\{S3ClientInterface, S3Client};
use app\modules\files\helpers\S3Files;
use app\modules\files\Module;
use app\modules\files\components\ThumbConfig;
use app\modules\files\interfaces\{ThumbConfigInterface, UploadModelInterface};

/**
 * Class S3Upload
 *
 * @property string $s3Domain Amazon web services S3 domain.
 * @property string $s3Bucket Amazon web services S3 bucket.
 * @property S3Client|S3ClientInterface $s3Client Amazon web services SDK S3 client.
 * @property string $originalContent Binary contente of the original file.
 *
 * @package Itstructure\FilesModule\models
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class S3Upload extends BaseUpload implements UploadModelInterface
{
    const DIR_LENGTH_FIRST = 2;
    const DIR_LENGTH_SECOND = 4;

    const BUCKET_ROOT = 's3://';
    const BUCKET_DIR_SEPARATOR = '/';

    /**
     * Amazon web services S3 domain.
     * @var string
     */
    public $s3Domain;

    /**
     * Amazon web services S3 bucket.
     * @var string
     */
    public $s3Bucket;

    /**
     * Amazon web services SDK S3 client.
     * @var S3ClientInterface|S3Client
     */
    private $s3Client;

    /**
     * Binary contente of the original file.
     * @var string
     */
    private $originalContent;

    /**
     * Initialize.
     */
    public function init()
    {
        if (null === $this->s3Client){
            throw new InvalidConfigException('S3 client is not defined correctly.');
        }

        if (null === $this->s3Bucket || !is_string($this->s3Bucket)){
            throw new InvalidConfigException('S3 bucket is not defined correctly.');
        }

        if (null === $this->s3Domain || !is_string($this->s3Domain)){
            throw new InvalidConfigException('S3 domain is not defined correctly.');
        }

        $this->s3Domain = rtrim($this->s3Domain, '/');

        $this->s3Client->registerStreamWrapper();
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
     * @return S3ClientInterface|null
     */
    public function getS3Client()
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
     * @throws InvalidConfigException
     * @return void
     */
    protected function setParamsForSave(): void
    {
        $uploadDir = $this->getUploadDirConfig($this->file->type);
        $uploadDir = trim(str_replace('\\', self::BUCKET_DIR_SEPARATOR, $uploadDir), self::BUCKET_DIR_SEPARATOR);

        if (!empty($this->subDir)){
            $uploadDir = $uploadDir .
                self::BUCKET_DIR_SEPARATOR .
                trim(str_replace('\\', self::BUCKET_DIR_SEPARATOR, $this->subDir), self::BUCKET_DIR_SEPARATOR);
        }

        $this->uploadDir = $uploadDir .
            self::BUCKET_DIR_SEPARATOR . substr(md5(time()), 0, self::DIR_LENGTH_FIRST) .
            self::BUCKET_DIR_SEPARATOR . substr(md5(time()+1), 0, self::DIR_LENGTH_SECOND);

        $this->uploadPath = self::BUCKET_ROOT . $this->s3Bucket . self::BUCKET_DIR_SEPARATOR . $this->uploadDir;

        $this->outFileName = $this->renameFiles ?
            md5(time()+2).'.'.$this->file->extension :
            Inflector::slug($this->file->baseName).'.'. $this->file->extension;

        $this->databaseUrl = $this->s3Domain .
            self::BUCKET_DIR_SEPARATOR . $this->s3Bucket .
            self::BUCKET_DIR_SEPARATOR . $this->uploadDir .
            self::BUCKET_DIR_SEPARATOR . $this->outFileName;
    }

    /**
     * Set some params for upload.
     * It is needed to set the next parameters:
     * $this->directoryForDelete
     * @return void
     */
    protected function setParamsForDelete(): void
    {
        $operatePath = self::BUCKET_ROOT . ltrim(str_replace($this->s3Domain, '', $this->mediafileModel->url), self::BUCKET_DIR_SEPARATOR);
        $originalFile = pathinfo($operatePath);
        $dirname = $originalFile['dirname'];
        $dirnameParent = substr($dirname, 0, -(self::DIR_LENGTH_SECOND+1));

        if (count(S3Files::findDirectories($dirnameParent)) == 1){
            $this->directoryForDelete = $dirnameParent;
        } else {
            $this->directoryForDelete = $dirname;
        }
    }

    /**
     * Send file to remote storage.
     * @return bool
     */
    protected function sendFile(): bool
    {
        if (!is_dir($this->uploadPath)){
            mkdir($this->uploadPath, 0777, true);
        }

        $result = file_put_contents($this->uploadPath .
            self::BUCKET_DIR_SEPARATOR .
            $this->outFileName, file_get_contents($this->file->tempName));

        return $result ? true : false;
    }

    /**
     * Delete storage directory with original file and thumbs.
     * @return mixed
     */
    protected function deleteFiles()
    {
        S3Files::removeDirectory($this->directoryForDelete);

        return true;
    }

    /**
     * Create thumb.
     * @param ThumbConfigInterface|ThumbConfig $thumbConfig
     * @return string|null
     */
    protected function createThumb(ThumbConfigInterface $thumbConfig)
    {
        $originalFile = pathinfo($this->mediafileModel->url);

        $thumbUrl = $originalFile['dirname'] .
                    self::BUCKET_DIR_SEPARATOR .
                    $this->getThumbFilename($originalFile['filename'],
                        $originalFile['extension'],
                        $thumbConfig->alias,
                        $thumbConfig->width,
                        $thumbConfig->height
                    );

        $thumbContent = Image::thumbnail($this->getOriginalContent(),
            $thumbConfig->width,
            $thumbConfig->height,
            $thumbConfig->mode
        )->get($originalFile['extension'], [
            //'animated' => false
        ]);

        $operatePath = self::BUCKET_ROOT . ltrim(str_replace($this->s3Domain, '', $thumbUrl), self::BUCKET_DIR_SEPARATOR);
        $result = file_put_contents($operatePath, $thumbContent);

        return $result ? $thumbUrl : null;
    }

    /**
     * Get binary contente of the original file.
     * @throws InvalidValueException
     * @return string
     */
    private function getOriginalContent()
    {
        if (null === $this->originalContent){
            $this->originalContent = file_get_contents($this->mediafileModel->url);
        }

        if (!$this->originalContent){
            throw new InvalidValueException('Content from '.$this->mediafileModel->url.' can not be read.');
        }

        return $this->originalContent;
    }
}
