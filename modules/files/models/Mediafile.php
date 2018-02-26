<?php

namespace app\modules\files\models;

use Yii;

/**
 * This is the model class for table "mediafiles".
 *
 * @property int $id
 * @property string $filename
 * @property string $type
 * @property string $url
 * @property string $alt
 * @property int $size
 * @property string $description
 * @property string $thumbs
 * @property string $advance
 * @property int $created_at
 * @property int $updated_at
 *
 * @property OwnersMediafiles[] $ownersMediafiles
 */
class Mediafile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mediafiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'filename',
                    'type',
                    'url',
                    'size',
                    'created_at',
                ],
                'required',
            ],
            [
                [
                    'alt',
                    'description',
                    'thumbs',
                    'advance',
                ],
                'string',
            ],
            [
                [
                    'size',
                    'created_at',
                    'updated_at',
                ],
                'integer',
            ],
            [
                [
                    'filename',
                    'type',
                    'url',
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
            'filename' => 'Filename',
            'type' => 'Type',
            'url' => 'Url',
            'alt' => 'Alt',
            'size' => 'Size',
            'description' => 'Description',
            'thumbs' => 'Thumbs',
            'advance' => 'Advance',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwnersMediafiles()
    {
        return $this->hasMany(OwnersMediafiles::class, ['mediafileId' => 'id']);
    }
}
