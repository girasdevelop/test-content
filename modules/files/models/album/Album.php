<?php
namespace app\modules\files\models\album;

use yii\helpers\ArrayHelper;
use app\modules\files\Module;
use app\modules\files\models\{ActiveRecord, OwnersAlbums, OwnersMediafiles, Mediafile};
use app\modules\files\behaviors\BehaviorMediafile;
use app\modules\files\interfaces\UploadModelInterface;

/**
 * This is the model class for table "albums".
 *
 * @property int|string $thumbnail
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $type
 * @property int $created_at
 * @property int $updated_at
 *
 * @property OwnersAlbums[] $ownersAlbums
 *
 * @package Itstructure\FilesModule\models\album
 */
class Album extends ActiveRecord
{
    const ALBUM_TYPE_IMAGE = UploadModelInterface::FILE_TYPE_IMAGE . 'Album';
    const ALBUM_TYPE_AUDIO = UploadModelInterface::FILE_TYPE_AUDIO . 'Album';
    const ALBUM_TYPE_VIDEO = UploadModelInterface::FILE_TYPE_VIDEO . 'Album';
    const ALBUM_TYPE_APP   = UploadModelInterface::FILE_TYPE_APP . 'Album';
    const ALBUM_TYPE_TEXT  = UploadModelInterface::FILE_TYPE_TEXT . 'Album';
    const ALBUM_TYPE_OTHER = UploadModelInterface::FILE_TYPE_OTHER . 'Album';

    /**
     * @var int|string thumbnail(mediafile id or url).
     */
    public $thumbnail;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'albums';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'title',
                    'type',
                ],
                'required',
            ],
            [
                [
                    'description'
                ],
                'string',
            ],
            [
                [
                    'title',
                    'type',
                ],
                'string',
                'max' => 255,
            ],
            [
                'thumbnail',
                function($attribute){
                    if (!is_numeric($this->{$attribute}) && !is_string($this->{$attribute})){
                        $this->addError($attribute, 'Tumbnail content must be a numeric or string.');
                    }
                },
                'skipOnError' => false,
            ],
            [
                [
                    'created_at',
                    'updated_at',
                ],
                'safe',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('main', 'ID'),
            'title' => Module::t('album', 'Type'),
            'description' => Module::t('album', 'Description'),
            'type' => Module::t('album', 'Title'),
            'created_at' => Module::t('main', 'Created date'),
            'updated_at' => Module::t('main', 'Updated date'),
        ];
    }

    /**
     * Get album types or selected type.
     *
     * @param string|null $key
     *
     * @return mixed
     */
    public static function getAlbumTypes(string $key = null)
    {
        $types = [
            self::ALBUM_TYPE_IMAGE => Module::t('album', 'Image album'),
            self::ALBUM_TYPE_AUDIO => Module::t('album', 'Audio album'),
            self::ALBUM_TYPE_VIDEO => Module::t('album', 'Video album'),
            self::ALBUM_TYPE_APP   => Module::t('album', 'Applications'),
            self::ALBUM_TYPE_TEXT  => Module::t('album', 'Documents'),
            self::ALBUM_TYPE_OTHER => Module::t('album', 'Other files'),
        ];

        if (null !== $key){
            return array_key_exists($key, $types) ? $types[$key] : [];
        }

        return $types;
    }

    /**
     * Get file type by album type.
     *
     * @param string $albumType
     *
     * @return mixed|null
     */
    public static function getFileType(string $albumType)
    {
        $albumTypes = [
            self::ALBUM_TYPE_IMAGE => UploadModelInterface::FILE_TYPE_IMAGE,
            self::ALBUM_TYPE_AUDIO => UploadModelInterface::FILE_TYPE_AUDIO,
            self::ALBUM_TYPE_VIDEO => UploadModelInterface::FILE_TYPE_VIDEO,
            self::ALBUM_TYPE_APP   => UploadModelInterface::FILE_TYPE_APP,
            self::ALBUM_TYPE_TEXT  => UploadModelInterface::FILE_TYPE_TEXT,
            self::ALBUM_TYPE_OTHER => UploadModelInterface::FILE_TYPE_OTHER,
        ];

        return array_key_exists($albumType, $albumTypes) ? $albumTypes[$albumType] : null;
    }

    /**
     * Search models by file types.
     *
     * @param array $types
     *
     * @return ActiveRecord|array
     */
    public static function findByTypes(array $types): ActiveRecord
    {
        return static::find()->filterWhere(['in', 'type', $types])->all();
    }

    /**
     * Add owner to mediafiles table.
     *
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
     *
     * @return bool
     */
    public function addOwner(int $ownerId, string $owner, string $ownerAttribute): bool
    {
        $owners = new OwnersAlbums();
        $owners->albumId = $this->id;
        $owners->owner = $owner;
        $owners->ownerId = $ownerId;
        $owners->ownerAttribute = $ownerAttribute;

        return $owners->save();
    }

    /**
     * Remove this mediafile owner.
     *
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
     *
     * @return bool
     */
    public static function removeOwner(int $ownerId, string $owner, string $ownerAttribute): bool
    {
        $deleted = OwnersAlbums::deleteAll([
            'ownerId' => $ownerId,
            'owner' => $owner,
            'ownerAttribute' => $ownerAttribute,
        ]);

        return $deleted > 0;
    }

    /**
     * Get album's owners.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwners()
    {
        return $this->hasMany(OwnersAlbums::class, ['albumId' => 'id']);
    }

    /**
     * Get album's mediafiles.
     *
     * @param string|null $ownerAttribute
     *
     * @return \app\modules\files\models\ActiveRecord[]
     */
    public function getMediaFiles(string $ownerAttribute = null)
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, $ownerAttribute);
    }

    /**
     * Get album's thumbnail.
     *
     * @return array|null|\yii\db\ActiveRecord|Mediafile
     */
    public function getThumbnailModel()
    {
        return OwnersMediafiles::getOwnerThumbnail($this->type, $this->id);
    }
}
