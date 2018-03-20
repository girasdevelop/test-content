<?php

use yii\helpers\{Html, Url};
use app\modules\files\models\Mediafile;
use app\modules\files\assets\FilemanagerAsset;

/* @var $model Mediafile */

FilemanagerAsset::register($this);
?>

<div class="item">
    <?php echo Html::a(Html::img(DIRECTORY_SEPARATOR.$model->getDefaultThumbUrl()) . '<span class="checked glyphicon glyphicon-check"></span>',
        '#mediafile',
        ['data-key' => $key]
    ); ?>
</div>
