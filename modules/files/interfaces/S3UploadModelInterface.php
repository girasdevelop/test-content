<?php

namespace app\modules\files\interfaces;

use Aws\S3\S3ClientInterface;

/**
 * Interface S3UploadModelInterface
 *
 * @package Itstructure\FilesModule\interfaces
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
interface S3UploadModelInterface extends UploadModelInterface
{
    /**
     * Set s3 client.
     * @param S3ClientInterface $s3Client
     */
    public function setS3Client(S3ClientInterface $s3Client): void;

    /**
     * Get s3 client.
     * @return S3ClientInterface|null
     */
    public function getS3Client();
}
