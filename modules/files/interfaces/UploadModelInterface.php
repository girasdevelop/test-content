<?php

namespace app\modules\files\interfaces;

use app\modules\files\models\Mediafile;

/**
 * Interface UploadModelInterface
 *
 * @package Itstructure\FilesModule\interfaces
 */
interface UploadModelInterface
{
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
     */
    public function setFile();

    /**
     * Get file.
     */
    public function getFile();

    /**
     * Save data.
     *
     * @return bool
     */
    public function save(): bool ;

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
