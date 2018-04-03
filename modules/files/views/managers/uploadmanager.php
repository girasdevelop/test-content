<?php
use yii\helpers\Html;
use app\modules\files\Module;
use app\modules\files\assets\UploadmanagerAsset;

/* @var $this yii\web\View */
/* @var $manager string */
/* @var $fileAttributeName string */
/* @var $thumbStubUrls array */

$this->params['bundle'] = UploadmanagerAsset::register($this);
$this->params['manager'] = $manager;
?>

<script type="html/tpl" id="file-template">
<tr>
    <td>
        <table width="100%" role="file-block">
            <tbody>
            <tr>
                <td width="128">
                    {preview}
                </td>
                <td width="10">
                </td>
                <td>
                    {size}
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">
                            <span class="sr-only">100% Complete</span>
                        </div>
                    </div>
                    {title}
                </td>
                <td width="10">
                </td>
                <td width="200">
                    <?php echo Html::button('<span class="glyphicon glyphicon-upload"></span> ' . Module::t('uploadmanager', 'Upload'), [
                        'class' => 'btn btn-primary btn-sm',
                        'id' => 'upload-button-{fileNumber}',
                        'role' => 'upload-file',
                        'data-file-number' => '{fileNumber}'
                    ]) ?>
                    <?php echo Html::button('<span class="glyphicon glyphicon-ban-circle"></span> ' . Module::t('uploadmanager', 'Cancel'), [
                        'class' => 'btn btn-info btn-sm',
                        'id' => 'cancel-button-{fileNumber}',
                        'role' => 'cancel-upload',
                        'data-number' => '{fileNumber}'
                    ]) ?>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon" id="file-title"><?php echo Module::t('filemanager', 'Title') ?></span>
                        <input type="text" class="form-control" placeholder="<?php echo Module::t('filemanager', 'Title') ?>"
                               aria-describedby="file-title" name="title" role="file-title">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon" id="file-description"><?php echo Module::t('filemanager', 'Description') ?></span>
                        <textarea class="form-control" placeholder="<?php echo Module::t('filemanager', 'Description') ?>"
                                  aria-describedby="file-description" name="description" role="file-description"></textarea>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
</script>

<div id="uploadmanager" role="uploadmanager"
     data-save-src="<?php echo Module::LOCAL_SAVE_SRC ?>"
     data-delete-src="<?php echo Module::DELETE_SRC ?>"
     data-confirm-message="<?php echo Module::t('main', 'Are you sure you want to do this action?') ?>"
     data-base-url="<?php echo $this->params['bundle']->baseUrl ?>"
     data-sub-dir="">

    <div id="buttons">
        <label class="btn btn-success btn-sm" for="my-file-selector">
            <input id="my-file-selector" type="file" role="add-file" style="display:none">
            <span class="glyphicon glyphicon-plus"></span> <?php echo Module::t('uploadmanager', 'Add') ?>
        </label>

        <?php echo Html::button('<span class="glyphicon glyphicon-upload"></span> ' . Module::t('uploadmanager', 'Upload'), [
            'class' => 'btn btn-primary btn-sm'
        ]) ?>

        <?php echo Html::button('<span class="glyphicon glyphicon-ban-circle"></span> ' . Module::t('uploadmanager', 'Cancel'), [
            'class' => 'btn btn-info btn-sm'
        ]) ?>

        <?php echo Html::button('<span class="glyphicon glyphicon-trash"></span> ' . Module::t('uploadmanager', 'Delete'), [
            'class' => 'btn btn-danger btn-sm'
        ]) ?>
    </div>

    <table class="table table-striped">
        <tbody id="workspace">

        </tbody>
    </table>

</div>
