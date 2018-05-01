<?php

use yii\db\Migration;

/**
 * Class m180501_161144_create_s3_files_options
 */
class m180501_161144_create_s3_files_options extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('s3_files_options', [
            'mediafileId' => $this->integer()->notNull(),
            'bucket' => $this->string()->notNull(),
            'prefix' => $this->string()->notNull(),
            'PRIMARY KEY (`mediafileId`, `bucket`, `key`, `region`)',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('s3_files_options');
    }
}
