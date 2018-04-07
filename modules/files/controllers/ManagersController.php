<?php

namespace app\modules\files\controllers;

use yii\helpers\BaseUrl;
use yii\data\{ActiveDataProvider, Pagination};
use yii\base\InvalidArgumentException;
use yii\web\{Controller, BadRequestHttpException};
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
        $this->authenticator        = $this->module->authenticator;
        $this->rateLimiter          = $this->module->rateLimiter;

        parent::init();
    }

    /**
     * Get filemanager with uploaded files.
     *
     * @throws BadRequestHttpException
     *
     * @return string
     */
    public function actionFilemanager()
    {
        try {
            $request = \Yii::$app->request;

            if (null !== $request->get('owner') || null !== $request->get('ownerAttribute')) {
                $query = OwnersMediafiles::getMediaFilesQuery([
                    'owner' => $request->get('owner'),
                    'ownerId' => $request->get('ownerId'),
                    'ownerAttribute' => $request->get('ownerAttribute')
                ])->orWhere([
                    'not in', 'id', OwnersMediafiles::find()->select('mediafileId')->asArray()
                ]);
            } else {
                $query = Mediafile::find()->orderBy('id DESC');
            }

            $pagination = new Pagination([
                'defaultPageSize' => 12,
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
        } catch (\Exception|InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get uploadmanager for uploading files.
     *
     * @throws BadRequestHttpException
     *
     * @return string
     */
    public function actionUploadmanager()
    {
        try {
            return $this->render('uploadmanager', [
                'manager' => 'uploadmanager',
                'fileAttributeName' => $this->module->fileAttributeName,
            ]);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }
    }
}
