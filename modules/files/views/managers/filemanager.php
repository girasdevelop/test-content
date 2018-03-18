<?php

use yii\widgets\ListView;
use yii\helpers\{Html, Url};
use app\modules\files\Module;
use app\modules\files\assets\FilemanagerAsset;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
FilemanagerAsset::register($this);
?>

<header id="header"><span class="glyphicon glyphicon-picture"></span> <?php echo Module::t('main', 'File manager') ?></header>

<div id="filemanager" data-url-info="<?php echo Url::to(['file/info']) ?>">

    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
                    return Html::a(
                        Html::img($model->getDefaultThumbUrl())
                        . '<span class="checked glyphicon glyphicon-check"></span>',
                        '#mediafile',
                        ['data-key' => $key]
                    );
            },
    ]) ?>

    <div class="dashboard">
        <p><?php echo Html::a('<span class="glyphicon glyphicon-upload"></span> ' . Module::t('main', 'Upload manager'),
                ['file/uploadmanager'], ['class' => 'btn btn-default']) ?></p>
        <div id="fileinfo">

        </div>
    </div>
</div>
