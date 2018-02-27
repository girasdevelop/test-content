<?php

namespace app\modules\files\models;

use yii\base\{Model, InvalidConfigException};
use yii\web\UploadedFile;
use yii\imagine\Image;
use app\modules\files\Module;
use app\modules\files\interfaces\{UploadModelInterface, ThumbConfigInterface};

/**
 * Class LocalUpload
 *
 * @property string $alt
 * @property string $description
 * @property string $advance
 * @property string $subDir
 * @property bool $renameFiles
 * @property string $directorySeparator
 * @property array $fileExtensions
 * @property int $fileMaxSize
 * @property array $thumbs
 * @property string $thumbFilenameTemplate
 * @property string $thumbStubUrl
 * @property string $uploadRoot
 * @property string $directoryForDelete
 * @property string $uploadDir
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
     * Alt text for the file.
     *
     * @var string
     */
    public $alt;

    /**
     * File description.
     *
     * @var string
     */
    public $description;

    /**
     * Advance value.
     *
     * @var string
     */
    public $advance;

    /**
     * Addition sub-directory for uploaded files.
     *
     * @var string
     */
    public $subDir;

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
    public $thumbFilenameTemplate = '{original}-{width}-{height}-{alias}.{extension}';

    /**
     * Default thumbnail stub url.
     *
     * @var string
     */
    public $thumbStubUrl;

    /**
     * Root directory for uploaded files.
     *
     * @var string
     */
    public $uploadRoot;

    /**
     * Directory for delete with all content.
     *
     * @var string
     */
    public $directoryForDelete = [];

    /**
     * Directory for uploaded files.
     *
     * @var string
     */
    protected $uploadDir;

    /**
     * Full directory path to upload file.
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
     * Set some params for upload.
     * It is needed to set the next parameters:
     * $this->uploadDir
     * $this->uploadPath
     * $this->outFileName
     * $this->databaseDir
     * $this->mediafileModel->type
     *
     * @return void
     */
    abstract protected function setParamsForUpload(): void;

    /**
     * Set some params for delete.
     *
     * @return void
     */
    abstract protected function setParamsForDelete(): void;

    /**
     * Save file in local directory or send file to remote storage.
     *
     * @return bool
     */
    abstract protected function sendFile(): bool;

    /**
     * Delete files from local directory or from remote storage.
     *
     * @return mixed
     */
    abstract protected function deleteFiles();

    /**
     * Create thumb.
     *
     * @param ThumbConfigInterface $thumbConfig
     *
     * @return string
     */
    abstract protected function createThumb(ThumbConfigInterface $thumbConfig): string;

        /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                'file',
                'required',
            ],
            [
                'file',
                'file',
                'skipOnEmpty' => false,
                'extensions' => $this->fileExtensions,
                'maxSize' => $this->fileMaxSize
            ],
            [
                'subDir',
                'string',
                'max' => 24,
            ],
            [
                'subDir',
                'filter',
                'filter' => function($value){
                    return trim($value, $this->directorySeparator);
                }
            ],
            [
                [
                    'alt',
                    'description',
                    'advance'
                ],
                'string',
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

        $this->setParamsForUpload();

        if (!$this->sendFile()){
            throw new \Exception('Error save file in to directory.', 500);
        }

        if (!$this->mediafileModel->isNewRecord){
            $this->setParamsForDelete();
            $this->deleteFiles();
        }

        $this->mediafileModel->url = $this->databaseDir;
        $this->mediafileModel->filename = $this->outFileName;
        $this->mediafileModel->size = $this->file->size;

        if (!empty($this->alt)){
            $this->mediafileModel->alt = $this->alt;
        }

        if (!empty($this->description)){
            $this->mediafileModel->description = $this->description;
        }

        if (!empty($this->advance)){
            $this->mediafileModel->advance = $this->advance;
        }

        if (!$this->mediafileModel->save()){
            throw new \Exception('Error save file data in database.', 500);
        }

        return true;
    }

    /**
     * Delete files from local directory or from remote storage.
     *
     * @throws \Exception
     *
     * @return int
     */
    public function delete(): int
    {
        $this->setParamsForDelete();

        $this->deleteFiles();

        $deleted = $this->mediafileModel->delete();

        if (!$deleted){
            throw new \Exception('Error delete file data from database.', 500);
        }

        return $deleted;
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
     *
     * @throws InvalidConfigException
     *
     * @return bool
     */
    public function createThumbs(): bool
    {
        $thumbs = [];

        Image::$driver = [Image::DRIVER_GD2, Image::DRIVER_GMAGICK, Image::DRIVER_IMAGICK];

        foreach ($this->thumbs as $alias => $preset) {
            $thumbs[$alias] = $this->createThumb(Module::configureThumb($alias, $preset));
        }

        $this->mediafileModel->thumbs = serialize($thumbs);

        return $this->mediafileModel->save();
    }

    /**
     * Get default thumbnail url.
     *
     * @throws InvalidConfigException
     *
     * @return string
     */
    public function getDefaultThumbUrl(): string
    {
        if ($this->isImage()) {

            $thumbConfig = Module::configureThumb(Module::DEFAULT_THUMB_ALIAS, $this->thumbs[Module::DEFAULT_THUMB_ALIAS]);

            $originalFile = pathinfo($this->mediafileModel->url);
            $dirname = $originalFile['dirname'];
            $filename = $originalFile['filename'];
            $extension = $originalFile['extension'];

            return $dirname .
                   $this->directorySeparator .
                   $this->getThumbFilename($filename, $extension, Module::DEFAULT_THUMB_ALIAS, $thumbConfig->width, $thumbConfig->height);
        }

        return $this->thumbStubUrl;
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
