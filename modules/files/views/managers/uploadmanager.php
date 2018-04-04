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
<?php echo $this->render('_fileBlock') ?>
</script>

<div id="uploadmanager" role="uploadmanager"
     data-save-src="<?php echo Module::LOCAL_SAVE_SRC ?>"
     data-delete-src="<?php echo Module::DELETE_SRC ?>"
     data-base-url="<?php echo $this->params['bundle']->baseUrl ?>">

    <div id="buttons">
        <label class="btn btn-success btn-sm" for="my-file-selector">
            <input id="my-file-selector" type="file" role="add-file" style="display:none">
            <span class="glyphicon glyphicon-plus"></span> <?php echo Module::t('uploadmanager', 'Add') ?>
        </label>

        <?php echo Html::button('<span class="glyphicon glyphicon-upload"></span> ' . Module::t('uploadmanager', 'Upload'), [
            'class' => 'btn btn-primary btn-sm',
            'role' => 'total-upload-file'
        ]) ?>

        <?php echo Html::button('<span class="glyphicon glyphicon-ban-circle"></span> ' . Module::t('uploadmanager', 'Cancel'), [
            'class' => 'btn btn-info btn-sm',
            'role' => 'total-cancel-upload'
        ]) ?>

        <?php echo Html::button('<span class="glyphicon glyphicon-trash"></span> ' . Module::t('uploadmanager', 'Delete'), [
            'class' => 'btn btn-danger btn-sm',
            'role' => 'total-delete-file-button'
        ]) ?>

        <?php echo Html::checkbox('', false, [
            'role' => 'total-delete-file-checkbox'
        ]) ?>
    </div>

    <table class="table table-striped">
        <tbody id="workspace">

        </tbody>
    </table>

</div>
