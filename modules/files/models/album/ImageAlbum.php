<?php
namespace app\modules\files\models\album;

use yii\helpers\ArrayHelper;
use app\modules\files\behaviors\BehaviorMediafile;
use app\modules\files\interfaces\UploadModelInterface;
use app\modules\files\models\{ActiveRecord, OwnersMediafiles};

/**
 * This is the model class for image album.
 *
 * @property array $image
 *
 * @package Itstructure\FilesModule\models\album
 */
class ImageAlbum extends Album
{
    /**
     * @var array image(array of 'mediafile id' or 'mediafile url').
     */
    public $image;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [
                UploadModelInterface::FILE_TYPE_IMAGE,
                function($attribute){
                    if (!is_array($this->{$attribute})){
                        $this->addError($attribute, 'Image field content must be an array.');
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
                'name' => self::ALBUM_TYPE_IMAGE,
                'attributes' => [
                    UploadModelInterface::FILE_TYPE_THUMB,
                    UploadModelInterface::FILE_TYPE_IMAGE,
                ],
            ]
        ]);
    }

    /**
     * Get album's images.
     *
     * @return ActiveRecord[]
     */
    public function getImageFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, static::getFileType(self::ALBUM_TYPE_IMAGE));
    }
}
