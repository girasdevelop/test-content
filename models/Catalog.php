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
     * Existing album ids.
     * @var array
     */
    public $albums;

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
                function($attribute){
                    if (!is_array($this->{$attribute})){
                        $this->addError($attribute, 'Albums field content must be an array.');
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
                'class' => BehaviorAlbum::class,
                'name' => static::tableName(),
                'attributes' => [
                    'albums'
                ],
            ]
        ]);
    }

    /**
     * Attributes.
     * @return array
     */
    public function attributes(): array
    {
        return ArrayHelper::merge(
            parent::attributes(),
            [
                'albums'
            ]
        );
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
     * @param array $albums
     */
    /*public function setAlbums(array $albums): void
    {
        Album::removeOwner($this->id, $this->tableName(), 'albums');

        foreach ($albums as $albumId) {
            $currentAlbum = Album::findOne(['id' => $albumId]);
            $currentAlbum->addOwner($this->id, $this->tableName(), 'albums');
        }
    }*/

    /**
     * @return Album[]
     */
    public function getAlbums()
    {
        return OwnersAlbums::getAlbums($this->tableName(), $this->id);
    }
}
