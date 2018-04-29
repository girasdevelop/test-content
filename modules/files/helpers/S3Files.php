<?php

namespace app\modules\files\helpers;

use yii\base\InvalidArgumentException;

/**
 * S3Files helper.
 *
 * @package Itstructure\FilesModule\helpers
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class S3Files
{
    const BUCKET_DIR_SEPARATOR = '/';

    /**
     * Removes a directory with all its content recursively.
     * @param string $dir
     */
    public static function removeDirectory(string $dir)
    {
        $dir = rtrim($dir, self::BUCKET_DIR_SEPARATOR);

        if (!is_dir($dir)) {
            return;
        }
        if (!($handle = opendir($dir))) {
            return;
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . self::BUCKET_DIR_SEPARATOR . $file;
            if (is_dir($path)) {
                static::removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        closedir($handle);
        rmdir($dir);
    }

    /**
     * Returns the directories found.
     * @param string $dir
     * @param bool $recursive
     * @return array
     */
    public static function findDirectories(string $dir, $recursive = false): array
    {
        $dir = rtrim($dir, self::BUCKET_DIR_SEPARATOR);

        $list = [];

        $handle = opendir($dir);
        if ($handle === false) {
            throw new InvalidArgumentException("Unable to open directory: " . $dir);
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . self::BUCKET_DIR_SEPARATOR . $file;
            if (is_dir($path)) {
                $list[] = $path;
                if ($recursive) {
                    $list = array_merge($list, static::findDirectories($path, $recursive));
                }
            }
        }
        closedir($handle);

        return $list;
    }
}