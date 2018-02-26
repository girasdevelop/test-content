<?php

namespace app\modules\files\models;

use Yii;

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
 */
class Album extends \yii\db\ActiveRecord
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
                    'created_at',
                ],
                'required',
            ],
            [
                ['description'],
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
                    'title',
                    'type',
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
            'title' => 'Title',
            'description' => 'Description',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwnersAlbums()
    {
        return $this->hasMany(OwnersAlbums::class, ['albumId' => 'id']);
    }
}
