<?php

namespace app\modules\files\components;

use Yii;
use yii\base\{Component, InvalidConfigException};
use app\modules\files\models\Mediafile;
use app\modules\files\models\LocalUpload;

/**
 * Class LocalUploadComponent
 * Component class to upload files in local space.
 *
 * @property string $localUploadRoot
 * @property array $localUploadDirs
 * @property bool $renameFiles
 * @property string $directorySeparator
 * @property string $fieldName
 *
 * @package Itstructure\FilesModule\components
 */
class LocalUploadComponent extends Component
{
    /**
     * Root directory for local uploaded files.
     *
     * @var string
     */
    public $localUploadRoot;

    /**
     * Directory for uploaded files.
     *
     * @var string
     */
    public $localUploadDirs = [
        LocalUpload::TYPE_IMAGE => 'uploads/images',
        LocalUpload::TYPE_AUDIO => 'uploads/audio',
        LocalUpload::TYPE_VIDEO => 'uploads/video',
        LocalUpload::TYPE_OTHER => 'uploads/other',
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
     * Name of the file field.
     *
     * @var string
     */
    public $fieldName = 'file';

    /**
     * Initialize.
     */
    public function init()
    {
        if (null === $this->localUploadRoot){
            $this->localUploadRoot = Yii::getAlias('@webroot');
        }

        if (null === $this->localUploadRoot){
            throw new InvalidConfigException('The localUploadRoot is not defined.');
        }
    }

    /**
     * Sets a mediafile model.
     *
     * @param Mediafile $model
     *
     * @return object
     */
    public function setModel(Mediafile $model)
    {
        $object = Yii::createObject([
            'class' => LocalUpload::class,
            'mediafileModel' => $model,
            'localUploadRoot' => $this->localUploadRoot,
            'localUploadDirs' => $this->localUploadDirs,
            'renameFiles' => $this->renameFiles,
            'directorySeparator' => $this->directorySeparator,
            'fieldName' => $this->fieldName,
        ]);

        return $object;
    }
}
