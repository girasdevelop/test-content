<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\files\Module;
use app\modules\files\models\Album;
use Itstructure\FieldWidgets\{Fields, FieldType};

/* @var $this yii\web\View */
/* @var $model app\modules\files\models\Album */
/* @var $form yii\widgets\ActiveForm */
?>

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

        </div>
    </div>

    <div class="form-group">
        <?php echo Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
