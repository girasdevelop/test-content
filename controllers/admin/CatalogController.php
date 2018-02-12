<?php

namespace app\controllers\admin;

use app\models\{Catalog, CatalogSearch};
use Itstructure\AdminModule\controllers\CommonAdminController;

/**
 * CatalogController implements the CRUD actions for Catalog model.
 */
class CatalogController extends CommonAdminController
{
    /**
     * Initialize.
     */
    public function init()
    {
        $this->isMultilanguage = true;

        parent::init();
    }

    /**
     * Returns Catalog model name.
     *
     * @return string
     */
    protected function getModelName():string
    {
        return Catalog::class;
    }

    /**
     * Returns CatalogSearch model name.
     *
     * @return string
     */
    protected function getSearchModelName():string
    {
        return CatalogSearch::class;
    }
}
