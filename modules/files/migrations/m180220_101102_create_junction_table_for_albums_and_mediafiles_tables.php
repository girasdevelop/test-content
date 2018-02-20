<?php

use yii\db\Migration;

/**
 * Handles the creation of table `albums_mediafiles`.
 * Has foreign keys to the tables:
 *
 * - `albums`
 * - `mediafiles`
 */
class m180220_101102_create_junction_table_for_albums_and_mediafiles_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('albums_mediafiles', [
            'albumId' => $this->integer(),
            'mediafileId' => $this->integer(),
            'PRIMARY KEY(albumId, mediafileId)',
        ]);

        // creates index for column `albumId`
        $this->createIndex(
            'idx-albums_mediafiles-albumId',
            'albums_mediafiles',
            'albumId'
        );

        // add foreign key for table `albums`
        $this->addForeignKey(
            'fk-albums_mediafiles-albumId',
            'albums_mediafiles',
            'albumId',
            'albums',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // creates index for column `mediafileId`
        $this->createIndex(
            'idx-albums_mediafiles-mediafileId',
            'albums_mediafiles',
            'mediafileId'
        );

        // add foreign key for table `mediafiles`
        $this->addForeignKey(
            'fk-albums_mediafiles-mediafileId',
            'albums_mediafiles',
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
        // drops foreign key for table `albums`
        $this->dropForeignKey(
            'fk-albums_mediafiles-albumId',
            'albums_mediafiles'
        );

        // drops index for column `albumId`
        $this->dropIndex(
            'idx-albums_mediafiles-albumId',
            'albums_mediafiles'
        );

        // drops foreign key for table `mediafiles`
        $this->dropForeignKey(
            'fk-albums_mediafiles-mediafileId',
            'albums_mediafiles'
        );

        // drops index for column `mediafileId`
        $this->dropIndex(
            'idx-albums_mediafiles-mediafileId',
            'albums_mediafiles'
        );

        $this->dropTable('albums_mediafiles');
    }
}
