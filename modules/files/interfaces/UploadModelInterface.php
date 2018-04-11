<?php

namespace app\modules\files\interfaces;

use yii\web\UploadedFile;
use app\modules\files\models\Mediafile;

/**
 * Interface UploadModelInterface
 *
 * @package Itstructure\FilesModule\interfaces
 */
interface UploadModelInterface
{
    const FILE_TYPE_IMAGE = 'image';
    const FILE_TYPE_AUDIO = 'audio';
    const FILE_TYPE_VIDEO = 'video';
    const FILE_TYPE_APP = 'application';
    const FILE_TYPE_APP_WORD = 'word';
    const FILE_TYPE_APP_EXCEL = 'excel';
    const FILE_TYPE_APP_PDF = 'pdf';
    const FILE_TYPE_TEXT = 'text';
    const FILE_TYPE_OTHER = 'other';
    const FILE_TYPE_THUMB = 'thumbnail';

    /**
     * Set mediafile model.
     *
     * @param Mediafile $model
     */
    public function setMediafileModel(Mediafile $model): void;

    /**
     * Get mediafile model.
     *
     * @return Mediafile
     */
    public function getMediafileModel(): Mediafile;

    /**
     * Set file.
     *
     * @param UploadedFile $file|null
     *
     * @return void
     */
    public function setFile(UploadedFile $file = null): void;

    /**
     * Get file.
     *
     * @return mixed
     */
    public function getFile();

    /**
     * Save file in storage and database.
     *
     * @return bool
     */
    public function save(): bool ;

    /**
     * Delete file from storage and database.
     *
     * @return int
     */
    public function delete(): int;

    /**
     * Returns current model id.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Load data.
     * Used from the parent model yii\base\Model.
     *
     * @param $data
     *
     * @param null $formName
     *
     * @return bool
     */
    public function load($data, $formName = null);

    /**
     * Set attributes with their values.
     * Used from the parent model yii\base\Model.
     *
     * @param      $values
     * @param bool $safeOnly
     *
     * @return mixed
     */
    public function setAttributes($values, $safeOnly = true);

    /**
     * Validate data.
     * Used from the parent model yii\base\Model.
     *
     * @param null $attributeNames
     * @param bool $clearErrors
     *
     * @return mixed
     */
    public function validate($attributeNames = null, $clearErrors = true);
}
