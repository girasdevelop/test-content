<?php
namespace app\modules\files\models\album;

use yii\helpers\ArrayHelper;
use app\modules\files\behaviors\BehaviorMediafile;
use app\modules\files\interfaces\UploadModelInterface;
use app\modules\files\models\{ActiveRecord, OwnersMediafiles};

/**
 * This is the model class for audio album.
 *
 * @property array $audio
 *
 * @package Itstructure\FilesModule\models\album
 */
class AudioAlbum extends Album
{
    /**
     * @var array audio(array of 'mediafile id' or 'mediafile url').
     */
    public $audio;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [
                UploadModelInterface::FILE_TYPE_AUDIO,
                function($attribute){
                    if (!is_array($this->{$attribute})){
                        $this->addError($attribute, 'Audio field content must be an array.');
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
                'name' => self::ALBUM_TYPE_AUDIO,
                'attributes' => [
                    UploadModelInterface::FILE_TYPE_THUMB,
                    UploadModelInterface::FILE_TYPE_AUDIO,
                ],
            ]
        ]);
    }

    /**
     * Get album's audio.
     *
     * @return ActiveRecord[]
     */
    public function getAudioFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, UploadModelInterface::FILE_TYPE_AUDIO);
    }
}
