<?php

namespace models;

use app\fixtures\Catalog as CatalogFixture;

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

    protected function _after()
    {
    }

    // tests
    public function testSomeFeature()
    {

    }
}