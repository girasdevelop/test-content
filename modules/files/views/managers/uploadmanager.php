<?php
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Mediafile */
?>

<header id="header"><span class="glyphicon glyphicon-upload"></span> <?php echo Module::t('uploadmanager', 'Upload manager') ?></header>

<div id="uploadmanager">
    <p><?php echo Html::a('← ' . Module::t('main', 'Back to file manager'), [Module::FILE_MANAGER_SRC]) ?></p>
</div>
