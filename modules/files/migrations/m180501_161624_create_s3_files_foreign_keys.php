<?php

use yii\db\Migration;

/**
 * Class m180501_161624_create_s3_files_foreign_keys
 */
class m180501_161624_create_s3_files_foreign_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-s3_files_options-mediafileId',
            's3_files_options',
            'mediafileId'
        );

        $this->addForeignKey(
            'fk-s3_files_options-mediafileId',
            's3_files_options',
            'mediafileId',
            'mediafiles',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-s3_files_options-mediafileId',
            's3_files_options'
        );

        $this->dropIndex(
            'idx-s3_files_options-mediafileId',
            's3_files_options'
        );
    }
}
