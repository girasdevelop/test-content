<?php

use yii\helpers\Html;
use yii\widgets\{ListView, LinkPager};
use yii\data\{ActiveDataProvider, Pagination};
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\assets\FilemanagerAsset;

/* @var $this yii\web\View */
/* @var $dataProvider ActiveDataProvider */
/* @var $model Mediafile */
/* @var $pagination Pagination */

FilemanagerAsset::register($this);
?>

<header id="header"><span class="glyphicon glyphicon-file"></span> <?php echo Module::t('filemanager', 'File manager') ?></header>

<div id="filemanager" data-url-info="<?php echo Module::FILE_INFO_SRC ?>">

    <div class="items">
        <?php echo ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_fileItem',
            'layout' => '{summary}{items}'
        ]) ?>

        <?php echo LinkPager::widget(['pagination' => $pagination]) ?>
    </div>
    <div class="redactor">
        <p><?php echo Html::a('<span class="glyphicon glyphicon-upload"></span> ' . Module::t('uploadmanager', 'Upload manager'),
                ['file/uploadmanager'], ['class' => 'btn btn-default']) ?></p>
        <div id="fileinfo">

        </div>
    </div>
</div>
