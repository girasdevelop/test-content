<?php

use yii\helpers\{Html, ArrayHelper};
use yii\widgets\ActiveForm;
use Itstructure\FieldWidgets\{Fields, FieldType};
use Itstructure\AdminModule\models\Language;
use Itstructure\MFUploader\Module as MFUModule;
use Itstructure\MFUploader\models\album\Album;

/* @var $this Itstructure\AdminModule\components\AdminView */
/* @var $model app\models\Catalog|Itstructure\AdminModule\models\MultilanguageValidateModel */
/* @var $form yii\widgets\ActiveForm */
/* @var $albums Album[] */
/* @var $ownerParams array */
?>

<div class="catalog-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-12">

            <?php echo Fields::widget([
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => FieldType::FIELD_TYPE_TEXT,
                    ],
                    [
                        'name' => 'description',
                        'type' => FieldType::FIELD_TYPE_CKEDITOR_ADMIN,
                        'preset' => 'full',
                        'options' => [
                            'filebrowserBrowseUrl' => '/ckfinder/ckfinder.html',
                            //'filebrowserImageBrowseUrl' => '/ckfinder/ckfinder.html?type=Images',
                            'filebrowserUploadUrl' => '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
                            'filebrowserImageUploadUrl' => '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
                            'filebrowserWindowWidth' => '1000',
                            'filebrowserWindowHeight' => '700',
                        ]
                    ],
                ],
                'model'         => $model,
                'form'          => $form,
                'languageModel' => new Language()
            ]) ?>

            <!-- Thumbnail begin -->
            <?php echo $this->render('_thumbnail', [
                'model' => $model,
                'ownerParams' => isset($ownerParams) && is_array($ownerParams) ? $ownerParams : null,
            ]) ?>
            <!-- Thumbnail end -->

            <?php echo $form->field($model, 'albums')->checkboxList(
                ArrayHelper::map($albums, 'id', 'title'),
                [
                    'separator' => '<br />',
                ]
            )->label(MFUModule::t('album', 'Albums')); ?>

        </div>
    </div>

    <div class="form-group">
        <?php echo Html::submitButton($model->mainModel->isNewRecord ? 'Create' : 'Update',
            [
                'class' => $model->mainModel->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
