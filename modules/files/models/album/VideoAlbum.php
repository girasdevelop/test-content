<?php
namespace app\modules\files\models\album;

use yii\helpers\ArrayHelper;
use app\modules\files\behaviors\BehaviorMediafile;
use app\modules\files\interfaces\UploadModelInterface;
use app\modules\files\models\{ActiveRecord, OwnersMediafiles};

/**
 * This is the model class for video album.
 *
 * @property array $video
 *
 * @package Itstructure\FilesModule\models\album
 */
class VideoAlbum extends Album
{
    /**
     * @var array video(array of 'mediafile id' or 'mediafile url').
     */
    public $video;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [
                UploadModelInterface::FILE_TYPE_VIDEO,
                function($attribute){
                    if (!is_array($this->{$attribute})){
                        $this->addError($attribute, 'Video field content must be an array.');
                    }
                },
                'skipOnError' => false,
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'mediafile' => [
                'class' => BehaviorMediafile::class,
                'name' => self::ALBUM_TYPE_VIDEO,
                'attributes' => [
                    UploadModelInterface::FILE_TYPE_THUMB,
                    UploadModelInterface::FILE_TYPE_VIDEO,
                ],
            ]
        ]);
    }

    /**
     * Get album's video.
     *
     * @return ActiveRecord[]
     */
    public function getVideoFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, static::getFileType(self::ALBUM_TYPE_VIDEO));
    }
}
