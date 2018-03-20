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

    const FILE_MANAGER_SRC = '/files/managers/filemanager';
    const UPLOAD_MANAGER_SRC = '/files/managers/uploadmanager';

    /**
     * Initialize.
     */
    public function init()
    {
        $this->layout               = '@app/modules/files/views/layouts/main';
        $this->enableCsrfValidation = $this->module->enableCsrfValidation;
        $this->authenticator        = $this->module->authenticator;
        $this->rateLimiter          = $this->module->rateLimiter;

        parent::init();
    }

    public function actionFilemanager()
    {
        $owner = \Yii::$app->request->get('owner');
        $ownerId = \Yii::$app->request->get('ownerId');
        $ownerAttribute = \Yii::$app->request->get('ownerAttribute');

        if (null !== $owner && null !== $ownerId) {
            $query = OwnersMediafiles::getMediaFilesQuery($owner, (int)$ownerId, $ownerAttribute);
        } else {
            $query = Mediafile::find();
        }

        $pagination = new Pagination([
            'defaultPageSize' => 2,
            'totalCount' => $query->count(),
            'params' => array_merge($_GET, [
                'owner' => $owner,
                'ownerId' => (int)$ownerId,
                'ownerAttribute' => $ownerAttribute,
            ])
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);

        return $this->renderAjax('filemanager', [
            'dataProvider' => $dataProvider,
            'pagination' => $pagination
        ]);
    }

    public function actionUploadmanager()
    {
        //return $this->render('uploadmanager', ['model' => new Mediafile()]);
    }
}
