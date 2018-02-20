<?php

namespace app\modules\files\models;

use Yii;

/**
 * This is the model class for table "albums_mediafiles".
 *
 * @property int $albumId
 * @property int $mediafileId
 *
 * @property Album $album
 * @property Mediafile $mediafile
 */
class AlbumsMediafiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'albums_mediafiles';
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
                    'mediafileId',
                ],
                'required',
            ],
            [
                [
                    'albumId',
                    'mediafileId',
                ],
                'integer',
            ],
            [
                [
                    'albumId',
                    'mediafileId',
                ],
                'unique',
                'targetAttribute' => [
                    'albumId',
                    'mediafileId',
                ],
            ],
            [
                ['albumId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Album::class,
                'targetAttribute' => ['albumId' => 'id'],
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
            'albumId' => 'Album ID',
            'mediafileId' => 'Mediafile ID',
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
     * @return \yii\db\ActiveQuery
     */
    public function getMediafile()
    {
        return $this->hasOne(Mediafile::class, ['id' => 'mediafileId']);
    }
}
