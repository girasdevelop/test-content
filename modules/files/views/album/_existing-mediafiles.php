<?php
use yii\helpers\ArrayHelper;
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\models\album\{Album};
use app\modules\files\widgets\FileSetter;
use app\modules\files\assets\FileSetterAsset;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $albumType string */
/* @var $fileType string */
/* @var $mediafile Mediafile */
/* @var $ownerParams array */
/* @var $baseUrl string */

$baseUrl = FileSetterAsset::register($this)->baseUrl;
?>

<div class="row">
    <div class="col-md-12">
        <h5><?php echo Module::t('main', 'Existing files'); ?></h5>
        <?php if (!$model->isNewRecord): ?>
            <?php $i=0; ?>
            <?php foreach ($model->getMediaFiles($fileType) as $mediafile): ?>
                <?php $i+=1; ?>
                <div id="mediafile-container-<?php echo $i; ?>">
                    <?php echo $mediafile->getPreview($baseUrl, $mediafile->isImage() ? ['width' => 300, 'thumbAlias' => Module::ORIGINAL_THUMB_ALIAS] : []); ?>
                </div>
                <?php echo FileSetter::widget(ArrayHelper::merge([
                    'model' => $model,
                    'attribute' => $fileType.'[]',
                    'buttonName' => Module::t('main', 'Set '.$fileType),
                    'buttonOptions' => [
                        'id' => $albumType . '-' . $fileType . '-btn-' . $i
                    ],
                    'mediafileContainer' => '#mediafile-container-' . $i,
                    'subDir' => Album::tableName()
                ], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge(['ownerAttribute' => $fileType], $ownerParams) : [])
                ); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
