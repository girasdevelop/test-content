<?php

use yii\helpers\Html;
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\assets\FilemanagerAsset;

/** @var $this yii\web\View */
/** @var $model Mediafile */
/** @var $fileAttributeName string */

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

<div class="inputs" role="file-inputs"
     data-file-id="<?php echo $model->id ?>"
     data-save-src="<?php echo Module::LOCAL_SAVE_SRC ?>"
     data-is-image="<?php echo $model->isImage() ?>">

    <?php if ($model->isImage()): ?>
        <div class="input-group input-group-sm">
            <span class="input-group-addon" id="file-alt"><?php echo Module::t('filemanager', 'Alt') ?></span>
            <input type="text" class="form-control" placeholder="<?php echo Module::t('filemanager', 'Alt') ?>"
                   aria-describedby="file-alt" name="alt" role="file-alt" value="<?php echo $model->alt ?>">
        </div>
    <?php endif; ?>

    <div class="input-group input-group-sm">
        <span class="input-group-addon" id="file-description"><?php echo Module::t('filemanager', 'Description') ?></span>
        <textarea style="height: 100px;" class="form-control" placeholder="<?php echo Module::t('filemanager', 'Description') ?>"
               aria-describedby="file-description" name="description" role="file-description"><?php echo $model->description ?></textarea>
    </div>

    <div class="input-group input-group-sm">
        <span class="input-group-addon" id="file-new"><?php echo Module::t('filemanager', 'New file') ?></span>
        <input type="file" class="form-control" placeholder="<?php echo Module::t('filemanager', 'New file') ?>"
               aria-describedby="file-new" name="<?php echo $fileAttributeName ?>" role="file-new" multiple>
    </div>

    <?php echo Html::button(Module::t('main', 'Update'), ['role' => 'update', 'class' => 'btn btn-warning btn-sm']) ?>

    <?php echo Html::button(Module::t('main', 'Insert'), ['role' => 'insert', 'id' => 'insert-btn', 'class' => 'btn btn-success btn-sm']) ?>

    <?php echo Html::button(Module::t('main', 'Delete'), ['role' => 'delete', 'id' => 'delete-btn', 'class' => 'btn btn-danger btn-sm']) ?>

</div>


