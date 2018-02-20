<?php

namespace app\modules\files\models;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\{BaseFileHelper, Inflector};

/**
 * This is the model class for table "mediafiles".
 *
 * @property string $localUploadRoot
 * @property array $localUploadDirs
 * @property bool $renameFiles
 * @property string $directorySeparator
 * @property string $fieldName
 * @property string $localUploadDir
 *
 * @property int $id
 * @property string $filename
 * @property string $type
 * @property string $url
 * @property string $alt
 * @property string $size
 * @property string $description
 * @property string $thumbs
 * @property string $advance
 * @property int $created_at
 * @property int $updated_at
 *
 * @property AlbumsMediafiles[] $albumsMediafiles
 * @property Album[] $albums
 * @property Owner[] $owners
 */
class Mediafile extends ActiveRecord
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
     * Directory for local uploaded files.
     *
     * @var string
     */
    private $localUploadDir;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mediafiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'filename',
                    'type',
                    'url',
                    'size',
                    'created_at',
                ],
                'required',
            ],
            [
                [
                    'alt',
                    'description',
                    'thumbs',
                    'advance',
                ],
                'string',
            ],
            [
                [
                    'created_at',
                    'updated_at',
                ],
                'integer',
            ],
            [
                [
                    'filename',
                    'type',
                    'url',
                    'size',
                ],
                'string',
                'max' => 255,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filename' => 'Filename',
            'type' => 'Type',
            'url' => 'Url',
            'alt' => 'Alt',
            'size' => 'Size',
            'description' => 'Description',
            'thumbs' => 'Thumbs',
            'advance' => 'Advance',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlbumsMediafiles()
    {
        return $this->hasMany(AlbumsMediafiles::class, ['mediafileId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlbums()
    {
        return $this->hasMany(Album::class, ['id' => 'albumId'])->viaTable('albums_mediafiles', ['mediafileId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwners()
    {
        return $this->hasMany(Owner::class, ['mediafileId' => 'id']);
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

            $this->filename = $outFileName;
            $this->size = $file->size;
            $this->url = $this->localUploadDir . $this->directorySeparator . $outFileName;

            return $this->save();
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
            $this->type = self::TYPE_IMAGE;
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_IMAGE], $this->directorySeparator);

        } elseif (strpos($type, self::TYPE_AUDIO) !== false) {
            $this->type = self::TYPE_AUDIO;
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_AUDIO], $this->directorySeparator);

        } elseif (strpos($type, self::TYPE_VIDEO) !== false) {
            $this->type = self::TYPE_VIDEO;
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_VIDEO], $this->directorySeparator);

        } else {
            $this->type = self::TYPE_OTHER;
            $this->localUploadDir = trim($this->localUploadDirs[self::TYPE_OTHER], $this->directorySeparator);
        }
    }
}
