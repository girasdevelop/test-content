<?php

namespace app\modules\files\controllers;

use yii\data\{ActiveDataProvider, Pagination};
use yii\web\Controller;
use app\modules\files\Module;
use app\modules\files\models\{OwnersMediafiles, Mediafile};
use app\modules\files\traits\BehaviorsTrait;

/**
 * Class ManagersController
 * Managers controller class to display the next managers:
 * 1. To view and select available files.
 * 2. To upload files.
 *
 * @property Module $module
 *
 * @package Itstructure\FilesModule\controllers\api
 */
class ManagersController extends Controller
{
    use BehaviorsTrait;

    /**
     * Initialize.
     */
    public function init()
    {
        $this->layout               = '@app/modules/files/views/layouts/main';
        $this->viewPath             = '@app/modules/files/views/managers';
        $this->enableCsrfValidation = $this->module->enableCsrfValidation;
        $this->authenticator        = $this->module->authenticator;
        $this->rateLimiter          = $this->module->rateLimiter;

        parent::init();
    }

    public function actionFilemanager()
    {var_dump(\Yii::$app->request->get('owner'));
        $owner = \Yii::$app->request->get('owner');
        $ownerId = \Yii::$app->request->get('ownerId');

        if (null !== $owner && null !== $ownerId) {
            $ownerAttribute = \Yii::$app->request->get('ownerAttribute');
            $query = OwnersMediafiles::getMediaFilesQuery($owner, $ownerId, $ownerAttribute);
        } else {
            $query = Mediafile::find();
        }

        $pagination = new Pagination([
            'defaultPageSize' => 15,
            'totalCount' => $query->count(),
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);

        return $this->render('filemanager', [
            'dataProvider' => $dataProvider,
            'pagination' => $pagination
        ]);
    }

    public function actionUploadmanager()
    {
        //return $this->render('uploadmanager', ['model' => new Mediafile()]);
    }
}
