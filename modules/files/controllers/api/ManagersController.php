<?php

namespace app\modules\files\controllers\api;

use yii\web\Controller;
use app\modules\files\assets\FilemanagerAsset;
use app\modules\files\controllers\api\common\CommonRestController;

/**
 * Class ManagersController
 * Managers controller class to display the next managers:
 * 1. To view and select available files.
 * 2. To upload files.
 *
 * @package Itstructure\FilesModule\controllers\api
 */
class ManagersController extends Controller
{
    public function init()
    {
        $this->layout = '@app/modules/files/views/layouts/main';
        $this->viewPath = '@app/modules/files/views/managers';

        $this->enableCsrfValidation = false;

        parent::init();
    }

    public function actionFilemanager()
    {
        /*FilemanagerAsset::register($this->getView());

        $model = new MediafileSearch();
        $dataProvider = $model->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->defaultPageSize = 15;*/

        return $this->render('filemanager', [
            /*'model' => $model,
            'dataProvider' => $dataProvider,*/
            'body' => 'This is the body.'
        ]);
    }

    public function actionUploadmanager()
    {
        //return $this->render('uploadmanager', ['model' => new Mediafile()]);
    }
}
