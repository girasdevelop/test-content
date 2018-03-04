<?php

namespace app\modules\files\models;

use Yii;
use yii\db\ActiveQuery;
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
            BaseUpload::TYPE_IMAGE => Module::t('album', 'Image album'),
            BaseUpload::TYPE_AUDIO => Module::t('album', 'Audio album'),
            BaseUpload::TYPE_VIDEO => Module::t('album', 'Video album'),
            BaseUpload::TYPE_APP   => Module::t('album', 'Applications'),
            BaseUpload::TYPE_TEXT  => Module::t('album', 'Documents'),
            BaseUpload::TYPE_OTHER => Module::t('album', 'Other files'),
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
        return self::find()->filterWhere(['in', 'type', $types])->all();
    }

    /**
     * Get all mediafiles by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     * @param string $ownerAttribute
     *
     * @return ActiveRecord|Mediafile[]
     */
    public static function getAllByOwner(string $owner, int $ownerId, string $ownerAttribute): ActiveRecord
    {
        return static::find()
            ->where(
                [
                    'id' =>  static::getIdsByOwner($owner, $ownerId, $ownerAttribute)->asArray()
                ]
            )->all();
    }

    /**
     * Get one mediafile by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     * @param string $ownerAttribute
     *
     * @return array|null|Mediafile
     */
    public static function getOneByOwner(string $owner, int $ownerId, string $ownerAttribute): Mediafile
    {
        return static::find()
            ->where(
                [
                    'id' =>  static::getIdsByOwner($owner, $ownerId, $ownerAttribute)->one()->albumId
                ]
            )->one();
    }

    /**
     * Get Id's by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     * @param string $ownerAttribute
     *
     * @return ActiveQuery|Album[]
     */
    private static function getIdsByOwner(string $owner, int $ownerId, string $ownerAttribute): ActiveQuery
    {
        return OwnersAlbums::find()
            ->select('albumId')
            ->where(
                [
                    'owner' => $owner,
                    'ownerId' => $ownerId,
                    'ownerAttribute' => $ownerAttribute,
                ]
            );
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
    public function getOwnersAlbums()
    {
        return $this->hasMany(OwnersAlbums::class, ['albumId' => 'id']);
    }
}
