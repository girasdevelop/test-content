<?php
use yii\widgets\{ListView, LinkPager};
use yii\data\{ActiveDataProvider, Pagination};
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\assets\FilemanagerAsset;

/* @var $this yii\web\View */
/* @var $dataProvider ActiveDataProvider */
/* @var $model Mediafile */
/* @var $pagination Pagination */
/* @var $manager string */

FilemanagerAsset::register($this);
$this->params['manager'] = $manager;
?>

<div id="filemanager" role="filemanager"
     data-url-info="<?php echo Module::FILE_INFO_SRC ?>">

    <div class="items">
        <?php echo ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_fileItem',
            'layout' => '{summary}{items}'
        ]) ?>

        <?php echo LinkPager::widget(['pagination' => $pagination]) ?>
    </div>
    <div class="redactor">
        <div id="fileinfo" role="fileinfo">

        </div>
    </div>
</div>
