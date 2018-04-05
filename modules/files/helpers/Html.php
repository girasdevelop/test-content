<?php

namespace app\modules\files\helpers;

use yii\helpers\Html as BaseHtml;

class Html extends BaseHtml
{
    public static function audio(string $src, $options = [])
    {
        return static::tag(
            'audio',
            static::tag('source', '', [
                'src' => $src,
                'type' => $options['type'],
                'preload' => 'auto'
            ])
            .
            static::tag('track', '', [
                'kind' => 'subtitles'
            ]),
            [
                'controls'
            ]
        );
    }
}