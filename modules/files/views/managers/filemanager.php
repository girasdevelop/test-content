<?php

use yii\widgets\{ListView, LinkPager};
use yii\helpers\{Html, Url};
use yii\data\{ActiveDataProvider, Pagination};
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\assets\FilemanagerAsset;
use app\modules\files\components\FilesLinkPager;

/* @var $this yii\web\View */
/* @var $dataProvider ActiveDataProvider */
/* @var $model Mediafile */
/* @var $pagination Pagination */

FilemanagerAsset::register($this);
?>

<header id="header"><span class="glyphicon glyphicon-picture"></span> <?php echo Module::t('main', 'File manager') ?></header>

<div id="filemanager" data-url-info="<?php echo Url::to(['file/info']) ?>">

    <div class="items">
        <?php echo ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_fileItem',
            'layout' => '{summary}{items}'
        ]) ?>

        <?php echo FilesLinkPager::widget(['pagination' => $pagination]) ?>
    </div>
    <div class="dashboard">
        <p><?php echo Html::a('<span class="glyphicon glyphicon-upload"></span> ' . Module::t('main', 'Upload manager'),
                ['file/uploadmanager'], ['class' => 'btn btn-default']) ?></p>
        <div id="fileinfo">

        </div>
    </div>
</div>
