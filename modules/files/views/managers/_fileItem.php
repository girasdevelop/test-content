<?php
use app\modules\files\models\Mediafile;

/* @var $model Mediafile */
/* @var $baseUrl string */
?>

<?php echo $model->getPreview($baseUrl) . '<span class="checked glyphicon glyphicon-ok"></span>'; ?>
<?php if ($model->isAudio()): ?>
    <?php echo $model->title; ?>
<?php endif; ?>
