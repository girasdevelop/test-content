<?php

namespace app\modules\files\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use Aws\S3\{S3Client, S3ClientInterface};
use app\modules\files\models\Mediafile;
use app\modules\files\models\upload\S3Upload;
use app\modules\files\interfaces\{S3UploadModelInterface, S3UploadComponentInterface};

/**
 * Class S3UploadComponent
 * Component class to upload files in Amazon S3 bucket.
 *
 * @property array $uploadDirs Directory for uploaded files.
 * @property string $AWSAccessKeyId Amazon web services access key.
 * @property string $AWSSecretKey Amazon web services secret key.
 * @property string $s3Domain Amazon web services S3 domain.
 * @property string $s3Bucket Amazon web services S3 bucket.
 * @property S3Client|S3ClientInterface $s3Client Amazon web services SDK S3 client.
 *
 * @package Itstructure\FilesModule\components
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class S3UploadComponent extends BaseUploadComponent implements S3UploadComponentInterface
{
    /**
     * Directory for uploaded files.
     * @var string
     */
    public $uploadDirs = [
        S3UploadModelInterface::FILE_TYPE_IMAGE => 'images',
        S3UploadModelInterface::FILE_TYPE_AUDIO => 'audio',
        S3UploadModelInterface::FILE_TYPE_VIDEO => 'video',
        S3UploadModelInterface::FILE_TYPE_APP => 'application',
        S3UploadModelInterface::FILE_TYPE_TEXT => 'text',
        S3UploadModelInterface::FILE_TYPE_OTHER => 'other',
    ];

    /**
     * Amazon web services access key.
     * @var string
     */
    public $AWSAccessKeyId = 'asfdsgfdsgfd';

    /**
     * Amazon web services secret key.
     * @var string
     */
    public $AWSSecretKey = 'safddsafdsgf';

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
     * @var S3Client|S3ClientInterface
     */
    private $s3Client;

    /**
     * Initialize.
     */
    public function init()
    {
        if (null === $this->s3Bucket || !is_string($this->s3Bucket)){
            throw new InvalidConfigException('S3 bucket is not defined correctly.');
        }

        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => [
                'key'    => $this->AWSAccessKeyId,
                'secret' => $this->AWSSecretKey,
            ],
        ]);
    }

    /**
     * Sets a mediafile model for upload file.
     * @param Mediafile $mediafileModel
     * @return S3UploadModelInterface
     */
    public function setModelForSave(Mediafile $mediafileModel): S3UploadModelInterface
    {
        /* @var S3UploadModelInterface $object */
        $object = Yii::createObject(ArrayHelper::merge([
                'class' => S3Upload::class,
                'mediafileModel' => $mediafileModel,
                'uploadDirs' => $this->uploadDirs,
                's3Domain' => $this->s3Domain,
                's3Client' => $this->s3Client,
                's3Bucket' => $this->s3Bucket,
            ], $this->getBaseConfigForSave())
        );

        return $object;
    }

    /**
     * Sets a mediafile model for delete file.
     * @param Mediafile $mediafileModel
     * @return S3UploadModelInterface
     */
    public function setModelForDelete(Mediafile $mediafileModel): S3UploadModelInterface
    {
        /* @var S3UploadModelInterface $object */
        $object = Yii::createObject([
            'class' => S3Upload::class,
            'mediafileModel' => $mediafileModel,
            's3Domain' => $this->s3Domain,
            's3Client' => $this->s3Client,
            's3Bucket' => $this->s3Bucket,
        ]);

        return $object;
    }
}
