<?php

use app\modules\files\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\files\models\Album */

$this->title = Module::t('album', 'Update Album') . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('album', 'Albums'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="album-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
