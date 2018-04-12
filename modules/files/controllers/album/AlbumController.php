<?php

namespace app\modules\files\controllers\album;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\UnknownMethodException;
use yii\web\{Controller, BadRequestHttpException, NotFoundHttpException};
use yii\filters\{VerbFilter, AccessControl};
use app\modules\files\interfaces\UploadModelInterface;
use app\modules\files\models\album\{Album, AlbumSearch};

/**
 * AlbumController implements the CRUD actions for Album model.
 */

/**
 * AlbumController implements the CRUD actions for Album model.
 *
 * @property Album $model
 *
 * @package Itstructure\FilesModule\controllers\album
 */
abstract class AlbumController extends Controller
{
    /**
     * Model object record.
     *
     * @var Album
     */
    private $model;

    /**
     * Returns the name of the base model.
     *
     * @return string
     */
    abstract protected function getModelName():string;

    /**
     * Returns the type of album.
     *
     * @return string
     */
    abstract protected function getAlbumType():string;

    /**
     * Initializer.
     */
    public function init()
    {
        $this->view->params['user'] = \Yii::$app->user->identity;

        $this->viewPath = '@app/modules/files/views/album';

        parent::init();
    }

    /**
     * Set model.
     *
     * @param $model Album
     */
    public function setModel(Album $model): void
    {
        $this->model = $model;
    }

    /**
     * Returns model.
     *
     * @return Album
     */
    public function getModel(): Album
    {
        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->accessRoles,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => [
                        'POST',
                    ],
                ],
            ],
        ];
    }

    /**
     * Give ability of configure view to the module class.
     *
     * @return \yii\base\View|\yii\web\View
     */
    public function getView()
    {
        if (method_exists($this->module, 'getView')) {
            return $this->module->getView();
        }

        return parent::getView();
    }

    /**
     * Lists all Album models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AlbumSearch();
        $searchParams = ArrayHelper::merge(Yii::$app->request->queryParams, [
            $searchModel->formName() => [
                'type' => $this->getAlbumType()
            ]
        ]);
        $dataProvider = $searchModel->search($searchParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Album model.
     *
     * @param integer $id
     *
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Album model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $this->setModelByConditions();

        if ($this->model->load(Yii::$app->request->post()) && $this->model->save()) {
            return $this->redirect([
                'view',
                'id' => $this->model->id
            ]);
        }

        return $this->render('create', [
            'model' => $this->model,
            'type' => $this->getAlbumType()
        ]);
    }

    /**
     * Updates an existing Album model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->setModelByConditions($id);

        if ($this->model->load(Yii::$app->request->post()) && $this->model->save()) {
            return $this->redirect([
                'view',
                'id' => $this->model->id
            ]);
        }

        return $this->render('update', [
            'model' => $this->model,
            'type' => $this->getAlbumType(),
            'thumbnailModel' => $this->model->getThumbnailModel(),
            'ownerParams' => [
                'owner' => $this->model->type,
                'ownerId' => $this->model->primaryKey,
            ]
        ]);
    }

    /**
     * Deletes an existing Album model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Album model based on its primary key value.
     *
     * @param $key
     *
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     *
     * @return mixed
     */
    protected function findModel($key)
    {
        if (null === $key){
            throw new BadRequestHttpException('Key parameter is not defined in findModel method.');
        }

        $modelObject = $this->getNewModel();

        if (!method_exists($modelObject, 'findOne')){
            $class = (new\ReflectionClass($modelObject));
            throw new UnknownMethodException('Method findOne does not exists in ' . $class->getNamespaceName() . '\\' . $class->getShortName().' class.');
        }

        $result = call_user_func([
            $modelObject,
            'findOne',
        ], $key);

        if ($result !== null) {
            return $result;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Returns new object of main Album model.
     *
     * @return Album
     */
    protected function getNewModel(): Album
    {
        $modelName = $this->getModelName();
        return new $modelName;
    }

    /**
     * Returns an intermediate model for working with the main.
     *
     * @param int|string|null $key
     *
     * @return void
     */
    protected function setModelByConditions($key = null): void
    {
        $model = null === $key ? $this->getNewModel() : $this->findModel($key);

        $this->setModel($model);
    }
}