<?php

namespace models;

use app\fixtures\Catalog as CatalogFixture;
use app\models\Catalog2;

class CatalogTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        $this->tester->haveFixtures([
            'cars' => [
                'class' => CatalogFixture::class,
                'dataFile' => codecept_data_dir() . 'catalog.php'
            ]
        ]);
    }

    /**
     * Тест для добавление машины
     */
    public function testCreate()
    {
        $catalog = new Catalog2();
        $catalog->title = 'Название';
        $catalog->description = 'bmw';
        expect_that($catalog->save());
    }
}