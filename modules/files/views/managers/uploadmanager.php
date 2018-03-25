<?php
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\assets\UploadmanagerAsset;

/* @var $this yii\web\View */
/* @var $model Mediafile */
/* @var $manager string */

UploadmanagerAsset::register($this);
$this->params['manager'] = $manager;
?>


