<?php
namespace app\modules\files\models\album;

use yii\helpers\ArrayHelper;
use app\modules\files\behaviors\BehaviorMediafile;
use app\modules\files\interfaces\UploadModelInterface;
use app\modules\files\models\{ActiveRecord, OwnersMediafiles};

/**
 * This is the model class for application album.
 *
 * @property array $application
 *
 * @package Itstructure\FilesModule\models\album
 */
class AppAlbum extends Album
{
    /**
     * @var array application(array of 'mediafile id' or 'mediafile url').
     */
    public $application;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [
                UploadModelInterface::FILE_TYPE_APP,
                function($attribute){
                    if (!is_array($this->{$attribute})){
                        $this->addError($attribute, 'Application field content must be an array.');
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
                'name' => self::ALBUM_TYPE_APP,
                'attributes' => [
                    UploadModelInterface::FILE_TYPE_THUMB,
                    UploadModelInterface::FILE_TYPE_APP,
                ],
            ]
        ]);
    }

    /**
     * Get album's application files.
     *
     * @return ActiveRecord[]
     */
    public function getAppFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, UploadModelInterface::FILE_TYPE_APP);
    }
}
