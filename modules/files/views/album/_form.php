<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\files\Module;
use app\modules\files\models\Album;
use app\modules\files\widgets\FileSetter;
use app\modules\files\interfaces\UploadModelInterface;
use Itstructure\FieldWidgets\{Fields, FieldType};

/* @var $this yii\web\View */
/* @var $model app\modules\files\models\Album */
/* @var $form yii\widgets\ActiveForm */
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

            <div id="thumbnail-container"></div>

            <?php echo FileSetter::widget([
                'name' => UploadModelInterface::FILE_TYPE_IMAGE,
                'thumb' => 'original',
                'template' => '<div class="input-group">{input}<span class="btn-group">{button}{reset-button}</span></div>',
                'insertedData' => FileSetter::INSERTED_DATA_ID,
                'buttonName' => Module::t('main', 'Set thumbnail'),
                'imageContainer' => '#thumbnail-container',
                /*'owner' => 'post',
                'ownerId' => 1,
                'ownerAttribute' => UploadModelInterface::FILE_TYPE_IMAGE*/
                'subDir' => 'post'
            ]); ?>

        </div>
    </div>

    <div class="form-group">
        <?php echo Html::submitButton(Module::t('main', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
