<?php

namespace app\models;

use app\modules\files\behaviors\BehaviorMediafile;
use yii\helpers\{ArrayHelper, Html};
use Itstructure\AdminModule\models\MultilanguageTrait;
use app\modules\files\Module;
use app\modules\files\behaviors\BehaviorAlbum;
use app\modules\files\models\{Mediafile, OwnersAlbums, OwnersMediafiles};
use app\modules\files\models\album\Album;
use app\modules\files\interfaces\UploadModelInterface;

/**
 * This is the model class for table "catalog".
 *
 * @property int|string $thumbnail thumbnail(mediafile id or url).
 * @property array $albums Existing album ids.
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 */
class Catalog extends ActiveRecord
{
    use MultilanguageTrait;

    /**
     * @var int|string thumbnail(mediafile id or url).
     */
    public $thumbnail;

    /**
     * @var array
     */
    public $albums = [];

    /**
     * Initialize.
     * Set albums, that catalog has.
     */
    public function init()
    {
        $this->albums = $this->getAlbums();

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'created_at',
                    'updated_at',
                ],
                'safe',
            ],
            [
                UploadModelInterface::FILE_TYPE_THUMB,
                function($attribute){
                    if (!is_numeric($this->{$attribute}) && !is_string($this->{$attribute})){
                        $this->addError($attribute, 'Tumbnail content must be a numeric or string.');
                    }
                },
                'skipOnError' => false,
            ],
            [
                'albums',
                'each',
                'rule' => ['integer'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'mediafile' => [
                'class' => BehaviorMediafile::class,
                'name' => static::tableName(),
                'attributes' => [
                    UploadModelInterface::FILE_TYPE_THUMB,
                ],
            ],
            'albums' => [
                'class' => BehaviorAlbum::class,
                'name' => static::tableName(),
                'attributes' => [
                    'albums',
                ],
            ],
        ]);
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return ArrayHelper::merge(parent::attributes(), [
            UploadModelInterface::FILE_TYPE_THUMB,
            'albums',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get albums, that catalog has.
     * @return Album[]
     */
    public function getAlbums()
    {
        $albums = OwnersAlbums::getAlbumsQuery([
            'owner' => $this->tableName(),
            'ownerId' => $this->id,
            'ownerAttribute' => 'albums',
        ])->select([
            'id',
            'title'
        ])->all();

        return array_map(function(Album $item) {
            return $item->id;
        }, $albums);
    }

    /**
     * Get album thumb image.
     * @param array  $options
     * @return mixed
     */
    public function getDefaultThumbImage(array $options = [])
    {
        $thumbnailModel = $this->getThumbnailModel();

        if (null === $thumbnailModel){
            return null;
        }

        $url = $thumbnailModel->getThumbUrl(Module::DEFAULT_THUMB_ALIAS);

        if (empty($url)) {
            return null;
        }

        if (empty($options['alt'])) {
            $options['alt'] = $thumbnailModel->alt;
        }

        return Html::img($url, $options);
    }

    /**
     * Get catalog's thumbnail.
     * @return array|null|\yii\db\ActiveRecord|Mediafile
     */
    public function getThumbnailModel()
    {
        return OwnersMediafiles::getOwnerThumbnail($this->tableName(), $this->id);
    }
}
