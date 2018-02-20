<?php

use yii\db\Migration;

/**
 * Class m180220_101844_add_owners_foreign_keys
 */
class m180220_101844_add_owners_foreign_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-owners-mediafileId',
            'owners',
            'mediafileId'
        );

        $this->addForeignKey(
            'fk-owners-mediafileId',
            'owners',
            'mediafileId',
            'mediafiles',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx-owners-albumId',
            'owners',
            'albumId'
        );

        $this->addForeignKey(
            'fk-owners-albumId',
            'owners',
            'albumId',
            'albums',
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
            'fk-owners-mediafileId',
            'owners'
        );

        $this->dropIndex(
            'idx-owners-mediafileId',
            'owners'
        );

        $this->dropForeignKey(
            'fk-owners-albumId',
            'owners'
        );

        $this->dropIndex(
            'idx-owners-albumId',
            'owners'
        );
    }
}
