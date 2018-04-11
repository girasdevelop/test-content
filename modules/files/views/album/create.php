<?php
use app\modules\files\Module;
use app\modules\files\models\album\Album;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $type string */

$this->title = Module::t('album', 'Create album');
$this->params['breadcrumbs'][] = ['label' => Module::t('album', 'Albums'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="album-create">

    <?php echo $this->render('_form', [
        'model' => $model,
        'type' => $type
    ]) ?>

</div>
