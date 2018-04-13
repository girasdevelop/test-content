<?php
use yii\helpers\{ArrayHelper, Html};
use app\modules\files\Module;
use app\modules\files\models\album\{Album};
use app\modules\files\widgets\FileSetter;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $albumType string */
/* @var $fileType string */
/* @var $ownerParams array */
/* @var $number int */
?>


<div id="mediafile-container-new<?php if (isset($number)): ?>-n<?php echo $number; ?><?php endif; ?>">
</div>
<?php echo FileSetter::widget(ArrayHelper::merge([
    'model' => $model,
    'attribute' => $fileType.'[]',
    'buttonName' => Module::t('main', 'Set '.$fileType),
    'options' => [
        'id' => Html::getInputId($model, $fileType) . (isset($number) ? '-n' . $number : '')
    ],
    'buttonOptions' => [
        'id' => $albumType . '-' . $fileType . '-btn' . (isset($number) ? '-n' . $number : '')
    ],
    'mediafileContainer' => '#mediafile-container-new' . (isset($number) ? '-n' . $number : ''),
    'subDir' => Album::tableName()
], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge(['ownerAttribute' => $fileType], $ownerParams) : [])
); ?>
