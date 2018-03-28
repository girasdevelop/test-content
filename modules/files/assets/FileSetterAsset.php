<?php

namespace app\modules\files\assets;

class FileSetterAsset extends BaseAsset
{
    public $js = [
        'js/filesetter.js',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'app\modules\files\assets\ModalAsset',
        'app\modules\files\assets\MainAsset',
    ];
}
