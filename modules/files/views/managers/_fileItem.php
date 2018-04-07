<?php
use app\modules\files\models\Mediafile;

/* @var $model Mediafile */
/* @var $baseUrl string */
?>

<?php echo $model->getPreview($baseUrl, $model->isImage() ? ['width' => 150] : ['width' => 50]) . '<span class="checked glyphicon glyphicon-ok"></span>'; ?>
<?php if ($model->isAudio() || $model->isText() || $model->isApp()): ?>
    <?php echo $model->title; ?>
<?php endif; ?>
