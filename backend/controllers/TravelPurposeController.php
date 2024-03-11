<?php

namespace backend\controllers;

use Yii;
use common\models\TravelPurpose;
use common\models\TravelPurposeSearch;
use common\models\Partner;
use common\models\TravelPartnerPurpose;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Model;

/**
 * TravelPurposeController implements the CRUD actions for TravelPurpose model.
 */
class TravelPurposeController extends Controller
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
     * Lists all TravelPurpose models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TravelPurposeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TravelPurpose model.
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
     * Creates a new TravelPurpose model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TravelPurpose();
        $model->status = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TravelPurpose model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    public function actionPartnerCoeffs()
    {
        $partners = Partner::find()->all();
        $purposes = TravelPurpose::find()->all();

        $models = [];

        foreach($partners as $partner) {
            foreach($purposes as $purpose) {
                $n = TravelPartnerPurpose::find()->where(['partner_id' => $partner->id, 'purpose_id' => $purpose->id])->one();

                if(!$n) {
                    $n = new TravelPartnerPurpose();
                    $n->partner_id = $partner->id;
                    $n->purpose_id = $purpose->id;
                }
                
                $models[] = $n;
            }
        }

        if(Model::loadMultiple($models, Yii::$app->request->post())) {
            foreach ($models as $pr) {
                if ($pr->coeff) {
                    $pr->save();
                } else {
                    if($pr->id) {
                        $pr->delete();
                    }
                }
            }

            return $this->redirect(['index']);
        }


        return $this->render('partner-purposes', [
            'partners' => $partners,
            'purposes' => $purposes,
            'models' => $models
        ]);
    }

    /**
     * Deletes an existing TravelPurpose model.
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
     * Finds the TravelPurpose model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TravelPurpose the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TravelPurpose::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
