<?php
use app\modules\files\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\files\models\Album */

$this->title = Module::t('album', 'Create album');
$this->params['breadcrumbs'][] = ['label' => Module::t('album', 'Albums'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="album-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
