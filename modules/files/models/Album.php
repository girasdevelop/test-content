<?php

namespace app\modules\files\models;

use Yii;

/**
 * This is the model class for table "albums".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 *
 * @property AlbumsMediafiles[] $albumsMediafiles
 * @property Mediafile[] $mediafiles
 * @property Owner[] $owners
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
                ['title'],
                'required',
            ],
            [
                ['description'],
                'string',
            ],
            [
                ['title'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlbumsMediafiles()
    {
        return $this->hasMany(AlbumsMediafiles::class, ['albumId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediafiles()
    {
        return $this->hasMany(Mediafile::class, ['id' => 'mediafileId'])->viaTable('albums_mediafiles', ['albumId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwners()
    {
        return $this->hasMany(Owner::class, ['albumId' => 'id']);
    }
}
