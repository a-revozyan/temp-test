<?php

namespace backend\controllers;

use backapi\models\forms\kaskoTariffForms\AttachAutoRiskTypesToKaskoTariffForm;
use Yii;
use common\models\KaskoTariff;
use common\models\KaskoTariffSearch;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * KaskoTariffController implements the CRUD actions for KaskoTariff model.
 */
class KaskoTariffController extends Controller
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
     * Lists all KaskoTariff models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new KaskoTariffSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KaskoTariff model.
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
     * Creates a new KaskoTariff model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new KaskoTariff();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $form_type_tariff = new AttachAutoRiskTypesToKaskoTariffForm();
            $form_type_tariff->setAttributes([
                'tariff_id' => $model->id,
                'auto_risk_type_ids' => Yii::$app->request->post()['KaskoTariff']['auto_risk_type_ids'] ?? []
            ]);
            $form_type_tariff->save();
            $file = UploadedFile::getInstanceByName('KaskoTariff[file]');
            if (!empty($file))
                $model = $model->saveFile($model, $file);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing KaskoTariff model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->auto_risk_type_ids = array_map(function ($auto_risk_type){
            return $auto_risk_type->id;
        }, $model->autoRiskTypes);

        $old_file = $model->file;
        $file = UploadedFile::getInstanceByName('KaskoTariff[file]');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $form_type_tariff = new AttachAutoRiskTypesToKaskoTariffForm();
            $form_type_tariff->setAttributes([
                'tariff_id' => $model->id,
                'auto_risk_type_ids' => Yii::$app->request->post()['KaskoTariff']['auto_risk_type_ids'] ?? []
            ]);
            $form_type_tariff->save();

            if ($file != null)
                $model = $model->saveFile($model, $file, $old_file);
            else
            {
                $model->file = $old_file;
                $model->save();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing KaskoTariff model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $file = $this->findModel($id)->file;

        $this->findModel($id)->delete();

        if (!empty($file))
            unlink(Yii::getAlias('@webroot') .  $file);

        return $this->redirect(['index']);
    }

    /**
     * Finds the KaskoTariff model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return KaskoTariff the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = KaskoTariff::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
