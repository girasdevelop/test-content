<?php

namespace app\modules\files\models\upload;

use yii\imagine\Image;
use yii\base\{InvalidConfigException, InvalidValueException};
use yii\helpers\{ArrayHelper, Inflector};
use Aws\S3\{S3ClientInterface, S3MultiRegionClient};
use app\modules\files\models\S3FileOptions;
use app\modules\files\Module;
use app\modules\files\components\ThumbConfig;
use app\modules\files\interfaces\{ThumbConfigInterface, UploadModelInterface};

/**
 * Class S3Upload
 *
 * @property string $s3Bucket Amazon web services S3 bucket for upload files (not for delete).
 * @property S3MultiRegionClient|S3ClientInterface $s3Client Amazon web services SDK S3 client.
 * @property string $originalContent Binary contente of the original file.
 * @property array $objectsForDelete Objects for delete (files in the S3 directory).
 * @property string $bucketForUpdate Bucket, in which the located files will be deleted or uploaded once again.
 * @property S3FileOptions $s3FileOptions S3 file options (bucket, prefix).
 *
 * @package Itstructure\FilesModule\models
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class S3Upload extends BaseUpload implements UploadModelInterface
{
    const DIR_LENGTH_FIRST = 2;
    const DIR_LENGTH_SECOND = 4;

    const BUCKET_DIR_SEPARATOR = '/';

    /**
     * Amazon web services S3 bucket for upload files (not for delete).
     * @var string
     */
    public $s3Bucket;

    /**
     * Amazon web services SDK S3 client.
     * @var S3ClientInterface|S3MultiRegionClient
     */
    private $s3Client;

    /**
     * Binary contente of the original file.
     * @var string
     */
    private $originalContent;

    /**
     * Objects for delete (files in the S3 directory).
     * @var array
     */
    private $objectsForDelete = [];

    /**
     * Bucket, in which the located files will be deleted or uploaded once again.
     * @var string
     */
    private $bucketForUpdate;

    /**
     * S3 file options (bucket, prefix).
     * @var S3FileOptions
     */
    private $s3FileOptions;

    /**
     * Initialize.
     */
    public function init()
    {
        if (null === $this->s3Client){
            throw new InvalidConfigException('S3 client is not defined correctly.');
        }
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
     * $this->outFileName
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

        $this->outFileName = $this->renameFiles ?
            md5(time()+2).'.'.$this->file->extension :
            Inflector::slug($this->file->baseName).'.'. $this->file->extension;
    }

    /**
     * Set some params for delete.
     * It is needed to set the next parameters:
     * $this->objectsForDelete
     * $this->bucketForUpdate
     * @return void
     */
    protected function setParamsForDelete(): void
    {
        /** @var S3FileOptions $s3fileOptions */
        $s3fileOptions = S3FileOptions::find()->where([
            'mediafileId' => $this->mediafileModel->id
        ])->one();

        $objects = $this->s3Client->listObjects([
            'Bucket' => $s3fileOptions->bucket,
            'Prefix' => $s3fileOptions->prefix
        ]);

        $this->objectsForDelete = null === $objects['Contents'] ? [] : array_map(function ($item) {
            return [
                'Key' => $item
            ];
        }, ArrayHelper::getColumn($objects['Contents'], 'Key'));

        $this->bucketForUpdate = $s3fileOptions->bucket;
    }

    /**
     * Send file to remote storage.
     * @throws InvalidConfigException
     * @return bool
     */
    protected function sendFile(): bool
    {
        if (null === $this->s3Bucket || !is_string($this->s3Bucket)){
            throw new InvalidConfigException('S3 bucket is not defined correctly.');
        }

        $result = $this->s3Client->putObject([
            'ACL' => 'public-read',
            'SourceFile' => $this->file->tempName,
            'Key' => $this->uploadDir . self::BUCKET_DIR_SEPARATOR . $this->outFileName,
            'Bucket' => $this->s3Bucket
        ]);

        if ($result['ObjectURL']){
            $this->databaseUrl = $result['ObjectURL'];
            return true;
        }

        return false;
    }

    /**
     * Delete storage directory with original file and thumbs.
     * @return void
     */
    protected function deleteFiles(): void
    {
        if (count($this->objectsForDelete) > 0) {
            $this->s3Client->deleteObjects([
                'Bucket' => $this->bucketForUpdate,
                'Delete' => [
                    'Objects' => $this->objectsForDelete,
                ]
            ]);
        }
    }

    /**
     * Create thumb.
     * @param ThumbConfigInterface|ThumbConfig $thumbConfig
     * @return mixed
     */
    protected function createThumb(ThumbConfigInterface $thumbConfig)
    {
        $originalFile = pathinfo($this->mediafileModel->url);
        $s3fileOptions = $this->getS3FileOptions();

        $uploadThumbUrl = $s3fileOptions->prefix .
                    self::BUCKET_DIR_SEPARATOR .
                    $this->getThumbFilename($originalFile['filename'],
                        $originalFile['extension'],
                        $thumbConfig->alias,
                        $thumbConfig->width,
                        $thumbConfig->height
                    );

        $thumbContent = Image::thumbnail(Image::getImagine()->load($this->getOriginalContent()),
            $thumbConfig->width,
            $thumbConfig->height,
            $thumbConfig->mode
        )->get($originalFile['extension'], [
            //'animated' => false
        ]);

        $result = $this->s3Client->putObject([
            'ACL' => 'public-read',
            'Body' => $thumbContent,
            'Key' => $uploadThumbUrl,
            'Bucket' => $s3fileOptions->bucket
        ]);

        if ($result['ObjectURL'] && !empty($result['ObjectURL'])){
            return $result['ObjectURL'];
        }

        return null;
    }

    /**
     * Actions after main save.
     * @return mixed
     */
    protected function afterSave()
    {
        $this->addOwner();

        $this->setS3FileOptions($this->s3Bucket, $this->uploadDir);
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

    /**
     * S3 file options (bucket, prefix).
     * @return S3FileOptions
     */
    private function getS3FileOptions(): S3FileOptions
    {
        if (null === $this->s3FileOptions){
            $this->s3FileOptions = S3FileOptions::find()->where([
                'mediafileId' => $this->mediafileModel->id
            ])->one();
        }

        return $this->s3FileOptions;
    }

    /**
     * Set S3 options for uploaded file in amazon S3 storage.
     * @param string $bucket
     * @param string $prefix
     * @return void
     */
    private function setS3FileOptions(string $bucket, string $prefix): void
    {
        if (null !== $this->file){
            S3FileOptions::deleteAll([
                'mediafileId' => $this->mediafileModel->id
            ]);
            $optionsModel = new S3FileOptions();
            $optionsModel->mediafileId = $this->mediafileModel->id;
            $optionsModel->bucket = $bucket;
            $optionsModel->prefix = $prefix;
            $optionsModel->save();
        }
    }
}
