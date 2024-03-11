<?php

namespace backend\controllers;

use Yii;
use common\models\TravelProgram;
use common\models\TravelProgramSearch;
use common\models\TravelProgramCountry;
use common\models\Country;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TravelProgramController implements the CRUD actions for TravelProgram model.
 */
class TravelProgramController extends Controller
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
                    'delete' => ['POST'],
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
     * Lists all TravelProgram models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TravelProgramSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TravelProgram model.
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
     * Creates a new TravelProgram model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TravelProgram();
        $model->status = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if(!empty($model->countries) && count($model->countries) > 0) {
                foreach($model->countries as $c) {
                    $e = new TravelProgramCountry();
                    $e->partner_id = $model->partner_id;
                    $e->program_id = $model->id;
                    $e->country_id = $c;
                    $e->save();
                }
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TravelProgram model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $countries = Yii::$app->request->post()['TravelProgram']['countries'];
            
            if(!empty($countries) && count($countries) > 0) {
                $programCountries = array_column($model->travelProgramCountries, 'country_id');

                    foreach($countries as $c) {
                    if(!in_array($c, $programCountries)) {
                    $e = new TravelProgramCountry();
                    $e->partner_id = $model->partner_id;
                    $e->program_id = $model->id;
                    $e->country_id = $c;
                    $e->save();
                }
                                }

                    foreach($model->travelProgramCountries as $c) {
                     if(!in_array($c->country_id, $countries)) {
                      $c->delete();
                     }
}
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TravelProgram model.
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
     * Finds the TravelProgram model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TravelProgram the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TravelProgram::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
