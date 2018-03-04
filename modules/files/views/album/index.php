<?php

use yii\helpers\{Html, Url};
use yii\grid\GridView;
use app\modules\files\Module;
use app\modules\files\models\Album;

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
            [
                'label' => Module::t('main', 'ID'),
                'value' => function($data) {
                    return Html::a(
                        Html::encode($data->id),
                        Url::to(['view', 'id' => $data->id])
                    );
                },
                'format' => 'raw',
            ],
            [
                'label' => Module::t('album', 'Title'),
                'value' => function($data) {
                    return Html::a(
                        Html::encode($data->title),
                        Url::to(['view', 'id' => $data->id])
                    );
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'description',
                'label' =>  Module::t('album', 'Description'),
            ],
            [
                'attribute' => 'type',
                'label' =>  Module::t('album', 'Type'),
                'value' => function($data) {
                    return Album::getTypes($data->type);
                }
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
