<?php

use yii\helpers\{Url, Html};
use yii\grid\GridView;

/* @var $this Itstructure\AdminModule\components\AdminView */
/* @var $searchModel app\models\CatalogSearch|Itstructure\AdminModule\models\MultilanguageTrait */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Catalogs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalog-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Create Catalog', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        /*'tableOptions' => [
            'class' => 'table table-bordered table-hover dataTable'
        ],*/
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title' => [
                'label' => 'Name',
                'value' => function($searchModel) {
                    return Html::a(
                        Html::encode($searchModel->getDefaultTranslate('title')),
                        Url::to(['view', 'id' => $searchModel->id])
                    );
                },
                'format' => 'raw',
            ],
            'description' => [
                'label' => 'Description',
                'value' => function($searchModel) {
                    return $searchModel->getDefaultTranslate('description');
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'created_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            [
                'attribute' => 'updated_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]); ?>
</div>
