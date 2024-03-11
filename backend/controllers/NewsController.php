<?php

namespace backend\controllers;

use Yii;
use common\models\News;
use common\models\NewsSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\base\Model;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
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
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single News model.
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
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();

        $model->status = 1;

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->updated_at = time();
     
            $model->save();   
    
            $model->imageFileRu = UploadedFile::getInstance($model, 'imageFileRu');
           

            if($model->imageFileRu) {
                $model->image_ru = 'news_ru_' . $model->id . '.' . $model->imageFileRu->extension;
                $model->save();
                $model->uploadRu();
            }

            $model->imageFileRu = null;
            $model->imageFileUz = UploadedFile::getInstance($model, 'imageFileUz');

            if($model->imageFileUz) {
                $model->image_uz = 'news_uz_' . $model->id . '.' . $model->imageFileUz->extension;
                $model->save();
                $model->uploadUz();
            }

            $model->imageFileUz = null;
            $model->imageFileEn = UploadedFile::getInstance($model, 'imageFileEn');   
            
            if($model->imageFileEn) {
                $model->image_en = 'news_en_' . $model->id . '.' . $model->imageFileEn->extension;
                $model->save();
                $model->uploadEn();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = time();

            $model->imageFileRu = UploadedFile::getInstance($model, 'imageFileRu');
            $model->imageFileUz = UploadedFile::getInstance($model, 'imageFileUz');
            $model->imageFileEn = UploadedFile::getInstance($model, 'imageFileEn');

            if(is_null($model->image_ru) && $model->imageFileRu) {
                $model->image_ru = 'news_ru_' . $model->created_at . '.' . $model->imageFileRu->extension;
            }

            if(is_null($model->image_uz) && $model->imageFileUz) {
                $model->image_uz = 'news_uz_' . $model->created_at . '.' . $model->imageFileUz->extension;
            }

            if(is_null($model->image_en) && $model->imageFileEn) {
                $model->image_en = 'news_en_' . $model->created_at . '.' . $model->imageFileEn->extension;
            }
            //
            $model->save();

            if($model->imageFileRu) {
                $model->uploadRu();
            }

            $model->imageFileRu = null;

            if($model->imageFileUz) {
                $model->uploadUz();
            }

            $model->imageFileUz = null;

            if($model->imageFileEn) {
                $model->uploadEn();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing News model.
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
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
