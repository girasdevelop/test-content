<?php

use yii\helpers\{Html, ArrayHelper};
use yii\widgets\ActiveForm;
use Itstructure\FieldWidgets\{Fields, FieldType};
use Itstructure\AdminModule\models\Language;
use app\modules\files\Module;
use app\modules\files\models\album\Album;
use app\modules\files\widgets\FileSetter;
use app\modules\files\interfaces\UploadModelInterface;

/* @var $this Itstructure\AdminModule\components\AdminView */
/* @var $model app\models\Catalog|Itstructure\AdminModule\models\MultilanguageValidateModel */
/* @var $form yii\widgets\ActiveForm */
/* @var $albums app\modules\files\models\album\Album[] */
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

            <?php echo $form->field($model, 'albums')->checkboxList(
                ArrayHelper::map($albums, 'id', 'title'),
                [
                    'separator' => '<br />',
                ]
            )->label(Module::t('album', 'Albums')); ?>

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
