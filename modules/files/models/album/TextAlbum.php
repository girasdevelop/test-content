<?php
namespace app\modules\files\models\album;

use yii\helpers\ArrayHelper;
use app\modules\files\behaviors\BehaviorMediafile;
use app\modules\files\interfaces\UploadModelInterface;
use app\modules\files\models\{ActiveRecord, OwnersMediafiles};

/**
 * This is the model class for text album.
 *
 * @property array $text
 *
 * @package Itstructure\FilesModule\models\album
 */
class TextAlbum extends Album
{
    /**
     * @var array text(array of 'mediafile id' or 'mediafile url').
     */
    public $text;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [
                UploadModelInterface::FILE_TYPE_TEXT,
                function($attribute){
                    if (!is_array($this->{$attribute})){
                        $this->addError($attribute, 'Text field content must be an array.');
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
                'name' => self::ALBUM_TYPE_TEXT,
                'attributes' => [
                    UploadModelInterface::FILE_TYPE_THUMB,
                    UploadModelInterface::FILE_TYPE_TEXT,
                ],
            ]
        ]);
    }

    /**
     * Get album's text files.
     *
     * @return ActiveRecord[]
     */
    public function getTextFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, static::getFileType(self::ALBUM_TYPE_TEXT));
    }
}
