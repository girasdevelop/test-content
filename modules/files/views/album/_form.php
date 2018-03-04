<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\files\models\Album */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="album-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">

            <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

            <?php echo $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <?php echo $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

        </div>
    </div>

    <div class="form-group">
        <?php echo Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
