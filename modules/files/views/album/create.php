<?php
use app\modules\files\Module;
use app\modules\files\models\album\Album;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $albumType string */

$this->title = Module::t('album', 'Create '.$model->getFileType($albumType).' album');
$this->params['breadcrumbs'][] = [
    'label' => Module::t('album', ucfirst($model->getFileType($albumType)).' albums'),
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="album-create">

    <?php echo $this->render('_form', [
        'model' => $model,
        'albumType' => $albumType,
    ]) ?>

</div>
