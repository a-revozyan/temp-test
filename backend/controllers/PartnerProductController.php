<?php

namespace backend\controllers;

use Yii;
use common\models\PartnerProduct;
use common\models\PartnerProductSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * PartnerProductController implements the CRUD actions for PartnerProduct model.
 */
class PartnerProductController extends Controller
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
     * Lists all PartnerProduct models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PartnerProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PartnerProduct model.
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
     * Creates a new PartnerProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PartnerProduct();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model->public_offer_ruFile = UploadedFile::getInstance($model, 'public_offer_ruFile');
            if($model->public_offer_ruFile) {
              $model->public_offer_ru = 'public_offer_ru_'.$model->getPrimaryKey().'.'.$model->public_offer_ruFile->extension;
              $model->save();
              $model->uploadPublicOfferRu();
            }

            $model->public_offer_ruFile = null;
            $model->public_offer_uzFile = UploadedFile::getInstance($model, 'public_offer_uzFile');
            if($model->public_offer_uzFile) {
              $model->public_offer_uz = 'public_offer_uz_'.$model->getPrimaryKey().'.'.$model->public_offer_uzFile->extension;
              $model->save();
              $model->uploadPublicOfferUz();
            }
            
            $model->public_offer_uzFile = null;
            $model->public_offer_enFile = UploadedFile::getInstance($model, 'public_offer_enFile');
            if($model->public_offer_enFile) {
              $model->public_offer_en = 'public_offer_en_'.$model->getPrimaryKey().'.'.$model->public_offer_enFile->extension;
              $model->save();
              $model->uploadPublicOfferEn();
            }
            
            $model->public_offer_enFile = null;
            $model->conditions_ruFile = UploadedFile::getInstance($model, 'conditions_ruFile');
            if($model->conditions_ruFile) {
              $model->conditions_ru = 'conditions_ru_'.$model->getPrimaryKey().'.'.$model->conditions_ruFile->extension;
              $model->save();
              $model->uploadConditionsRu();
            }
            
            $model->conditions_ruFile = null;
            $model->conditions_uzFile = UploadedFile::getInstance($model, 'conditions_uzFile');
            if($model->conditions_uzFile) {
              $model->conditions_uz = 'conditions_uz_'.$model->getPrimaryKey().'.'.$model->conditions_uzFile->extension;
              $model->save();
              $model->uploadConditionsUz();
            }
            
            $model->conditions_uzFile = null;
            $model->conditions_enFile = UploadedFile::getInstance($model, 'conditions_enFile');
            if($model->conditions_enFile) {
              $model->conditions_en = 'conditions_en_'.$model->getPrimaryKey().'.'.$model->conditions_enFile->extension;
              $model->save();
              $model->uploadConditionsEn();
            }

            return $this->redirect(['index']);
        }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PartnerProduct model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model->public_offer_ruFile = UploadedFile::getInstance($model, 'public_offer_ruFile');
            if($model->public_offer_ruFile) {
              $model->public_offer_ru = 'public_offer_ru_'.$model->getPrimaryKey().'.'.$model->public_offer_ruFile->extension;
              $model->save();
              $model->uploadPublicOfferRu();
            }

            $model->public_offer_ruFile = null;
            $model->public_offer_uzFile = UploadedFile::getInstance($model, 'public_offer_uzFile');
            if($model->public_offer_uzFile) {
              $model->public_offer_uz = 'public_offer_uz_'.$model->getPrimaryKey().'.'.$model->public_offer_uzFile->extension;
              $model->save();
              $model->uploadPublicOfferUz();
            }
            
            $model->public_offer_uzFile = null;
            $model->public_offer_enFile = UploadedFile::getInstance($model, 'public_offer_enFile');
            if($model->public_offer_enFile) {
              $model->public_offer_en = 'public_offer_en_'.$model->getPrimaryKey().'.'.$model->public_offer_enFile->extension;
              $model->save();
              $model->uploadPublicOfferEn();
            }
            
            $model->public_offer_enFile = null;
            $model->conditions_ruFile = UploadedFile::getInstance($model, 'conditions_ruFile');
            if($model->conditions_ruFile) {
              $model->conditions_ru = 'conditions_ru_'.$model->getPrimaryKey().'.'.$model->conditions_ruFile->extension;
              $model->save();
              $model->uploadConditionsRu();
            }
            
            $model->conditions_ruFile = null;
            $model->conditions_uzFile = UploadedFile::getInstance($model, 'conditions_uzFile');
            if($model->conditions_uzFile) {
              $model->conditions_uz = 'conditions_uz_'.$model->getPrimaryKey().'.'.$model->conditions_uzFile->extension;
              $model->save();
              $model->uploadConditionsUz();
            }
            
            $model->conditions_uzFile = null;
            $model->conditions_enFile = UploadedFile::getInstance($model, 'conditions_enFile');
            if($model->conditions_enFile) {
              $model->conditions_en = 'conditions_en_'.$model->getPrimaryKey().'.'.$model->conditions_enFile->extension;
              $model->save();
              $model->uploadConditionsEn();
            }

            return $this->redirect(['index']);
        }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PartnerProduct model.
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
     * Finds the PartnerProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PartnerProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PartnerProduct::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
