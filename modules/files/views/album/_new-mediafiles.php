<?php
use yii\helpers\ArrayHelper;
use app\modules\files\Module;
use app\modules\files\models\album\{Album};
use app\modules\files\widgets\FileSetter;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $albumType string */
/* @var $fileType string */
/* @var $ownerParams array */
?>

<div class="row">
    <div class="col-md-12">
        <h5><?php echo Module::t('main', 'New files'); ?></h5>
        <div id="mediafile-container-new">
        </div>
        <?php echo FileSetter::widget(ArrayHelper::merge([
            'model' => $model,
            'attribute' => $fileType.'[]',
            'buttonName' => Module::t('main', 'Set '.$fileType),
            'buttonOptions' => [
                'id' => $albumType . '-' . $fileType . '-btn'
            ],
            'mediafileContainer' => '#mediafile-container-new',
            'subDir' => Album::tableName()
        ], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge(['ownerAttribute' => $fileType], $ownerParams) : [])
        ); ?>
    </div>
</div>
