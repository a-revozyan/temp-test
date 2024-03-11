<?php

namespace backend\controllers;

use Yii;
use yii\base\Model;
use common\models\Partner;
use common\models\Product;
use common\models\PartnerProduct;
use common\models\PartnerSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * PartnerController implements the CRUD actions for Partner model.
 */
class PartnerController extends Controller
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
     * Lists all Partner models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PartnerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Partner model.
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
     * Creates a new Partner model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Partner();

        $model->scenario = 'insert';
        $model->status = 1;

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->updated_at = time();
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
        
            if($model->imageFile) {
                $model->image = 'partner' . $model->created_at . '.' . $model->imageFile->extension;
                $model->save();
                $model->upload();
            } else {
                $model->save();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Partner model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = time();
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            if($model->imageFile) {
                $model->image = 'partner' . $model->created_at . '.' . $model->imageFile->extension;
                $model->save();
                $model->upload();
            } else {
                $model->save();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Partner model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionProducts()
    {
        $partners = Partner::find()->all();
        $products = Product::find()->all();

        $models = [];

        foreach($partners as $partner) {
            foreach($products as $product) {
                $n = PartnerProduct::find()->where(['partner_id' => $partner->id, 'product_id' => $product->id])->one();

                if(!$n) {
                    $n = new PartnerProduct();
                    $n->partner_id = $partner->id;
                    $n->product_id = $product->id;
                }
                
                $models[] = $n;
            }
        }

        if(Model::loadMultiple($models, Yii::$app->request->post())) {
            foreach ($models as $pr) {
                if (!is_null($pr->percent)) {
                    $pr->save();
                } else {
                    if($pr->id) {
                        $pr->delete();
                    }
                }
            }

            return $this->redirect(['index']);
        }


        return $this->render('products', [
            'partners' => $partners,
            'products' => $products,
            'models' => $models
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Partner model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Partner the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Partner::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
