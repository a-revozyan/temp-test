<?php

namespace backend\controllers;

use common\models\Currency;
use Yii;
use common\models\TravelExtraInsurance;
use common\models\TravelExtraInsuranceSearch;
use common\models\Partner;
use common\models\TravelPartnerExtraInsurance   ;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Model;

/**
 * TravelExtraInsuranceController implements the CRUD actions for TravelExtraInsurance model.
 */
class TravelExtraInsuranceController extends Controller
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
     * Lists all TravelExtraInsurance models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TravelExtraInsuranceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TravelExtraInsurance model.
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
     * Creates a new TravelExtraInsurance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TravelExtraInsurance();
        $model->status = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TravelExtraInsurance model.
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

    /**
     * Deletes an existing TravelExtraInsurance model.
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
     * Finds the TravelExtraInsurance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TravelExtraInsurance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TravelExtraInsurance::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionPartnerCoeffs()
    {
        $partners = Partner::find()->all();
        $extras = TravelExtraInsurance::find()->all();

        $models = [];

        foreach($partners as $partner) {
            foreach($extras as $e) {
                $n = TravelPartnerExtraInsurance::find()->where(['partner_id' => $partner->id, 'extra_insurance_id' => $e->id])->one();

                if(!$n) {
                    $n = new TravelPartnerExtraInsurance();
                    $n->partner_id = $partner->id;
                    $n->extra_insurance_id = $e->id;
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

            return $this->redirect(['partner-coeffs']);
        }

        return $this->render('partner-extra-insurances', [
            'partners' => $partners,
            'extra_insurances' => $extras,
            'models' => $models,
            'euro' => Currency::getEuroRate()
        ]);
    }
}
