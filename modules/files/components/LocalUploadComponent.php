<?php

namespace app\modules\files\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use app\modules\files\models\Mediafile;
use app\modules\files\models\upload\LocalUpload;
use app\modules\files\interfaces\{UploadModelInterface, UploadComponentInterface};

/**
 * Class LocalUploadComponent
 * Component class to upload files in local space.
 *
 * @property string $uploadRoot Root directory for local uploaded files.
 * @property array $uploadDirs Directory for uploaded files.
 *
 * @package Itstructure\FilesModule\components
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class LocalUploadComponent extends BaseUploadComponent implements UploadComponentInterface
{
    /**
     * Root directory for local uploaded files.
     * @var string
     */
    public $uploadRoot;

    /**
     * Directory for uploaded files.
     * @var string
     */
    public $uploadDirs = [
        UploadModelInterface::FILE_TYPE_IMAGE => 'uploads'.DIRECTORY_SEPARATOR.'images',
        UploadModelInterface::FILE_TYPE_AUDIO => 'uploads'.DIRECTORY_SEPARATOR.'audio',
        UploadModelInterface::FILE_TYPE_VIDEO => 'uploads'.DIRECTORY_SEPARATOR.'video',
        UploadModelInterface::FILE_TYPE_APP => 'uploads'.DIRECTORY_SEPARATOR.'application',
        UploadModelInterface::FILE_TYPE_TEXT => 'uploads'.DIRECTORY_SEPARATOR.'text',
        UploadModelInterface::FILE_TYPE_OTHER => 'uploads'.DIRECTORY_SEPARATOR.'other',
    ];

    /**
     * Initialize.
     */
    public function init()
    {
        if (null === $this->uploadRoot){
            $this->uploadRoot = Yii::getAlias('@webroot');
        }

        if (null === $this->uploadRoot || !is_string($this->uploadRoot)){
            throw new InvalidConfigException('The uploadRoot is not defined correctly.');
        }
    }

    /**
     * Sets a mediafile model for upload file.
     * @param Mediafile $mediafileModel
     * @return UploadModelInterface
     */
    public function setModelForSave(Mediafile $mediafileModel): UploadModelInterface
    {
        /* @var UploadModelInterface $object */
        $object = Yii::createObject(ArrayHelper::merge([
                'class' => LocalUpload::class,
                'mediafileModel' => $mediafileModel,
                'uploadRoot' => $this->uploadRoot,
                'uploadDirs' => $this->uploadDirs,
            ], $this->getBaseConfigForSave())
        );

        return $object;
    }

    /**
     * Sets a mediafile model for delete file.
     * @param Mediafile $mediafileModel
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
