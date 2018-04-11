<?php
use app\modules\files\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\files\models\album\Album */
/* @var $thumbnailModel app\modules\files\models\Mediafile|null */
/* @var $ownerParams array */

$this->title = Module::t('album', 'Update album') . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('album', 'Albums'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('main', 'Update');
?>
<div class="album-update">

    <?php echo $this->render('_form', [
        'model' => $model,
        'thumbnailModel' => $thumbnailModel,
        'ownerParams' => $ownerParams,
    ]) ?>

</div>
