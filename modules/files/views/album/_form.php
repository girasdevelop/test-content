<?php

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\models\album\{Album};
use app\modules\files\helpers\Html;
use app\modules\files\widgets\FileSetter;
use app\modules\files\assets\FileSetterAsset;
use app\modules\files\interfaces\UploadModelInterface;
use Itstructure\FieldWidgets\{Fields, FieldType};

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $type string */
/* @var $mediafile Mediafile */
/* @var $form yii\widgets\ActiveForm */
/* @var $thumbnailModel Mediafile|null */
/* @var $ownerParams array */
/* @var $baseUrl string */

$baseUrl = FileSetterAsset::register($this)->baseUrl;
?>

<style>
    h5 {
        border-top: 1px solid #8ca68c;
        font-weight: bold;
        padding: 5px;
    }
</style>

<div class="album-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?php echo Fields::widget([
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => FieldType::FIELD_TYPE_TEXT,
                        'label' => Module::t('album', 'Title')
                    ],
                    [
                        'name' => 'description',
                        'type' => FieldType::FIELD_TYPE_TEXT_AREA,
                        'label' => Module::t('album', 'Description')
                    ],
                    [
                        'name' => 'type',
                        'type' => FieldType::FIELD_TYPE_DROPDOWN,
                        'data' => Album::getAlbumTypes(),
                        'label' => Module::t('album', 'Type'),
                    ],
                ],
                'model' => $model,
                'form'  => $form,
            ]) ?>
        </div>
    </div>

    <!-- Thumbnail begin -->
    <div class="row">
        <div class="col-md-4">
            <h5><?php echo Module::t('main', 'Thumbnail'); ?></h5>
            <div id="thumbnail-container">
                <?php if (isset($thumbnailModel) && $thumbnailModel instanceof Mediafile): ?>
                    <img src="<?php echo $thumbnailModel->getThumbUrl(Module::DEFAULT_THUMB_ALIAS) ?>">
                <?php endif; ?>
            </div>
            <?php echo FileSetter::widget(ArrayHelper::merge([
                'model' => $model,
                'attribute' => UploadModelInterface::FILE_TYPE_THUMB,
                'buttonName' => Module::t('main', 'Set thumbnail'),
                'buttonOptions' => [
                    'id' => $type . '-thumbnail-btn'
                ],
                'mediafileContainer' => '#thumbnail-container',
                'subDir' => Album::tableName()
            ], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge(['ownerAttribute' => UploadModelInterface::FILE_TYPE_THUMB], $ownerParams) : [])
            ); ?>
        </div>
    </div>
    <!-- Thumbnail end -->

    <!-- New files begin -->
    <div class="row">
        <div class="col-md-12">
            <h5><?php echo Module::t('main', 'New files'); ?></h5>
            <div id="mediafile-container-new">
            </div>
            <?php echo FileSetter::widget(ArrayHelper::merge([
                'model' => $model,
                'attribute' => $model->getFileType($type).'[]',
                'buttonName' => Module::t('main', 'Set '.$model->getFileType($type)),
                'buttonOptions' => [
                    'id' => $type . '-' . $model->getFileType($type) . '-btn'
                ],
                'mediafileContainer' => '#mediafile-container-new',
                'subDir' => Album::tableName()
            ], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge(['ownerAttribute' => $model->getFileType($type)], $ownerParams) : [])
            ); ?>
        </div>
    </div>
    <!-- New files end -->

    <!-- Existing files begin -->
    <div class="row">
        <div class="col-md-12">
            <h5><?php echo Module::t('main', 'Existing files'); ?></h5>
            <?php if (!$model->isNewRecord): ?>
                <?php $i=0; ?>
                <?php foreach ($model->getMediaFiles($model->getFileType($model->type)) as $mediafile): ?>
                    <?php $i+=1; ?>
                    <div id="mediafile-container-<?php echo $i; ?>">
                        <?php echo $mediafile->getPreview($baseUrl, $mediafile->isImage() ? ['width' => 300, 'thumbAlias' => Module::ORIGINAL_THUMB_ALIAS] : []); ?>
                    </div>
                    <?php echo FileSetter::widget(ArrayHelper::merge([
                        'model' => $model,
                        'attribute' => $model->getFileType($model->type).'[]',
                        'buttonName' => Module::t('main', 'Set '.$model->getFileType($model->type)),
                        'buttonOptions' => [
                            'id' => $model->type . '-' . $model->getFileType($model->type) . '-btn-' . $i
                        ],
                        'mediafileContainer' => '#mediafile-container-' . $i,
                        'subDir' => Album::tableName()
                    ], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge(['ownerAttribute' => $model->getFileType($model->type)], $ownerParams) : [])
                    ); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <!-- Existing files end -->

    <div class="form-group">
        <?php echo Html::submitButton(Module::t('main', 'Save'), [
            'class' => 'btn btn-success',
            'style' => 'margin-top: 15px;'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
