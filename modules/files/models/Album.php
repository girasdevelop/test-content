<?php

namespace app\modules\files\models;

use Yii;
use app\modules\files\Module;
use app\modules\files\models\upload\BaseUpload;

/**
 * This is the model class for table "albums".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $type
 * @property int $created_at
 * @property int $updated_at
 *
 * @property OwnersAlbums[] $ownersAlbums
 *
 * @package Itstructure\FilesModule\models
 */
class Album extends ActiveRecord
{
    const ALBUM_TYPE_IMAGE = BaseUpload::FILE_TYPE_IMAGE . 'Album';
    const ALBUM_TYPE_AUDIO = BaseUpload::FILE_TYPE_AUDIO . 'Album';
    const ALBUM_TYPE_VIDEO = BaseUpload::FILE_TYPE_VIDEO . 'Album';
    const ALBUM_TYPE_APP   = BaseUpload::FILE_TYPE_APP . 'Album';
    const ALBUM_TYPE_TEXT  = BaseUpload::FILE_TYPE_TEXT . 'Album';
    const ALBUM_TYPE_OTHER = BaseUpload::FILE_TYPE_OTHER . 'Album';

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
                ['description'],
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
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get album types or selected type.
     *
     * @param string|null $key
     *
     * @return mixed
     */
    public static function getTypes(string $key = null)
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
            return $types[$key];
        }

        return $types;
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
     * @return \yii\db\ActiveQuery
     */
    public function getOwners()
    {
        return $this->hasMany(OwnersAlbums::class, ['albumId' => 'id']);
    }

    public function getMediaFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id);
    }

    public function getThumbnail()
    {
        return Mediafile::find()
            ->where(
                [
                    'id' =>  OwnersMediafiles::find()
                        ->select('mediafileId')
                        ->where(
                            [
                                'owner' => $this->type,
                                'ownerId' => $this->id,
                                'ownerAttribute' => BaseUpload::FILE_TYPE_THUMB,
                            ]
                        )
                ]
            )->one();
    }

    /**
     * @return ActiveRecord[]
     */
    public function getImageFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, BaseUpload::FILE_TYPE_IMAGE);
    }

    /**
     * @return ActiveRecord[]
     */
    public function getAudioFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, BaseUpload::FILE_TYPE_AUDIO);
    }

    /**
     * @return ActiveRecord[]
     */
    public function getVideoFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, BaseUpload::FILE_TYPE_VIDEO);
    }

    /**
     * @return ActiveRecord[]
     */
    public function getAppFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, BaseUpload::FILE_TYPE_APP);
    }

    /**
     * @return ActiveRecord[]
     */
    public function getTextFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, BaseUpload::FILE_TYPE_TEXT);
    }

    /**
     * @return ActiveRecord[]
     */
    public function getOtherFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, BaseUpload::FILE_TYPE_OTHER);
    }
}
