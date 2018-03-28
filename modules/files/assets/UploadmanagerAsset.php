<?php

namespace app\modules\files\assets;

class UploadmanagerAsset extends BaseAsset
{
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
