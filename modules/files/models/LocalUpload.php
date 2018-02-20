<?php

namespace app\modules\files\models;

use Yii;
use yii\web\UploadedFile;
use yii\base\{Model, InvalidConfigException};
use yii\helpers\{BaseFileHelper, Inflector};

/**
 * Class LocalUpload
 *
 * @property string $localUploadRoot
 * @property array $localUploadDirs
 * @property bool $renameFiles
 * @property string $directorySeparator
 * @property string $fieldName
 * @property string $localUploadDir
 */
class LocalUpload extends Model
{
    const TYPE_IMAGE = 'image';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';
    const TYPE_OTHER = 'other';

    /**
     * Root directory for local uploaded files.
     *
     * @var string
     */
    public $localUploadRoot;

    /**
     * Directories for local uploaded files.
     *
     * @var array
     */
    public $localUploadDirs;

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
     * Directory for local uploaded files.
     *
     * @var string
     */
    private $localUploadDir;

    /**
     * Mediafile model to save files data.
     *
     * @var Mediafile
     */
    private $mediafileModel;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    $this->fieldName,
                ],
                'required',
            ],
            [
                [
                    $this->fieldName,
                ],
                'file',
            ],
        ];
    }

    /**
     * Set mediafile model.
     *
     * @param Mediafile $model
     */
    public function setMediafileModel(Mediafile $model): void
    {
        $this->mediafileModel = $model;
    }

    /**
     * Get mediafile model.
     *
     * @return Mediafile
     */
    public function getMediafileModel(): Mediafile
    {
        return $this->mediafileModel;
    }

    /**
     * Save uploaded file to local directory.
     *
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function saveLocalUploadedFile(UploadedFile $file): bool
    {
        $this->setLocalFileParamsByType($file->type);

        $localUploadPath = trim($this->localUploadRoot, $this->directorySeparator) . $this->directorySeparator . $this->localUploadDir;

        $outFileName = $this->renameFiles ? md5(time()+2) . '.' . $file->extension : Inflector::slug($file->baseName).'.'. $file->extension;

        BaseFileHelper::createDirectory($localUploadPath, 0777);

        if ($file->saveAs($localUploadPath . $this->directorySeparator . $outFileName)){

            $this->mediafileModel->filename = $outFileName;
            $this->mediafileModel->size = $file->size;
            $this->mediafileModel->url = $this->localUploadDir . $this->directorySeparator . $outFileName;

            return $this->mediafileModel->save();
        }

        return false;
    }

    /**
     * Set params for local uploaded file by its type.
     *
     * @param string $type
     */
    private function setLocalFileParamsByType(string $type): void
    {
        if (strpos($type, self::TYPE_IMAGE) !== false) {
            $this->mediafileModel->type = self::TYPE_IMAGE;
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_IMAGE], $this->directorySeparator);

        } elseif (strpos($type, self::TYPE_AUDIO) !== false) {
            $this->mediafileModel->type = self::TYPE_AUDIO;
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_AUDIO], $this->directorySeparator);

        } elseif (strpos($type, self::TYPE_VIDEO) !== false) {
            $this->mediafileModel->type = self::TYPE_VIDEO;
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_VIDEO], $this->directorySeparator);

        } else {
            $this->mediafileModel->type = self::TYPE_OTHER;
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_OTHER], $this->directorySeparator);
        }
    }
}
