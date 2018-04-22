<?php
use yii\helpers\ArrayHelper;
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\models\album\Album;
use app\modules\files\widgets\FileSetter;
use app\modules\files\interfaces\UploadModelInterface;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $albumType string */
/* @var $thumbnailModel Mediafile|null */
/* @var $ownerParams array */
?>

<div id="thumbnail-container">
    <?php echo $model->getDefaultThumbImage(); ?>
</div>
<?php echo FileSetter::widget(ArrayHelper::merge([
        'model' => $model,
        'attribute' => UploadModelInterface::FILE_TYPE_THUMB,
        'neededFileType' => UploadModelInterface::FILE_TYPE_THUMB,
        'buttonName' => Module::t('main', 'Set thumbnail'),
        'resetButtonName' => Module::t('main', 'Clear'),
        'options' => [
            'value' => ($thumbnailModel = $model->getThumbnailModel()) !== null ? $thumbnailModel->{FileSetter::INSERTED_DATA_ID} : null,
        ],
        'mediafileContainer' => '#thumbnail-container',
        'subDir' => strtolower($albumType)
    ], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge([
        'ownerAttribute' => UploadModelInterface::FILE_TYPE_THUMB
    ], $ownerParams) : [])
); ?>
