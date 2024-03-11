<?php

namespace backend\controllers;

use common\models\Partner;
use Yii;
use common\models\TravelMultiplePeriod;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TravelMultiplePeriodController implements the CRUD actions for TravelMultiplePeriod model.
 */
class TravelMultiplePeriodController extends Controller
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
        ];
    }

    /**
     * Lists all TravelMultiplePeriod models.
     * @return mixed
     */
    public function actionIndex()
    {
        $partner = $this->isExistPartnerId();

        $dataProvider = new ActiveDataProvider([
            'query' => TravelMultiplePeriod::find()->where(['partner_id' => Yii::$app->request->get('partner_id')]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TravelMultiplePeriod model.
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
     * Creates a new TravelMultiplePeriod model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $partner = $this->isExistPartnerId();

        $model = new TravelMultiplePeriod();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index',
                'id' => $model->id,
                'partner_id' => $partner->id
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'partner' => $partner
        ]);
    }

    /**
     * Updates an existing TravelMultiplePeriod model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $partner = $this->isExistPartnerId();

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index',
                'id' => $model->id,
                'partner_id' => $partner->id
            ]);
        }

        return $this->render('update', [
            'model' => $model,
            'partner' => $partner
        ]);
    }

    /**
     * Deletes an existing TravelMultiplePeriod model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        $back = Yii::$app->request->referrer;
        if (str_contains($back, 'view'))
            $back = ['/travel-program-period/index'];

        return $this->redirect($back);
    }

    /**
     * Finds the TravelMultiplePeriod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TravelMultiplePeriod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TravelMultiplePeriod::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function isExistPartnerId()
    {
        if (Yii::$app->request->get('partner_id') == '' or !$partner = Partner::findOne(Yii::$app->request->get('partner_id')))
            throw new NotFoundHttpException();

        return $partner;
    }
}
