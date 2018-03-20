<?php

namespace app\modules\files\controllers;

use yii\web\Controller;
use app\modules\files\models\Mediafile;

class FileinfoController extends Controller
{
    const FILE_INFO_SRC = '/files/fileinfo/index';

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
     *
     * @return string
     */
    public function actionIndex()
    {
        $id = \Yii::$app->request->post('id');
        $strictThumb = \Yii::$app->request->post('strictThumb');

        $model = Mediafile::findOne($id);

        return $this->renderAjax('index', [
            'model' => $model,
            'strictThumb' => $strictThumb,
        ]);
    }
}