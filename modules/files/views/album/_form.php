<?php
use yii\widgets\ActiveForm;
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\models\album\{Album};
use app\modules\files\helpers\Html;
use Itstructure\FieldWidgets\{Fields, FieldType};

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $albumType string */
/* @var $fileType string */
/* @var $form yii\widgets\ActiveForm */
/* @var $thumbnailModel Mediafile|null */
/* @var $ownerParams array */
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
    <?php echo $this->render('_thumbnail', [
        'model' => $model,
        'thumbnailModel' => isset($thumbnailModel) && $thumbnailModel instanceof Mediafile ? $thumbnailModel : null,
        'albumType' => $albumType,
        'ownerParams' => isset($ownerParams) && is_array($ownerParams) ? $ownerParams : null,
    ]) ?>
    <!-- Thumbnail end -->

    <!-- New files begin -->
    <?php echo $this->render('_new-mediafiles', [
        'model' => $model,
        'albumType' => $albumType,
        'fileType' => $fileType,
        'ownerParams' => isset($ownerParams) && is_array($ownerParams) ? $ownerParams : null,
    ]) ?>
    <!-- New files end -->

    <!-- Existing files begin -->
    <?php echo $this->render('_existing-mediafiles', [
        'model' => $model,
        'albumType' => $albumType,
        'fileType' => $fileType,
        'ownerParams' => isset($ownerParams) && is_array($ownerParams) ? $ownerParams : null,
    ]) ?>
    <!-- Existing files end -->

    <div class="form-group">
        <?php echo Html::submitButton(Module::t('main', 'Save'), [
            'class' => 'btn btn-success',
            'style' => 'margin-top: 15px;'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
