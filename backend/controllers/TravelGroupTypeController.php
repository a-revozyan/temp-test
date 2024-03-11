<?php

namespace backend\controllers;

use Yii;
use common\models\TravelGroupType;
use common\models\TravelGroupTypeSearch;
use common\models\Partner;
use common\models\TravelPartnerGroupType;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Model;

/**
 * TravelGroupTypeController implements the CRUD actions for TravelGroupType model.
 */
class TravelGroupTypeController extends Controller
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
     * Lists all TravelGroupType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TravelGroupTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TravelGroupType model.
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
     * Creates a new TravelGroupType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TravelGroupType();
        $model->status = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TravelGroupType model.
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
        $group_types = TravelGroupType::find()->all();

        $models = [];

        foreach($partners as $partner) {
            foreach($group_types as $group_type) {
                $n = TravelPartnerGroupType::find()->where(['partner_id' => $partner->id, 'group_type_id' => $group_type->id])->one();

                if(!$n) {
                    $n = new TravelPartnerGroupType();
                    $n->partner_id = $partner->id;
                    $n->group_type_id = $group_type->id;
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


        return $this->render('partner-group_types', [
            'partners' => $partners,
            'group_types' => $group_types,
            'models' => $models
        ]);
    }

    /**
     * Deletes an existing TravelGroupType model.
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
     * Finds the TravelGroupType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TravelGroupType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TravelGroupType::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
