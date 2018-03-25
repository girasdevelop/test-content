<?php

namespace app\modules\files\assets;

use yii\web\{AssetBundle};

class MainAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/files/assets/source';
    public $css = [
        'css/main.css',
    ];
    public $js = [
        'js/main.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
    ];
}
