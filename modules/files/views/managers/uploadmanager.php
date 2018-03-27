<?php
use yii\helpers\Html;
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\assets\UploadmanagerAsset;

/* @var $this yii\web\View */
/* @var $model Mediafile */
/* @var $manager string */
/* @var $fileAttributeName string */
/* @var $fileTypes array */

UploadmanagerAsset::register($this);
$this->params['manager'] = $manager;
?>

<script type="html/tpl" id="file-template">
<tr>
    <td>
        <table>
            <tbody>
                <tr>
                    <td>
                        <img width="75" src="{src}">
                    </td>
                    <td>
                        {title}
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
     data-file-types="<?php echo $fileTypes ?>">

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
