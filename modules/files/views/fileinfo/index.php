<?php

use yii\widgets\ActiveForm;
use yii\helpers\{Html, ArrayHelper};
use app\modules\files\Module;
use app\modules\files\models\Mediafile;
use app\modules\files\assets\FilemanagerAsset;

/** @var $this yii\web\View */
/** @var $form yii\widgets\ActiveForm */
/** @var $model Mediafile */

$bundle = FilemanagerAsset::register($this);
?>

<?php echo Html::img(DIRECTORY_SEPARATOR.$model->getDefaultThumbUrl()) ?>
