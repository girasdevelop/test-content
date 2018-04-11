<?php

namespace app\modules\files\helpers;

use yii\helpers\{ArrayHelper, Html as BaseHtml};

/**
 * HTML helper.
 *
 * @package Itstructure\FilesModule\helpers
 */
class Html extends BaseHtml
{
    /**
     * Render html 5 audio tag structure.
     *
     * @param string $src
     * @param array $options
     *
     * @return string
     */
    public static function audio(string $src, $options = []): string
    {
        /** Main options */
        $mainOptions = [
            'controls' => 'controls'
        ];
        if (isset($options['main']) && is_array($options['main'])){
            $mainOptions = ArrayHelper::merge($mainOptions, $options['main']);
        }
        /*******************/

        /** Source options */
        $sourceOptions = [
            'src' => $src,
            'preload' => 'auto'
        ];
        if (isset($options['source']) && is_array($options['source'])){
            $sourceOptions = ArrayHelper::merge($sourceOptions, $options['source']);
        }
        /*******************/

        /** Track options */
        $trackOptions = [
            'kind' => 'subtitles'
        ];
        if (isset($options['track']) && is_array($options['track'])){
            $trackOptions = ArrayHelper::merge($trackOptions, $options['track']);
        }
        /*******************/

        return static::tag(
            'audio',
            static::tag('source', '', $sourceOptions) . static::tag('track', '', $trackOptions),
            $mainOptions
        );
    }

    /**
     * Render html 5 video tag structure.
     *
     * @param string $src
     * @param array $options
     *
     * @return string
     */
    public static function video(string $src, $options = []): string
    {
        /** Main options */
        $mainOptions = [
            'controls' => 'controls',
            'width' => 300,
            'height' => 240
        ];
        if (isset($options['main']) && is_array($options['main'])){
            $mainOptions = ArrayHelper::merge($mainOptions, $options['main']);
        }
        /*******************/

        /** Source options */
        $sourceOptions = [
            'src' => $src,
            'preload' => 'auto'
        ];
        if (isset($options['source']) && is_array($options['source'])){
            $sourceOptions = ArrayHelper::merge($sourceOptions, $options['source']);
        }
        /*******************/

        /** Track options */
        $trackOptions = [
            'kind' => 'subtitles'
        ];
        if (isset($options['track']) && is_array($options['track'])){
            $trackOptions = ArrayHelper::merge($trackOptions, $options['track']);
        }
        /*******************/

        return static::tag(
            'video',
            static::tag('source', '', $sourceOptions) . static::tag('track', '', $trackOptions),
            $mainOptions
        );
    }
}