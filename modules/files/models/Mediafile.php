<?php

namespace app\modules\files\models;

use yii\helpers\Html;
use yii\base\InvalidConfigException;
use app\modules\files\Module;
use app\modules\files\interfaces\UploadModelInterface;

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
 * @property Module $_module
 *
 * @property OwnersMediafiles[] $ownersMediafiles
 *
 * @package Itstructure\FilesModule\models
 */
class Mediafile extends ActiveRecord
{
    /**
     * @var null|Module
     */
    public $_module = null;

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
     * @return Module|null|static
     */
    public function getModule(): Module
    {

        if ($this->_module === null){
            $this->_module = Module::getInstance();
        }

        return $this->_module;
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
     * Check if the file is audio.
     *
     * @return bool
     */
    public function isAudio(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_AUDIO) !== false;
    }

    /**
     * Check if the file is video.
     *
     * @return bool
     */
    public function isVideo(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_VIDEO) !== false;
    }

    /**
     * Check if the file is text.
     *
     * @return bool
     */
    public function isText(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_TEXT) !== false;
    }

    /**
     * Check if the file is application.
     *
     * @return bool
     */
    public function isApp(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_APP) !== false;
    }

    /**
     * Get default thumbnail url.
     *
     * @param string $assetUrl
     *
     * @return string
     */
    public function getDefaultThumbUrl($assetUrl = ''): string
    {
        if (!empty($assetUrl) && is_string($assetUrl)){
            $root = $assetUrl.DIRECTORY_SEPARATOR;
        } else {
            $root = '';
        }

        $module = $this->getModule();

        if ($this->isImage()) {
            return $this->getThumbUrl(Module::DEFAULT_THUMB_ALIAS);

        } elseif ($this->isAudio()){
            return $root . $module->thumbStubUrls[UploadModelInterface::FILE_TYPE_AUDIO];

        } elseif ($this->isVideo()){
            return $root . $module->thumbStubUrls[UploadModelInterface::FILE_TYPE_VIDEO];

        } elseif ($this->isText()){
            return $root . $module->thumbStubUrls[UploadModelInterface::FILE_TYPE_TEXT];

        } elseif ($this->isApp()){
            return $root . $module->thumbStubUrls[UploadModelInterface::FILE_TYPE_APP];

        } else {
            return $root . $module->thumbStubUrls[UploadModelInterface::FILE_TYPE_OTHER];
        }

    }

    /**
     * @return string file size
     */
    public function getFileSize()
    {
        \Yii::$app->formatter->sizeFormatBase = 1000;
        return \Yii::$app->formatter->asShortSize($this->size, 0);
    }
}
