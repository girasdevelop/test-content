<?php

namespace app\modules\files\helpers;

use yii\helpers\Html as BaseHtml;

class Html extends BaseHtml
{
    public static function audio($options = [])
    {
        return static::tag('audio', '', $options);
    }
}