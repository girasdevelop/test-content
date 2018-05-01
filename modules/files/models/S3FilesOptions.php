<?php

namespace app\modules\files\models;

/**
 * This is the model class for table "s3_files_options".
 *
 * @property int $mediafileId Mediafile id.
 * @property string $bucket Bucket.
 * @property string $prefix Prefix path.
 * @property Mediafile $mediafile
 *
 * @package Itstructure\FilesModule\models
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class S3FilesOptions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 's3_files_options';
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
                    'bucket',
                    'prefix',
                ],
                'required',
            ],
            [
                [
                    'mediafileId',
                ],
                'integer',
            ],
            [
                [
                    'bucket',
                    'prefix',
                ],
                'string',
                'max' => 255,
            ],
            [
                [
                    'mediafileId',
                ],
                'unique',
                'targetAttribute' => [
                    'mediafileId',
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
            'bucket' => 'Bucket',
            'key' => 'Key',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaFile()
    {
        return $this->hasOne(Mediafile::class, ['id' => 'mediafileId']);
    }
}
