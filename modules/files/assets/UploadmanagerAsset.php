<?php

namespace app\modules\files\assets;

use yii\web\AssetBundle;

class UploadmanagerAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/files/assets/source';
    public $css = [
        'css/uploadmanager.css',
    ];
    public $js = [
        'js/uploadmanager.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'app\modules\files\assets\MainAsset',
    ];
}
