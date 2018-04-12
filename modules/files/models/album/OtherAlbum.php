<?php
namespace app\modules\files\models\album;

use yii\helpers\ArrayHelper;
use app\modules\files\behaviors\BehaviorMediafile;
use app\modules\files\interfaces\UploadModelInterface;
use app\modules\files\models\{ActiveRecord, OwnersMediafiles};

/**
 * This is the model class for other album.
 *
 * @property array $other
 *
 * @package Itstructure\FilesModule\models\album
 */
class OtherAlbum extends Album
{
    /**
     * @var array other(array of 'mediafile id' or 'mediafile url').
     */
    public $other;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [
                UploadModelInterface::FILE_TYPE_OTHER,
                function($attribute){
                    if (!is_array($this->{$attribute})){
                        $this->addError($attribute, 'Other field content must be an array.');
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
                'name' => self::ALBUM_TYPE_OTHER,
                'attributes' => [
                    UploadModelInterface::FILE_TYPE_THUMB,
                    UploadModelInterface::FILE_TYPE_OTHER,
                ],
            ]
        ]);
    }

    /**
     * Get album's other files.
     *
     * @return ActiveRecord[]
     */
    public function getOtherFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, static::getFileType(self::ALBUM_TYPE_OTHER));
    }
}
