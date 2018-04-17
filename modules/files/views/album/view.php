<?php
use yii\data\Pagination;
use yii\widgets\LinkPager;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\files\Module;
use app\modules\files\assets\BaseAsset;
use app\modules\files\models\Mediafile;
use app\modules\files\models\album\Album;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $mediafiles Mediafile[] */
/* @var $pages Pagination */
/* @var $fileType string */

$this->title = $model->title;
$this->params['breadcrumbs'][] = [
    'label' => Module::t('album', ucfirst($fileType).' albums'),
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .file-item {
        margin-bottom: 15px;
    }
</style>

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
                    return Album::getAlbumTypes($data->type);
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

    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <?php $i=0; ?>
                <?php foreach ($mediafiles as $mediafile): ?>
                    <div class="col-md-6 file-item">
                        <?php $i+=1; ?>
                        <div class="media">
                            <div class="media-left" id="mediafile-container-<?php echo $i; ?>">
                                <?php echo $mediafile->getPreview(BaseAsset::register($this)->baseUrl, 'existing'); ?>
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">
                                    <?php echo $mediafile->title ?>
                                </h4>
                                <div>
                                    <?php echo $mediafile->description ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php echo LinkPager::widget(['pagination' => $pages]) ?>
        </div>
    </div>

</div>
