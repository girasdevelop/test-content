<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\files\Module;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\files\models\AlbumSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Albums';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="album-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Create Album', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'title',
                'label' =>  Module::t('album', 'Title'),
            ],
            [
                'attribute' => 'description',
                'label' =>  Module::t('album', 'Description'),
            ],
            [
                'attribute' => 'type',
                'label' =>  Module::t('album', 'Type'),
            ],
            [
                'attribute' => 'created_at',
                'label' => Module::t('main', 'Created date'),
                'format' =>  ['date', 'dd.MM.YY HH:mm:ss'],
            ],
            [
                'attribute' => 'updated_at',
                'label' => Module::t('main', 'Updated date'),
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Module::t('main', 'Actions'),
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]); ?>
</div>
