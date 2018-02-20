<?php

namespace app\modules\files\models;

use Yii;

/**
 * This is the model class for table "owners".
 *
 * @property int $mediafileId
 * @property int $albumId
 * @property int $ownerId
 * @property string $owner
 * @property string $propertyType
 *
 * @property Album $album
 * @property Mediafile $mediafile
 */
class Owner extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'owners';
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
                    'albumId',
                    'ownerId',
                    'owner',
                    'propertyType',
                ],
                'required',
            ],
            [
                [
                    'mediafileId',
                    'albumId',
                    'ownerId',
                ],
                'integer',
            ],
            [
                [
                    'owner',
                    'propertyType',
                ],
                'string',
                'max' => 255,
            ],
            [
                [
                    'mediafileId',
                    'albumId',
                    'ownerId',
                    'owner',
                    'propertyType',
                ],
                'unique',
                'targetAttribute' => [
                    'mediafileId',
                    'albumId',
                    'ownerId',
                    'owner',
                    'propertyType',
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
            'mediafileId' => 'Mediafile ID',
            'albumId' => 'Album ID',
            'ownerId' => 'Owner ID',
            'owner' => 'Owner',
            'propertyType' => 'Property Type',
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
