<?php
use yii\data\Pagination;
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\models\album\Album;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $mediafiles Mediafile[] */
/* @var $pages Pagination */
/* @var $albumType string */
/* @var $fileType string */
/* @var $thumbnailModel Mediafile|null */
/* @var $ownerParams array */

$this->title = Module::t('album', 'Update album') . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('album', 'Albums'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('main', 'Update');
?>
<div class="album-update">

    <?php echo $this->render('_form', [
        'model' => $model,
        'mediafiles' => $mediafiles,
        'pages' => $pages,
        'albumType' => $albumType,
        'fileType' => $fileType,
        'thumbnailModel' => $thumbnailModel,
        'ownerParams' => $ownerParams,
    ]) ?>

</div>
