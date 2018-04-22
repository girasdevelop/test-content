<?php

namespace app\controllers\admin;

use app\models\{Catalog, CatalogSearch};
use app\modules\files\models\album\Album;
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
     * Set other additionFields for actions.
     *
     * @param $action
     *
     * @return mixed
     */
    public function beforeAction($action)
    {
        if ($this->action->id == 'create' || $this->action->id == 'update'){
            $this->additionFields['albums'] = Album::find()->select([
                'id', 'title'
            ])->all();
        }

        return parent::beforeAction($action);
    }

    /**
     * Run action to set ownerParams.
     * @param string $id
     * @param array $params
     * @return mixed
     */
    public function runAction($id, $params = [])
    {
        if ($id == 'update'){
            $this->setModelByConditions($params['id']);
            $this->additionFields['ownerParams'] = [
                'owner' => Catalog::tableName(),
                'ownerId' => $this->model->getId(),
            ];
        }

        return parent::runAction($id, $params);
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
