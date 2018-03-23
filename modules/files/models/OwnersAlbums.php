<?php

namespace app\modules\files\models;

use yii\db\ActiveQuery;

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
    public static function getAlbums(string $owner, int $ownerId, string $ownerAttribute)
    {
        return Album::find()
            ->where(
                [
                    'id' =>  static::getAlbumIds($owner, $ownerId, $ownerAttribute)->asArray()
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
     * @param string $owner
     * @param int    $ownerId
     * @param string $ownerAttribute
     *
     * @return ActiveQuery
     */
    private static function getAlbumIds(string $owner, int $ownerId, string $ownerAttribute):ActiveQuery
    {
        return static::find()
            ->select('albumId')
            ->where(
                [
                    'owner' => $owner,
                    'ownerId' => $ownerId,
                    'ownerAttribute' => $ownerAttribute,
                ]
            );
    }
}
