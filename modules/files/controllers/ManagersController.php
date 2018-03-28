<?php

namespace app\modules\files\controllers;

use yii\data\{ActiveDataProvider, Pagination};
use yii\web\Controller;
use yii\helpers\BaseUrl;
use app\modules\files\Module;
use app\modules\files\assets\FilemanagerAsset;
use app\modules\files\interfaces\UploadModelInterface;
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
        $this->authenticator        = $this->module->authenticator;
        $this->rateLimiter          = $this->module->rateLimiter;

        parent::init();
    }

    /**
     * @return string
     */
    public function actionFilemanager()
    {
        $request = \Yii::$app->request;

        if (null !== $request->get('owner') && null !== $request->get('ownerId')) {
            $query = OwnersMediafiles::getMediaFilesQuery($request->get('owner'), (int)$request->get('ownerId'), $request->get('ownerAttribute'));
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

        BaseUrl::remember($request->getAbsoluteUrl(), Module::BACK_URL_PARAM);

        return $this->render('filemanager', [
            'dataProvider' => $dataProvider,
            'pagination' => $pagination,
            'manager' => 'filemanager',
        ]);
    }

    /**
     * @return string
     */
    public function actionUploadmanager()
    {
        return $this->render('uploadmanager', [
            'manager' => 'uploadmanager',
            'fileAttributeName' => $this->module->fileAttributeName,
            'fileTypes' => '[
                UploadModelInterface::FILE_TYPE_IMAGE,
                UploadModelInterface::FILE_TYPE_AUDIO,
                UploadModelInterface::FILE_TYPE_VIDEO,
                UploadModelInterface::FILE_TYPE_TEXT,
                UploadModelInterface::FILE_TYPE_APP,
            ]'
        ]);
    }
}
