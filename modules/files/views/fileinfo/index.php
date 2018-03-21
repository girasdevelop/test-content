<?php

use yii\widgets\ActiveForm;
use yii\helpers\{Html, ArrayHelper};
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\assets\FilemanagerAsset;

/** @var $this yii\web\View */
/** @var $form yii\widgets\ActiveForm */
/** @var $model Mediafile */

$bundle = FilemanagerAsset::register($this);
?>

<div class="media">
    <div class="media-left">
        <a href="#">
            <?php echo Html::img(DIRECTORY_SEPARATOR.$model->getDefaultThumbUrl()) ?>
        </a>
    </div>
    <div class="media-body">
        <h4 class="media-heading"><?php echo Module::t('filemanager', 'File information') ?></h4>
        <h6><?php echo Module::t('filemanager', 'File type') ?> <span class="label label-default"><?php echo $model->type ?></span></h6>
        <h6><?php echo Module::t('filemanager', 'Created') ?> <span class="label label-default"><?php echo Yii::$app->formatter->asDatetime($model->created_at) ?></span></h6>
        <h6><?php echo Module::t('filemanager', 'Updated') ?> <span class="label label-default"><?php echo Yii::$app->formatter->asDatetime($model->updated_at) ?></span></h6>
        <h6><?php echo Module::t('filemanager', 'File size') ?> <span class="label label-default"><?php echo $model->getFileSize() ?></span></h6>
    </div>
</div>
