<?php

namespace app\modules\files\models\upload;

use yii\base\{Model, InvalidConfigException};
use yii\web\UploadedFile;
use yii\imagine\Image;
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\components\ThumbConfig;
use app\modules\files\interfaces\{UploadModelInterface, ThumbConfigInterface};

/**
 * Class BaseUpload
 *
 * @property string $alt
 * @property string $description
 * @property string $advance
 * @property string $subDir
 * @property bool $renameFiles
 * @property string $directorySeparator
 * @property array $fileExtensions
 * @property int $fileMaxSize
 * @property string $fileAttributeName
 * @property array $thumbsConfig
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
    /**
     * Scripts Constants.
     * Required for certain validation rules to work for specific scenarios.
     */
    const SCENARIO_UPLOAD = 'upload';
    const SCENARIO_UPDATE = 'update';

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
     * Name of the file field.
     *
     * @var string
     */
    public $fileAttributeName = 'file';

    /**
     * @var array
     */
    public $thumbsConfig = [];

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
    abstract protected function setParamsForSave(): void;

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
     * Scenarios.
     *
     * @return array
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_UPLOAD => $this->attributes(),
            self::SCENARIO_UPDATE => $this->attributes(),
        ];
    }

    /**
     * Attributes.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            $this->fileAttributeName,
            'subDir',
            'alt',
            'description',
            'advance'
        ];
    }

        /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                $this->fileAttributeName,
                'required',
                'on' => [
                    self::SCENARIO_UPLOAD,
                ],
            ],
            [
                $this->fileAttributeName,
                'file',
                'on' => [
                    self::SCENARIO_UPLOAD,
                ],
                'skipOnEmpty' => false,
                'extensions' => $this->fileExtensions,
                'maxSize' => $this->fileMaxSize
            ],
            [
                'subDir',
                'string',
                'on' => [
                    self::SCENARIO_UPLOAD,
                    self::SCENARIO_UPDATE,
                ],
                'max' => 24,
            ],
            [
                'subDir',
                'filter',
                'on' => [
                    self::SCENARIO_UPLOAD,
                    self::SCENARIO_UPDATE,
                ],
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
                'on' => [
                    self::SCENARIO_UPLOAD,
                    self::SCENARIO_UPDATE,
                ],
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
     * @return mixed
     */
    public function getFile()
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
        if ($this->mediafileModel->isNewRecord){
            $this->setScenario(self::SCENARIO_UPLOAD);
        } else {
            $this->setScenario(self::SCENARIO_UPDATE);
        }

        if (!$this->validate()){
            return false;
        }

        if (null !== $this->file){

            $this->setParamsForSave();

            if (!$this->sendFile()){
                throw new \Exception('Error save file in to directory.', 500);
            }

            if ($this->getScenario() == self::SCENARIO_UPDATE){
                $this->setParamsForDelete();
                $this->deleteFiles();
            }

            $this->mediafileModel->url = $this->databaseDir;
            $this->mediafileModel->filename = $this->outFileName;
            $this->mediafileModel->size = $this->file->size;
        }

        $this->mediafileModel->alt = $this->alt;
        $this->mediafileModel->description = $this->description;
        $this->mediafileModel->advance = $this->advance;

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

        foreach ($this->thumbsConfig as $alias => $preset) {
            $thumbs[$alias] = $this->createThumb(Module::configureThumb($alias, $preset));
        }

        // Create default thumb.
        if (!array_key_exists(Module::DEFAULT_THUMB_ALIAS, $this->thumbsConfig)){
            $thumbs[Module::DEFAULT_THUMB_ALIAS] = $this->createThumb(
                Module::configureThumb(
                    Module::DEFAULT_THUMB_ALIAS,
                    Module::getDefaultThumbConfig()
                )
            );
        }

        $this->mediafileModel->thumbs = serialize($thumbs);

        return $this->mediafileModel->save();
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
    public function getThumbFilename($original, $extension, $alias, $width, $height)
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
