<?php

namespace app\modules\files\models;

use Yii;

/**
 * This is the model class for table "owners_albums".
 *
 * @property int $albumId
 * @property int $ownerId
 * @property string $owner
 * @property string $ownerAttribute
 *
 * @property Album $album
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
}
