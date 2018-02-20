<?php

namespace app\modules\files\components;

use Yii;
use yii\base\{Component, InvalidConfigException};
use app\modules\files\models\Mediafile;

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
        Mediafile::TYPE_IMAGE => 'uploads/images',
        Mediafile::TYPE_AUDIO => 'uploads/audio',
        Mediafile::TYPE_VIDEO => 'uploads/video',
        Mediafile::TYPE_OTHER => 'uploads/other',
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
     * Sets a user model for ProfileValidateComponent validation model.
     *
     * @return object
     */
    public function setModel()
    {
        $object = Yii::createObject([
            'class' => Mediafile::class,
            'localUploadRoot' => $this->localUploadRoot,
            'localUploadDirs' => $this->localUploadDirs,
            'renameFiles' => $this->renameFiles,
            'directorySeparator' => $this->directorySeparator,
            'fieldName' => $this->fieldName,
        ]);

        return $object;
    }
}
