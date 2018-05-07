<?php

namespace app\controllers\admin;

use app\models\{Catalog, CatalogSearch};
use Itstructure\MFUploader\models\album\Album;
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
     * Returns addition fields.
     * @return array
     */
    protected function getAdditionFields(): array
    {
        $additionFields = [];

        if ($this->action->id == 'create' || $this->action->id == 'update'){
            $additionFields['albums'] = Album::find()->select([
                'id', 'title'
            ])->all();
        }

        return $additionFields;
    }

    /**
     * Returns Catalog model name.
     * @return string
     */
    protected function getModelName():string
    {
        return Catalog::class;
    }

    /**
     * Returns CatalogSearch model name.
     * @return string
     */
    protected function getSearchModelName():string
    {
        return CatalogSearch::class;
    }
}
