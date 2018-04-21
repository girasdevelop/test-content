<?php

namespace app\modules\files\models;

use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use app\modules\files\Module;
use app\modules\files\helpers\Html;
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
 * @property string $title
 * @property string $description
 * @property string $thumbs
 * @property string $storage
 * @property string $advance
 * @property int $created_at
 * @property int $updated_at
 * @property Module $_module
 * @property OwnersMediafiles[] $ownersMediafiles
 *
 * @package Itstructure\FilesModule\models
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class Mediafile extends ActiveRecord
{
    /**
     * @var Module
     */
    private $_module;

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
                    'storage',
                ],
                'required',
            ],
            [
                [
                    'alt',
                    'title',
                    'description',
                    'thumbs',
                    'storage',
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
            'title' => 'Title',
            'description' => 'Description',
            'thumbs' => 'Thumbs',
            'storage' => 'Storage',
            'advance' => 'Advance',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return Module
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
     * @param string $url
     * @return Mediafile
     */
    public static function findByUrl(string $url): Mediafile
    {
        return self::findOne(['url' => $url]);
    }

    /**
     * Search models by file types.
     * @param array $types
     * @return ActiveRecord|array
     */
    public static function findByTypes(array $types): ActiveRecord
    {
        return self::find()->filterWhere(['in', 'type', $types])->all();
    }

    /**
     * Add owner to mediafiles table.
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
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
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
     * @return bool
     */
    public static function removeOwner(int $ownerId, string $owner, string $ownerAttribute): bool
    {
        $deleted = OwnersMediafiles::deleteAll([
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
     * Get preview html.
     * @param string $baseUrl Url to asset files which is formed on the BaseAsset.
     * @param string $location Where is this function calling:
     *      existing - if view template renders files, which are exist;
     *      fileinfo - for "fileinfo/index" view template of "filemanager";
     *      fileitem - for "_fileItem" view template of "filemanager".
     * You can set new location attributes by custom in Module "previewOptions"
     * before using in this getPreview() function inside the view template.
     * It is necessary to set location attributes for certain types of mediafiles.
     * See how it's done in "preview-options" config file as an example.
     * @param array $options Options for the next tags, which must be the keys of the options array:
     *      1. Main tag:
     *      mainTag - main preview html tag. Can contain different html tag options.
     *                And also can contain "alias" from the number of module constant aliases:
     *                default, original, small, medium, large.
     *      2. Addition tags:
     *      leftTag - tag that is located to the left of the "mainTag";
     *      rightTag - tag that is located to the right of the "mainTag";
     *      externalTag - tag in which the mainTag with leftTag and rightTag are embedded.
     * Importantly! Addition tag keys must contain the next attributes:
     * name - the name of addition tag.
     * options - html options of addition tag.
     * By default, the mainTag is already in the "preview-options" configuration file.
     * You can insert configurations values of addition tags through the third parameter of this function.
     * @return string
     */
    public function getPreview(string $baseUrl = '', string $location = null, array $options = []): string
    {
        $module = $this->getModule();

        if ($this->isImage()){
            $options = null === $location ? $options : ArrayHelper::merge($module->getPreviewOptions(UploadModelInterface::FILE_TYPE_IMAGE, $location), $options);
            $preview = $this->getImagePreview(isset($options['mainTag']) ? $options['mainTag'] : []);

        } elseif ($this->isAudio()){
            $options = null === $location ? $options : ArrayHelper::merge($module->getPreviewOptions(UploadModelInterface::FILE_TYPE_AUDIO, $location), $options);
            $preview = $this->getAudioPreview(isset($options['mainTag']) ? $options['mainTag'] : []);

        } elseif ($this->isVideo()){
            $options = null === $location ? $options : ArrayHelper::merge($module->getPreviewOptions(UploadModelInterface::FILE_TYPE_VIDEO, $location), $options);
            $preview = $this->getVideoPreview(isset($options['mainTag']) ? $options['mainTag'] : []);

        } elseif ($this->isApp()){
            $options = null === $location ? $options : ArrayHelper::merge($module->getPreviewOptions(UploadModelInterface::FILE_TYPE_APP, $location), $options);
            $preview = $this->getAppPreview($baseUrl, isset($options['mainTag']) ? $options['mainTag'] : []);

        } elseif ($this->isText()){
            $options = null === $location ? $options : ArrayHelper::merge($module->getPreviewOptions(UploadModelInterface::FILE_TYPE_TEXT, $location), $options);
            $preview = $this->getTextPreview($baseUrl, isset($options['mainTag']) ? $options['mainTag'] : []);

        } else {
            $options = null === $location ? $options : ArrayHelper::merge($module->getPreviewOptions(UploadModelInterface::FILE_TYPE_OTHER, $location), $options);
            $preview = $this->getOtherPreview($baseUrl, isset($options['mainTag']) ? $options['mainTag'] : []);
        }

        if (isset($options['leftTag']) && is_array($options['leftTag'])){
            $preview = $this->compactPreviewAdditionTags($preview, 'leftTag', $options['leftTag']);
        }

        if (isset($options['rightTag']) && is_array($options['rightTag'])){
            $preview = $this->compactPreviewAdditionTags($preview, 'rightTag', $options['rightTag']);
        }

        if (isset($options['externalTag']) && is_array($options['externalTag'])){
            $preview = $this->compactPreviewAdditionTags($preview, 'externalTag', $options['externalTag']);
        }

        return $preview;
    }

    /**
     * Get image preview.
     * @param array $mainTagOptions
     * @return string
     */
    public function getImagePreview(array $mainTagOptions): string
    {
        if (!isset($mainTagOptions['alt'])) {
            $mainTagOptions['alt'] = $this->alt;
        }

        if (isset($mainTagOptions['alias']) && is_string($mainTagOptions['alias'])){
            return Html::img($this->getThumbUrl($mainTagOptions['alias']), $mainTagOptions);
        }

        return Html::img($this->getThumbUrl(Module::DEFAULT_THUMB_ALIAS), $mainTagOptions);
    }

    /**
     * Get audio preview.
     * @param array $mainTagOptions
     * @return string
     */
    public function getAudioPreview(array $mainTagOptions): string
    {
        return Html::audio($this->url, ArrayHelper::merge([
                'source' => [
                    'type' => $this->type
                ]
            ], $mainTagOptions)
        );
    }

    /**
     * Get video preview.
     * @param array $mainTagOptions
     * @return string
     */
    public function getVideoPreview(array $mainTagOptions): string
    {
        return Html::video($this->url, ArrayHelper::merge([
                'source' => [
                    'type' => $this->type
                ]
            ], $mainTagOptions)
        );
    }

    /**
     * Get application preview.
     * @param string $baseUrl
     * @param array $mainTagOptions
     * @return string
     */
    public function getAppPreview(string $baseUrl = '', array $mainTagOptions): string
    {
        return Html::img($this->getAppPreviewUrl($baseUrl), $mainTagOptions);
    }

    /**
     * Get text preview.
     * @param string $baseUrl
     * @param array $mainTagOptions
     * @return string
     */
    public function getTextPreview(string $baseUrl = '', array $mainTagOptions): string
    {
        return Html::img($this->getTextPreviewUrl($baseUrl), $mainTagOptions);
    }

    /**
     * Get other preview.
     * @param string $baseUrl
     * @param array $mainTagOptions
     * @return string
     */
    public function getOtherPreview(string $baseUrl = '', array $mainTagOptions): string
    {
        return Html::img($this->getOtherPreviewUrl($baseUrl), $mainTagOptions);
    }

    /**
     * Get application preview url.
     * @param string $baseUrl
     * @return string
     */
    public function getAppPreviewUrl($baseUrl = ''): string
    {
        if (!empty($baseUrl) && is_string($baseUrl)){
            $root = $baseUrl.DIRECTORY_SEPARATOR;
        } else {
            $root = DIRECTORY_SEPARATOR;
        }
        $module = $this->getModule();

        if ($this->isExcel()){
            $url = $root . $module->thumbStubUrls[UploadModelInterface::FILE_TYPE_APP_EXCEL];

        } elseif ($this->isPdf()){
            $url = $root . $module->thumbStubUrls[UploadModelInterface::FILE_TYPE_APP_PDF];

        } elseif ($this->isWord()){
            $url = $root . $module->thumbStubUrls[UploadModelInterface::FILE_TYPE_APP_WORD];

        } else {
            $url = $root . $module->thumbStubUrls[UploadModelInterface::FILE_TYPE_APP];
        }

        return $url;
    }

    /**
     * Get text preview url.
     * @param string $baseUrl
     * @return string
     */
    public function getTextPreviewUrl($baseUrl = ''): string
    {
        if (!empty($baseUrl) && is_string($baseUrl)){
            $root = $baseUrl.DIRECTORY_SEPARATOR;
        } else {
            $root = DIRECTORY_SEPARATOR;
        }

        return $root . $this->getModule()->thumbStubUrls[UploadModelInterface::FILE_TYPE_APP];
    }

    /**
     * Get other preview url.
     * @param string $baseUrl
     * @return string
     */
    public function getOtherPreviewUrl($baseUrl = ''): string
    {
        if (!empty($baseUrl) && is_string($baseUrl)){
            $root = $baseUrl.DIRECTORY_SEPARATOR;
        } else {
            $root = DIRECTORY_SEPARATOR;
        }
        $module = $this->getModule();

        return $root . $module->thumbStubUrls[UploadModelInterface::FILE_TYPE_OTHER];
    }

    /**
     * Get thumbnails.
     * @return array
     */
    public function getThumbs(): array
    {
        return unserialize($this->thumbs) ?: [];
    }

    /**
     * Get thumb url.
     * @param string $alias
     * @return string
     */
    public function getThumbUrl(string $alias): string
    {
        if ($alias === Module::ORIGINAL_THUMB_ALIAS) {
            return $this->url;
        }

        $thumbs = $this->getThumbs();

        return !empty($thumbs[$alias]) ? $thumbs[$alias] : '';
    }

    /**
     * Get thumb image.
     * @param string $alias
     * @param array  $options
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
     * Get default thumbnail url.
     * @param string $baseUrl
     * @return string
     */
    public function getDefaultThumbUrl($baseUrl = ''): string
    {
        if ($this->isImage()){
            return $this->getThumbUrl(Module::DEFAULT_THUMB_ALIAS);

        } elseif ($this->isApp()){
            return $this->getAppPreviewUrl($baseUrl);

        } elseif ($this->isText()){
            return $this->getTextPreviewUrl($baseUrl);

        } else {
            return $this->getOtherPreviewUrl($baseUrl);
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

    /**
     * Check if the file is image.
     * @return bool
     */
    public function isImage(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_IMAGE) !== false;
    }

    /**
     * Check if the file is audio.
     * @return bool
     */
    public function isAudio(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_AUDIO) !== false;
    }

    /**
     * Check if the file is video.
     * @return bool
     */
    public function isVideo(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_VIDEO) !== false;
    }

    /**
     * Check if the file is text.
     * @return bool
     */
    public function isText(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_TEXT) !== false;
    }

    /**
     * Check if the file is application.
     * @return bool
     */
    public function isApp(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_APP) !== false;
    }

    /**
     * Check if the file is excel.
     * @return bool
     */
    public function isExcel(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_APP_EXCEL) !== false;
    }

    /**
     * Check if the file is pdf.
     * @return bool
     */
    public function isPdf(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_APP_PDF) !== false;
    }

    /**
     * Check if the file is word.
     * @return bool
     */
    public function isWord(): bool
    {
        return strpos($this->type, UploadModelInterface::FILE_TYPE_APP_WORD) !== false;
    }

    /**
     * Compact addition tag with the main preview.
     * @param string $preview
     * @param string $additionTagType
     * @param array $additionTagConfig
     * @throws InvalidConfigException
     * @return string
     */
    private function compactPreviewAdditionTags(string $preview, string $additionTagType, array $additionTagConfig): string
    {
        if (!isset($additionTagConfig['name']) || !is_string($additionTagConfig['name'])){
            throw new InvalidConfigException('Bad name configuration for addition tag: '.$additionTagType);
        }

        if (!isset($additionTagConfig['options']) || !is_array($additionTagConfig['options'])){
            throw new InvalidConfigException('Bad options configuration for addition tag: '.$additionTagType);
        }

        switch ($additionTagType) {
            case 'leftTag': {
                $content = isset($additionTagConfig['content']) ? isset($additionTagConfig['content']) : '';
                $preview = Html::tag($additionTagConfig['name'], $content, $additionTagConfig['options']) . $preview;
                break;
            }

            case 'rightTag': {
                $content = isset($additionTagConfig['content']) ? isset($additionTagConfig['content']) : '';
                $preview = $preview . Html::tag($additionTagConfig['name'], $content, $additionTagConfig['options']);
                break;
            }

            case 'externalTag': {
                $preview = Html::tag($additionTagConfig['name'], $preview, $additionTagConfig['options']);
                break;
            }

            default: {
                throw new InvalidConfigException('Unknown type of addition tag');
            }
        }

        return $preview;
    }
}
