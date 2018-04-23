<?php

use yii\helpers\ArrayHelper;
use app\modules\files\Module;
use app\modules\files\widgets\FileSetter;
use app\modules\files\interfaces\UploadModelInterface;

/* @var $this Itstructure\AdminModule\components\AdminView */
/* @var $model app\models\Catalog|Itstructure\AdminModule\models\MultilanguageValidateModel */
/* @var $ownerParams array */
?>

<div id="thumbnail-container">
    <?php echo $model->mainModel->getDefaultThumbImage(); ?>
</div>
<?php echo FileSetter::widget(ArrayHelper::merge([
        'model' => $model,
        'attribute' => UploadModelInterface::FILE_TYPE_THUMB,
        'neededFileType' => UploadModelInterface::FILE_TYPE_THUMB,
        'buttonName' => Module::t('main', 'Set thumbnail'),
        'resetButtonName' => Module::t('main', 'Clear'),
        'options' => [
            'value' => ($thumbnailModel = $model->mainModel->getThumbnailModel()) !== null ? $thumbnailModel->{FileSetter::INSERTED_DATA_ID} : null,
        ],
        'mediafileContainer' => '#thumbnail-container',
        'subDir' => $model->mainModel->tableName()
    ], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge([
        'ownerAttribute' => UploadModelInterface::FILE_TYPE_THUMB
    ], $ownerParams) : [])
); ?>
