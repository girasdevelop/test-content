<?php

use yii\helpers\Html;
use app\modules\files\models\Mediafile;

/* @var $model Mediafile */
/* @var $baseUrl string */
?>

<div class="item">
    <?php echo Html::a(Html::img($model->getDefaultThumbUrl($baseUrl)) . '<span class="checked glyphicon glyphicon-ok"></span>',
        '#mediafile',
        ['data-key' => $model->id]
    ); ?>
</div>
