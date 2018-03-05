<?php

namespace app\modules\files\models;

use Yii;
use yii\db\ActiveQuery;
use app\modules\files\models\upload\BaseUpload;

/**
 * This is the model class for table "owners_mediafiles".
 *
 * @property int $mediafileId
 * @property int $ownerId
 * @property string $owner
 * @property string $ownerAttribute
 *
 * @property Mediafile $mediafile
 *
 * @package Itstructure\FilesModule\models
 */
class OwnersMediafiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'owners_mediafiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'mediafileId',
                    'ownerId',
                    'owner',
                    'ownerAttribute',
                ],
                'required',
            ],
            [
                [
                    'mediafileId',
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
                    'mediafileId',
                    'ownerId',
                    'owner',
                    'ownerAttribute',
                ],
                'unique',
                'targetAttribute' => [
                    'mediafileId',
                    'ownerId',
                    'owner',
                    'ownerAttribute',
                ],
            ],
            [
                ['mediafileId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Mediafile::class,
                'targetAttribute' => ['mediafileId' => 'id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'mediafileId' => 'Mediafile ID',
            'ownerId' => 'Owner ID',
            'owner' => 'Owner',
            'ownerAttribute' => 'Owner Attribute',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaFile()
    {
        return $this->hasOne(Mediafile::class, ['id' => 'mediafileId']);
    }

    /**
     * Get all mediafiles by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     * @param null|string $ownerAttribute
     *
     * @return ActiveRecord[]
     */
    public static function getMediaFiles(string $owner, int $ownerId, string $ownerAttribute = null)
    {
        return Mediafile::find()
            ->where(
                [
                    'id' =>  static::getMediafileIds($owner, $ownerId, $ownerAttribute)->asArray()
                ]
            )->all();
    }

    public static function getThumbnail()
    {

    }

    /**
     * Get image files by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return ActiveRecord[]
     */
    public static function getImageFiles(string $owner, int $ownerId)
    {
        return static::getMediaFiles($owner, $ownerId, BaseUpload::FILE_TYPE_IMAGE);
    }

    /**
     * Get audio files by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return ActiveRecord[]
     */
    public static function getAudioFiles(string $owner, int $ownerId)
    {
        return static::getMediaFiles($owner, $ownerId, BaseUpload::FILE_TYPE_AUDIO);
    }

    /**
     * Get video files by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return ActiveRecord[]
     */
    public static function getVideoFiles(string $owner, int $ownerId)
    {
        return static::getMediaFiles($owner, $ownerId, BaseUpload::FILE_TYPE_VIDEO);
    }

    /**
     * Get app files by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return ActiveRecord[]
     */
    public static function getAppFiles(string $owner, int $ownerId)
    {
        return static::getMediaFiles($owner, $ownerId, BaseUpload::FILE_TYPE_APP);
    }

    /**
     * Get text files by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return ActiveRecord[]
     */
    public static function getTextFiles(string $owner, int $ownerId)
    {
        return static::getMediaFiles($owner, $ownerId, BaseUpload::FILE_TYPE_TEXT);
    }

    /**
     * Get other files by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return ActiveRecord[]
     */
    public static function getOtherFiles(string $owner, int $ownerId)
    {
        return static::getMediaFiles($owner, $ownerId, BaseUpload::FILE_TYPE_OTHER);
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
    private static function getMediafileIds(string $owner, int $ownerId, string $ownerAttribute): ActiveQuery
    {
        return static::find()
            ->select('mediafileId')
            ->where(
                [
                    'owner' => $owner,
                    'ownerId' => $ownerId,
                    'ownerAttribute' => $ownerAttribute,
                ]
            );
    }
}
