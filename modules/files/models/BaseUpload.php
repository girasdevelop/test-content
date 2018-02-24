<?php

namespace app\modules\files\models;

use Yii;
use yii\imagine\Image;
use yii\web\UploadedFile;
use yii\base\{Model, InvalidConfigException};
use yii\helpers\{BaseFileHelper, Inflector};
use Imagine\Image\ImageInterface;
use app\modules\files\interfaces\UploadModelInterface;

/**
 * Class LocalUpload
 *
 * @property bool $renameFiles
 * @property string $directorySeparator
 * @property array $fileExtensions
 * @property int $fileMaxSize
 * @property array $thumbs
 * @property string $thumbFilenameTemplate
 * @property string $uploadRoot
 * @property string $uploadPath
 * @property string $outFileName
 * @property string $databaseDir
 * @property UploadedFile $file
 * @property Mediafile $mediafileModel
 *
 * @package Itstructure\FilesModule\models
 */
abstract class BaseUpload extends Model implements UploadModelInterface
{
    const TYPE_IMAGE = 'image';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';
    const TYPE_APP = 'application';
    const TYPE_TEXT = 'text';
    const TYPE_OTHER = 'other';

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
     * @var array
     */
    public $thumbs = [];

    /**
     * Thumbnails name template.
     * Values can be the next: {original}, {width}, {height}, {alias}, {extension}
     *
     * @var string
     */
    public $thumbFilenameTemplate = '{original}-{alias}.{extension}';

    /**
     * Root directory for uploaded files.
     *
     * @var string
     */
    public $uploadRoot;

    /**
     * Path to upload file.
     *
     * @var string
     */
    protected $uploadPath;

    /**
     * Prepared file name to save in database and storage.
     *
     * @var string
     */
    protected $outFileName;

    /**
     * File directory path for database.
     *
     * @var string
     */
    protected $databaseDir;

    /**
     * File object.
     *
     * @var UploadedFile
     */
    private $file;

    /**
     * Mediafile model to save files data.
     *
     * @var Mediafile
     */
    private $mediafileModel;

    /**
     * Set some params before upload according with type of file.
     *
     * @param string $type
     *
     * @return void
     */
    abstract protected function setParamsByType(string $type): void;

    /**
     * Set path to upload file.
     *
     * @return void
     */
    abstract protected function setUploadPath(): void;

    /**
     * Set file directory path for database.
     *
     * @return void
     */
    abstract protected function setDatabaseDir(): void;

    /**
     * Save file in local directory or send file to remote storage.
     *
     * @param string $uploadPath
     * @param string $outFileName
     *
     * @return bool
     */
    abstract protected function sendFile(string $uploadPath, string $outFileName): bool;

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

        $this->setUploadPath();

        $this->outFileName = $this->renameFiles ? md5(time()+2).'.'.$this->file->extension : Inflector::slug($this->file->baseName).'.'. $this->file->extension;

        if (!$this->sendFile($this->uploadPath, $this->outFileName)){
            throw new \Exception('Error save file in to directory.', 500);
        }

        $this->setDatabaseDir();

        $this->mediafileModel->url = $this->databaseDir;
        $this->mediafileModel->filename = $this->outFileName;
        $this->mediafileModel->size = $this->file->size;

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
     * Check if the file is image.
     *
     * @return bool
     */
    public function isImage(): bool
    {
        return strpos($this->mediafileModel->type, self::TYPE_IMAGE) !== false;
    }

    /**
     * Create thumbs for this image
     */
    public function createThumbs()
    {
        $thumbs = [];
        $localUploadRoot = trim($this->localUploadRoot, $this->directorySeparator);
        $originalFile = pathinfo($this->mediafileModel->url);

        Image::$driver = [Image::DRIVER_GD2, Image::DRIVER_GMAGICK, Image::DRIVER_IMAGICK];

        foreach ($this->thumbs as $alias => $preset) {
            $width = $preset['size'][0];
            $height = $preset['size'][1];
            $mode = (isset($preset['mode']) ? $preset['mode'] : ImageInterface::THUMBNAIL_OUTBOUND);

            $thumbUrl = $originalFile['dirname'] .
                        $this->directorySeparator .
                        $this->getThumbFilename($originalFile['filename'], $originalFile['extension'], $alias, $width, $height);

            Image::thumbnail("$localUploadRoot".$this->directorySeparator."{$this->mediafileModel->url}", $width, $height, $mode)
                ->save("$localUploadRoot".$this->directorySeparator."$thumbUrl");

            $thumbs[$alias] = $thumbUrl;
        }

        $this->thumbs = serialize($thumbs);
        $this->detachBehavior('timestamp');

        $this->createDefaultThumb();

        return $this->save();
    }

    /**
     * Create default thumbnail
     */
    public function createDefaultThumb()
    {
        $originalFile = pathinfo($this->mediafileModel->url);

        Image::$driver = [Image::DRIVER_GD2, Image::DRIVER_GMAGICK, Image::DRIVER_IMAGICK];

        $size = Module::getDefaultThumbSize();
        $width = $size[0];
        $height = $size[1];
        $thumbUrl = $originalFile['dirname'] .
                    $this->directorySeparator .
                    $this->getThumbFilename($originalFile['filename'], $originalFile['extension'], Module::DEFAULT_THUMB_ALIAS, $width, $height);

        $localUploadRoot = trim($this->localUploadRoot, $this->directorySeparator);

        Image::thumbnail("$localUploadRoot".$this->directorySeparator."{$this->mediafileModel->url}", $width, $height)
            ->save("$localUploadRoot".$this->directorySeparator."$thumbUrl");
    }

    /**
     * Returns thumbnail name.
     *
     * @param $original
     * @param $extension
     * @param $alias
     * @param $width
     * @param $height
     *
     * @return string
     */
    protected function getThumbFilename($original, $extension, $alias, $width, $height)
    {
        return strtr($this->thumbFilenameTemplate, [
            '{width}'     => $width,
            '{height}'    => $height,
            '{alias}'     => $alias,
            '{original}'  => $original,
            '{extension}' => $extension,
        ]);
    }
}
