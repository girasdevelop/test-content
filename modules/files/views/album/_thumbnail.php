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
    <?php if (isset($thumbnailModel) && $thumbnailModel instanceof Mediafile): ?>
        <img src="<?php echo $thumbnailModel->getThumbUrl(Module::DEFAULT_THUMB_ALIAS) ?>">
    <?php endif; ?>
</div>
<?php echo FileSetter::widget(ArrayHelper::merge([
    'model' => $model,
    'attribute' => UploadModelInterface::FILE_TYPE_THUMB,
    'ownerAttribute' => UploadModelInterface::FILE_TYPE_THUMB,
    'buttonName' => Module::t('main', 'Set thumbnail'),
    'mediafileContainer' => '#thumbnail-container',
    'subDir' => strtolower($albumType)
], isset($ownerParams) && is_array($ownerParams) ? $ownerParams : [])
); ?>
