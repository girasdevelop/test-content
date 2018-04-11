<?php

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\modules\files\Module;
use app\modules\files\models\album\{Album};
use app\modules\files\helpers\Html;
use app\modules\files\widgets\FileSetter;
use app\modules\files\models\Mediafile;
use app\modules\files\interfaces\UploadModelInterface;
use Itstructure\FieldWidgets\{Fields, FieldType};

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $form yii\widgets\ActiveForm */
/* @var $thumbnailModel Mediafile|null */
/* @var $ownerParams array */
?>

<style>
    #thumbnail-container img {
        width: 300px;
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
                        'data' => Album::getTypes(),
                        'label' => Module::t('album', 'Type'),
                    ],
                ],
                'model' => $model,
                'form'  => $form,
            ]) ?>

            <div id="thumbnail-container">
                <?php if (isset($thumbnailModel) && $thumbnailModel instanceof Mediafile): ?>
                    <img src="<?php echo $thumbnailModel->getThumbUrl(Module::DEFAULT_THUMB_ALIAS) ?>">
                <?php endif; ?>
            </div>
            <?php echo FileSetter::widget(ArrayHelper::merge([
                    'model' => $model,
                    'attribute' => UploadModelInterface::FILE_TYPE_THUMB,
                    'buttonName' => Module::t('main', 'Set thumbnail'),
                    'mediafileContainer' => '#thumbnail-container',
                    'subDir' => Album::tableName()
                ], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge(['ownerAttribute' => UploadModelInterface::FILE_TYPE_THUMB], $ownerParams) : [])
            ); ?>

            <?php if (!$model->isNewRecord): ?>
                <?php foreach ($model->getMediaFiles()): ?>

                <?php endforeach; ?>
            <?php endif; ?>

            <div id="image-container">
                <?php if (isset($thumbnailModel) && $thumbnailModel instanceof Mediafile): ?>
                    <img src="<?php echo $thumbnailModel->getThumbUrl(Module::DEFAULT_THUMB_ALIAS) ?>">
                <?php endif; ?>
            </div>
            <?php echo FileSetter::widget(ArrayHelper::merge([
                'model' => $model,
                'attribute' => UploadModelInterface::FILE_TYPE_THUMB,
                'buttonName' => Module::t('main', 'Set thumbnail'),
                'mediafileContainer' => '#thumbnail-container',
                'subDir' => Album::tableName()
            ], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge(['ownerAttribute' => UploadModelInterface::FILE_TYPE_THUMB], $ownerParams) : [])
            ); ?>

            <?php echo $model::ALBUM_TYPE_IMAGE ?>

        </div>
    </div>

    <div class="form-group">
        <?php echo Html::submitButton(Module::t('main', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
