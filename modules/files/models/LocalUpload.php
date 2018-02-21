<?php

namespace app\modules\files\models;

use Yii;
use yii\web\UploadedFile;
use yii\base\{Model, InvalidConfigException};
use yii\helpers\{BaseFileHelper, Inflector};
use app\modules\files\interfaces\UploadModelInterface;

/**
 * Class LocalUpload
 *
 * @property string $localUploadRoot
 * @property array $localUploadDirs
 * @property bool $renameFiles
 * @property string $directorySeparator
 * @property array $fileExtensions
 * @property int $fileMaxSize
 * @property UploadedFile $file
 * @property string $localUploadDir
 * @property Mediafile $mediafileModel
 */
class LocalUpload extends Model implements UploadModelInterface
{
    const TYPE_IMAGE = 'image';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';
    const TYPE_APP = 'application';
    const TYPE_TEXT = 'text';
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
     * File extensions.
     *
     * @var array
     */
    public $fileExtensions = [
        'png', 'jpg', 'jpeg', 'pjpg', 'pjpeg', 'gif',
        'mpe', 'mpeg', 'mpg', 'mp3', 'wma', 'avi',
        'flv', 'mp4',
        'doc', 'docx', 'rtf', 'pdf', 'txt', 'rar', 'zip'
    ];

    /**
     * Maximum file size.
     *
     * @var int
     */
    public $fileMaxSize = 1024*1024*5;

    /**
     * File object.
     *
     * @var UploadedFile
     */
    private $file;

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
     * Initialize.
     */
    public function init()
    {
        if (null === $this->localUploadRoot){
            throw new InvalidConfigException('The localUploadRoot is not defined.');
        }

        if (!is_array($this->localUploadDirs) || empty($this->localUploadDirs)){
            throw new InvalidConfigException('The localUploadDirs is not defined.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['file'],
                'required',
            ],
            [
                ['file'],
                'file',
                'skipOnEmpty' => false,
                'extensions' => $this->fileExtensions,
                'maxSize' => $this->fileMaxSize
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
     * Set file.
     *
     * @param UploadedFile|null $file
     *
     * @return void
     */
    public function setFile(UploadedFile $file = null): void
    {
        $this->file = $file;
    }

    /**
     * Set file.
     *
     * @return UploadedFile
     */
    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    /**
     * Save file in directory and database by using a "mediafileModel".
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validate()){
            return false;
        }

        $this->setParamsByType($this->file->type);

        $localUploadPath = trim($this->localUploadRoot, $this->directorySeparator) . $this->directorySeparator . $this->localUploadDir;

        $outFileName = $this->renameFiles ? md5(time()+2) . '.' . $this->file->extension : Inflector::slug($this->file->baseName).'.'. $this->file->extension;

        BaseFileHelper::createDirectory($localUploadPath, 0777);

        if (!$this->file->saveAs($localUploadPath . $this->directorySeparator . $outFileName)){
            throw new \Exception('Error save file in to directory.', 500);
        }

        $this->mediafileModel->filename = $outFileName;
        $this->mediafileModel->size = $this->file->size;
        $this->mediafileModel->url = $this->localUploadDir . $this->directorySeparator . $outFileName;

        if (!$this->mediafileModel->save()){
            throw new \Exception('Error save file data in database.', 500);
        }

        return true;
    }

    /**
     * Returns current model id.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->mediafileModel->id;
    }

    /**
     * Set params for local uploaded file by its type.
     *
     * @param string $type
     */
    private function setParamsByType(string $type): void
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

        } elseif (strpos($type, self::TYPE_APP) !== false) {
            $this->mediafileModel->type = self::TYPE_APP;
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_APP], $this->directorySeparator);

        } elseif (strpos($type, self::TYPE_TEXT) !== false) {
            $this->mediafileModel->type = self::TYPE_TEXT;
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_TEXT], $this->directorySeparator);

        } else {
            $this->mediafileModel->type = self::TYPE_OTHER;
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_OTHER], $this->directorySeparator);
        }
    }
}
