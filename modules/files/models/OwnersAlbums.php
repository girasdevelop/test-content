<?php

namespace app\modules\files\models;

use yii\db\ActiveQuery;
use yii\base\InvalidArgumentException;
use app\modules\files\models\album\Album;

/**
 * This is the model class for table "owners_albums".
 *
 * @property int $albumId
 * @property int $ownerId
 * @property string $owner
 * @property string $ownerAttribute
 *
 * @property Album $album
 *
 * @package Itstructure\FilesModule\models
 */
class OwnersAlbums extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'owners_albums';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'albumId',
                    'ownerId',
                    'owner',
                    'ownerAttribute',
                ],
                'required',
            ],
            [
                [
                    'albumId',
                    'ownerId',
                ],
                'integer',
            ],
            [
                [
                    'owner',
                    'ownerAttribute',
                ],
                'string',
                'max' => 255,
            ],
            [
                [
                    'albumId',
                    'ownerId',
                    'owner',
                    'ownerAttribute',
                ],
                'unique',
                'targetAttribute' => [
                    'albumId',
                    'ownerId',
                    'owner',
                    'ownerAttribute',
                ],
            ],
            [
                ['albumId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Album::class,
                'targetAttribute' => ['albumId' => 'id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'albumId' => 'Album ID',
            'ownerId' => 'Owner ID',
            'owner' => 'Owner',
            'ownerAttribute' => 'Owner Attribute',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlbum()
    {
        return $this->hasOne(Album::class, ['id' => 'albumId']);
    }

    /**
     * Get all albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     * @param string $ownerAttribute
     *
     * @return ActiveRecord[]
     */
    public static function getAlbums(string $owner, int $ownerId, string $ownerAttribute = null)
    {
        return Album::find()
            ->where(
                [
                    'id' =>  static::getAlbumIds([
                        'owner' => $owner,
                        'ownerId' => $ownerId,
                        'ownerAttribute' => $ownerAttribute,
                    ])->asArray()
                ]
            )->all();
    }

    /**
     * Get image albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return ActiveRecord[]
     */
    public static function getImageAlbums(string $owner, int $ownerId)
    {
        return static::getAlbums($owner, $ownerId, Album::ALBUM_TYPE_IMAGE);
    }

    /**
     * Get audio albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return ActiveRecord[]
     */
    public static function getAudioAlbums(string $owner, int $ownerId)
    {
        return static::getAlbums($owner, $ownerId, Album::ALBUM_TYPE_AUDIO);
    }

    /**
     * Get video albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return ActiveRecord[]
     */
    public static function getVideoAlbums(string $owner, int $ownerId)
    {
        return static::getAlbums($owner, $ownerId, Album::ALBUM_TYPE_VIDEO);
    }

    /**
     * Get application albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return ActiveRecord[]
     */
    public static function getAppAlbums(string $owner, int $ownerId)
    {
        return static::getAlbums($owner, $ownerId, Album::ALBUM_TYPE_APP);
    }

    /**
     * Get text albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return ActiveRecord[]
     */
    public static function getTextAlbums(string $owner, int $ownerId)
    {
        return static::getAlbums($owner, $ownerId, Album::ALBUM_TYPE_TEXT);
    }

    /**
     * Get other albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return ActiveRecord[]
     */
    public static function getOtherAlbums(string $owner, int $ownerId)
    {
        return static::getAlbums($owner, $ownerId, Album::ALBUM_TYPE_OTHER);
    }

    /**
     * Get Id's by owner.
     *
     * @param array $args. It can be an array of the next params: owner{string}, ownerId{int}, ownerAttribute{string}.
     *
     * @throws InvalidArgumentException
     *
     * @return ActiveQuery
     */
    private static function getAlbumIds(array $args):ActiveQuery
    {
        $conditions = [];

        if (isset($args['owner'])){
            if (!is_string($args['owner']) || empty($args['owner'])){
                throw new InvalidArgumentException('Parameter owner must be a string.');
            }
            $conditions['owner'] = $args['owner'];

            if (isset($args['ownerId'])){
                if (!is_numeric($args['ownerId'])){
                    throw new InvalidArgumentException('Parameter ownerId must be numeric.');
                }
                $conditions['ownerId'] = $args['ownerId'];
            }
        }

        if (isset($args['ownerAttribute'])){
            if (!is_string($args['owner']) || empty($args['ownerAttribute'])){
                throw new InvalidArgumentException('Parameter ownerAttribute must be a string.');
            }
            $conditions['ownerAttribute'] = $args['ownerAttribute'];
        }

        $query = static::find()
            ->select('albumId');

        if (count($conditions) > 0){
            return $query->where($conditions);
        }

        return $query;
    }
}
