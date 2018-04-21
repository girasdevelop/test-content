<?php

namespace app\modules\files\controllers;

use yii\web\Controller;
use app\modules\files\Module;
use app\modules\files\models\Mediafile;

/**
 * Class FileinfoController
 * Controller class to get file information using view template.
 *
 * @property Module $module
 *
 * @package Itstructure\FilesModule\controllers
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class FileinfoController extends Controller
{
    /**
     * Initialize.
     */
    public function init()
    {
        $this->enableCsrfValidation = $this->module->enableCsrfValidation;
    }

    /**
     * @return array
     */
    public function verbs()
    {
        return [
            'index' => ['POST'],
        ];
    }

    /**
     * Get file info.
     * @return string
     */
    public function actionIndex()
    {
        $id = \Yii::$app->request->post('id');

        $model = Mediafile::findOne($id);

        return $this->renderAjax('index', [
            'model' => $model,
            'fileAttributeName' => $this->module->fileAttributeName
        ]);
    }
}