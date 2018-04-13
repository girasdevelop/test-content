<?php
use yii\helpers\{ArrayHelper, Html};
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\models\album\{Album};
use app\modules\files\widgets\FileSetter;
use app\modules\files\assets\FileSetterAsset;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $albumType string */
/* @var $fileType string */
/* @var $mediafile Mediafile */
/* @var $ownerParams array */
/* @var $baseUrl string */

$baseUrl = FileSetterAsset::register($this)->baseUrl;
?>

<?php $i=0; ?>
<?php foreach ($model->getMediaFiles($fileType) as $mediafile): ?>
    <?php $i+=1; ?>
    <div class="media">
        <div class="media-left" id="mediafile-container-<?php echo $i; ?>">
            <?php echo $mediafile->getPreview($baseUrl, $mediafile->isImage() ? ['width' => Module::ORIGINAL_PREVIEW_WIDTH, 'thumbAlias' => Module::ORIGINAL_THUMB_ALIAS] : []); ?>
        </div>
        <div class="media-body">
            <h4 class="media-heading"><?php echo $mediafile->title ?></h4>
            <?php echo $mediafile->description ?>
        </div>
    </div>
    <?php echo FileSetter::widget(ArrayHelper::merge([
        'model' => $model,
        'attribute' => $fileType.'[]',
        'buttonName' => Module::t('main', 'Set '.$fileType),
        'options' => [
            'id' => Html::getInputId($model, $fileType) . '-' . $i
        ],
        'buttonOptions' => [
            'id' => $albumType . '-' . $fileType . '-btn-' . $i
        ],
        'mediafileContainer' => '#mediafile-container-' . $i,
        'subDir' => Album::tableName()
    ], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge(['ownerAttribute' => $fileType], $ownerParams) : [])
    ); ?>
<?php endforeach; ?>
