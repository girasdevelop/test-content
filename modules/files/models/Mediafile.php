<?php

namespace app\modules\files\models;

use yii\helpers\Html;
use yii\base\InvalidConfigException;
use app\modules\files\Module;
use app\modules\files\components\ThumbConfig;
use app\modules\files\interfaces\{ThumbConfigInterface, UploadModelInterface};

/**
 * This is the model class for table "mediafiles".
 *
 * @property int $id
 * @property string $filename
 * @property string $type
 * @property string $url
 * @property string $alt
 * @property int $size
 * @property string $description
 * @property string $thumbs
 * @property string $advance
 * @property int $created_at
 * @property int $updated_at
 *
 * @property OwnersMediafiles[] $ownersMediafiles
 *
 * @package Itstructure\FilesModule\models
 */
class Mediafile extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mediafiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'filename',
                    'type',
                    'url',
                    'size',
                ],
                'required',
            ],
            [
                [
                    'alt',
                    'description',
                    'thumbs',
                    'advance',
                ],
                'string',
            ],
            [
                [
                    'size',
                ],
                'integer',
            ],
            [
                [
                    'filename',
                    'type',
                    'url',
                ],
                'string',
                'max' => 255,
            ],
            [
                [
                    'created_at',
                    'updated_at',
                ],
                'safe',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filename' => 'Filename',
            'type' => 'Type',
            'url' => 'Url',
            'alt' => 'Alt',
            'size' => 'Size',
            'description' => 'Description',
            'thumbs' => 'Thumbs',
            'advance' => 'Advance',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Find model by url.
     *
     * @param string $url
     *
     * @return Mediafile
     */
    public static function findByUrl(string $url): Mediafile
    {
        return self::findOne(['url' => $url]);
    }

    /**
     * Search models by file types.
     *
     * @param array $types
     *
     * @return ActiveRecord|array
     */
    public static function findByTypes(array $types): ActiveRecord
    {
        return self::find()->filterWhere(['in', 'type', $types])->all();
    }

    /**
     * Add owner to mediafiles table.
     *
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
     *
     * @return bool
     */
    public function addOwner(int $ownerId, string $owner, string $ownerAttribute): bool
    {
        $owners = new OwnersMediafiles();
        $owners->mediafileId = $this->id;
        $owners->owner = $owner;
        $owners->ownerId = $ownerId;
        $owners->ownerAttribute = $ownerAttribute;

        return $owners->save();
    }

    /**
     * Remove this mediafile owner.
     *
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
     *
     * @return bool
     */
    public static function removeOwner(int $ownerId, string $owner, string $ownerAttribute): bool
    {
        $deleted = OwnersMediafiles::findOne([
            'ownerId' => $ownerId,
            'owner' => $owner,
            'ownerAttribute' => $ownerAttribute,
        ]);

        return $deleted > 0;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwners()
    {
        return $this->hasMany(OwnersMediafiles::class, ['mediafileId' => 'id']);
    }

    /**
     * Get thumbnails.
     *
     * @return array
     */
    public function getThumbs(): array
    {
        return unserialize($this->thumbs) ?: [];
    }

    /**
     * Get thumb url.
     *
     * @param string $alias
     *
     * @return string
     */
    public function getThumbUrl(string $alias): string
    {
        if ($alias === 'original') {
            return $this->url;
        }

        $thumbs = $this->getThumbs();

        return !empty($thumbs[$alias]) ? $thumbs[$alias] : '';
    }

    /**
     * Get thumb image.
     *
     * @param string $alias
     * @param array  $options
     *
     * @return string
     */
    public function getThumbImage(string $alias, array $options = []): string
    {
        $url = $this->getThumbUrl($alias);

        if (empty($url)) {
            return '';
        }

        if (empty($options['alt'])) {
            $options['alt'] = $this->alt;
        }

        return Html::img($url, $options);
    }

    /**
     * Check if the file is image.
     *
     * @return bool
     */
    public function isImage(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_IMAGE) !== false;
    }

    /**
     * Get default thumbnail url.
     *
     * @throws InvalidConfigException
     *
     * @return string
     */
    public function getDefaultThumbUrl(): string
    {
        if ($this->isImage()) {

            /* @var ThumbConfigInterface|ThumbConfig $thumbConfig */
            $thumbConfig = Module::configureThumb(Module::DEFAULT_THUMB_ALIAS, $this->thumbsConfig[Module::DEFAULT_THUMB_ALIAS]);

            $originalFile = pathinfo($this->mediafileModel->url);
            $dirname = $originalFile['dirname'];
            $filename = $originalFile['filename'];
            $extension = $originalFile['extension'];

            return $dirname .
            $this->directorySeparator .
            $this->getThumbFilename($filename, $extension, Module::DEFAULT_THUMB_ALIAS, $thumbConfig->width, $thumbConfig->height);
        }

        return $this->thumbStubUrl;
    }
}
