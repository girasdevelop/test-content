<?php

use yii\helpers\Html;
use app\modules\files\models\Mediafile;

/* @var $model Mediafile */
?>

<div class="item">
    <?php echo Html::a(Html::img(DIRECTORY_SEPARATOR.$model->getDefaultThumbUrl()) . '<span class="checked glyphicon glyphicon-ok"></span>',
        '#mediafile',
        ['data-key' => $model->id]
    ); ?>
</div>
