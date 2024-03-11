<?php

namespace backend\controllers;

use mdm\admin\models\AuthItem;
use Yii;
use common\models\Surveyer;
use common\models\SurveyerSearch;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SurveyerController implements the CRUD actions for Surveyer model.
 */
class SurveyerController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'only' => ['*'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }


    /**
     * Lists all Surveyer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SurveyerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Surveyer model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Surveyer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Surveyer();
        $model->username = (string)time();
        $model->email = "default@surveyer.uz";

        if ($model->load(Yii::$app->request->post())) {
            $model->generateAuthKey();
            $model->save();
            $password = Yii::$app->request->post()["Surveyer"]["password"];

            $model->setPassword($password);

            $model->created_at = time();
            $model->updated_at = time();
            if ($model->save())
            {
                $auth = Yii::$app->authManager;
                if (!($auth_item = $auth->getRole(Surveyer::SURVEYER_ROLE_NAME)))
                {
                    $auth_item = $auth->createRole(Surveyer::SURVEYER_ROLE_NAME);
                    $auth->add($auth_item);
                }
                $auth->assign($auth_item, $model->id);

                return $this->redirect('index');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Surveyer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $password = Yii::$app->request->post()["Surveyer"]["password"];

            if (!empty($password))
                $model->setPassword($password);

            $model->save();
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Surveyer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Surveyer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Surveyer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Surveyer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
