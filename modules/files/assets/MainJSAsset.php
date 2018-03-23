<?php

namespace app\modules\files\assets;

use yii\web\{AssetBundle};

class MainJSAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/files/assets/source';
    public $js = [
        'js/mainjs.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
    ];
}
