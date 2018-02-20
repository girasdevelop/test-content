<?php

use yii\db\Migration;

/**
 * Handles the creation of table `owners`.
 */
class m180220_082806_create_owners_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('owners', [
            'mediafileId' => $this->integer(),
            'albumId' => $this->integer(),
            'ownerId' => $this->integer()->notNull(),
            'owner' => $this->string()->notNull(),
            'propertyType' => $this->string()->notNull(),
            'PRIMARY KEY (`mediafileId`, `albumId`, `ownerId`, `owner`, `propertyType`)',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('owners');
    }
}
