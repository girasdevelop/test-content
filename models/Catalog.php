<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use Itstructure\AdminModule\models\MultilanguageTrait;
use app\modules\files\behaviors\BehaviorAlbum;
use app\modules\files\models\OwnersAlbums;
use app\modules\files\models\album\Album;

/**
 * This is the model class for table "catalog".
 *
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
                'class' => BehaviorAlbum::class,
                'name' => static::tableName(),
                'attributes' => [
                    'albums'
                ],
            ]
        ]);
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return ArrayHelper::merge(parent::attributes(), [
            'albums'
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
}
