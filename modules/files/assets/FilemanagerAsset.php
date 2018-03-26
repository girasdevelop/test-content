<?php

namespace app\modules\files\assets;

use yii\web\AssetBundle;

class FilemanagerAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/files/assets/source';
    public $css = [
        'css/filemanager.css',
    ];
    public $js = [
        'js/filemanager.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'app\modules\files\assets\MainAsset',
    ];
}
