<?php

use Itstructure\AdminModule\components\MultilanguageMigration;
use app\models\Catalog;

/**
 * Handles the creation of table `catalog`.
 */
class m171230_138146_create_catalog_table extends MultilanguageMigration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createMultiLanguageTable(Catalog::tableName(),
            [
                'title' => $this->string(),
                'description' => $this->text(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropMultiLanguageTable(Catalog::tableName());
    }
}
