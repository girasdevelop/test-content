<?php

namespace app\modules\files\assets;

class ModalAsset extends BaseAsset
{
    public $css = [
        'css/modal.css',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
