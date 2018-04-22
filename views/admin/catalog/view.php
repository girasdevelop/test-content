<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use Itstructure\FieldWidgets\TableMultilanguage;
use Itstructure\AdminModule\models\Language;
use app\modules\files\Module as FilesModule;

/* @var $this yii\web\View */
/* @var $model app\models\Catalog */

$this->title = $model->getDefaultTranslate('title');
$this->params['breadcrumbs'][] = ['label' => 'Catalogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalog-view">

    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['catalog/delete'], [
            'class' => 'btn btn-danger',
            'data'=>[
                'method' => 'get',
                'confirm' => 'Are you sure you want to do this action?',
                'params'=>['id'=>$model->id],
            ]
        ]) ?>
    </p>

    <h3>Translate</h3>
    <?php echo TableMultilanguage::widget([
        'fields' => [
            [
                'name' => 'title',
                'label' => 'Title',
            ],
            [
                'name' => 'description',
                'label' => 'Description',
            ],
        ],
        'model'         => $model,
        'languageModel' => new Language(),
    ]) ?>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'created_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            [
                'attribute' => 'updated_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
        ],
    ]) ?>

    <?php if (($defaultThumbImage = $model->getDefaultThumbImage()) !== null): ?>
        <div class="row">
            <div class="col-md-4">
                <h5><?php echo FilesModule::t('main', 'Thumbnail'); ?></h5>
                <?php echo $defaultThumbImage ?>
            </div>
        </div>
    <?php endif; ?>

</div>
