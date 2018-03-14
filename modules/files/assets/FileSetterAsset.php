<?php

namespace app\modules\files\assets;

use yii\web\AssetBundle;

class FileSetterAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/files/assets/source';

    public $js = [
        'js/filesetter.js',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'pendalf89\filemanager\assets\ModalAsset',
    ];
}
