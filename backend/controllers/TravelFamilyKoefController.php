<?php

namespace backend\controllers;

use common\models\Partner;
use Yii;
use common\models\TravelFamilyKoef;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TravelFamilyKoefController implements the CRUD actions for TravelFamilyKoef model.
 */
class TravelFamilyKoefController extends Controller
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
     * Lists all TravelFamilyKoef models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TravelFamilyKoef::find()->where(['partner_id' => Yii::$app->request->get('partner_id')]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TravelFamilyKoef model.
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
     * Creates a new TravelFamilyKoef model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $partner = $this->isExistPartnerId();
        $model = new TravelFamilyKoef();

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
     * Updates an existing TravelFamilyKoef model.
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
                'partner_id' => $partner->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'partner' => $partner
        ]);
    }

    /**
     * Deletes an existing TravelFamilyKoef model.
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
     * Finds the TravelFamilyKoef model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TravelFamilyKoef the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TravelFamilyKoef::findOne($id)) !== null) {
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
