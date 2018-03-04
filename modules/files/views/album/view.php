<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\files\Module;
use app\modules\files\models\Album;

/* @var $this yii\web\View */
/* @var $model app\modules\files\models\Album */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('album', 'Albums'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="album-view">

    <p>
        <?php echo Html::a(Module::t('main', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Module::t('main', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Module::t('main', 'Are you sure you want to do this action?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'id',
                'label' => Module::t('main', 'ID')
            ],
            [
                'attribute' => 'title',
                'label' => Module::t('album', 'Title')
            ],
            [
                'attribute' => 'description',
                'label' => Module::t('album', 'Description')
            ],
            [
                'attribute' => 'type',
                'label' => Module::t('album', 'Type'),
                'value' => function($data) {
                    return Album::getTypes($data->type);
                }
            ],
            [
                'attribute' => 'created_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
                'label' => Module::t('main', 'Created date')
            ],
            [
                'attribute' => 'updated_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
                'label' => Module::t('main', 'Updated date')
            ],
        ],
    ]) ?>

</div>
