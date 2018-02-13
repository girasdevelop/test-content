<?php

namespace app\controllers\admin;

use Yii;
use Itstructure\AdminModule\controllers\AdminController;

/**
 * Class SettingsController
 * SettingsController implements the CRUD actions for Settings model.
 */
class SettingsController extends AdminController
{
    /**
     * List of records.
     *
     * @return string
     */
    public function actionIndex()
    {
        /* @var $model \app\models\Settings */
        $model = Yii::$app->get('settings')
            ->setModel()
            ->getSettings();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect([
                '/admin/settings'
            ]);
        }

        $fields = [
            'model' => $model,
            'roles' => Yii::$app->authManager->getRoles()
        ];

        return $this->render('index', $fields);
    }
}
