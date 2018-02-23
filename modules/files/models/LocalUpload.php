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
 * @property string $localUploadRoot
 * @property array $localUploadDirs
 * @property bool $renameFiles
 * @property string $directorySeparator
 * @property array $fileExtensions
 * @property int $fileMaxSize
 * @property array $thumbs
 * @property string $thumbFilenameTemplate
 * @property UploadedFile $file
 * @property string $localUploadDir
 * @property Mediafile $mediafileModel
 *
 * @package Itstructure\FilesModule\models
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
     * Set params for local uploaded file by its type.
     *
     * @param string $type
     */
    private function setParamsByType(string $type): void
    {
        if (strpos($type, self::TYPE_IMAGE) !== false) {
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_IMAGE], $this->directorySeparator);

        } elseif (strpos($type, self::TYPE_AUDIO) !== false) {
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_AUDIO], $this->directorySeparator);

        } elseif (strpos($type, self::TYPE_VIDEO) !== false) {
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_VIDEO], $this->directorySeparator);

        } elseif (strpos($type, self::TYPE_APP) !== false) {
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_APP], $this->directorySeparator);

        } elseif (strpos($type, self::TYPE_TEXT) !== false) {
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_TEXT], $this->directorySeparator);

        } else {
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_OTHER], $this->directorySeparator);
        }

        $this->mediafileModel->type = $type;
    }
}
