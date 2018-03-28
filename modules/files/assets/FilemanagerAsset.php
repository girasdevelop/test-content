<?php

namespace app\modules\files\assets;

class FilemanagerAsset extends BaseAsset
{
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
