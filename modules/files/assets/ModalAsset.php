<?php

namespace app\modules\files\assets;

use yii\web\AssetBundle;

class ModalAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/files/assets/source';

    public $css = [
        'css/modal.css',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
