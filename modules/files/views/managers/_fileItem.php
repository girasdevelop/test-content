<?php

use yii\helpers\Html;
use app\modules\files\models\Mediafile;

/* @var $model Mediafile */
/* @var $baseUrl string */
?>

<div class="item" role="item" data-key="<?php echo $model->id ?>">
    <?php echo $model->getPreview($baseUrl) . '<span class="checked glyphicon glyphicon-ok"></span>'; ?>
    <?php if ($model->isAudio()): ?>
        <?php echo $model->title; ?>
    <?php endif; ?>
</div>
