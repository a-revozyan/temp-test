<?php

namespace backend\controllers;

use Yii;
use common\models\Partner;
use common\models\TravelRisk;
use common\models\TravelRiskSearch;
use common\models\TravelProgram;
use common\models\TravelProgramRisk;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Model;

/**
 * TravelRiskController implements the CRUD actions for TravelRisk model.
 */
class TravelRiskController extends Controller
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
     * Lists all TravelRisk models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TravelRiskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TravelRisk model.
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
     * Creates a new TravelRisk model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TravelRisk();
        $model->status = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TravelRisk model.
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

    public function actionSetAmounts($partner_id)
    {
        $programs = TravelProgram::find()->where(['partner_id' => $partner_id])->all();
        $risks = TravelRisk::find()->where(['partner_id' => $partner_id])->all();
        $program_risks = [];
        $partner = Partner::findOne($partner_id);

        foreach($programs as $p) {
            foreach ($risks as $r) {
                $model = TravelProgramRisk::find()->where(['program_id' => $p->id, 'risk_id' => $r->id])->one();

                if(is_null($model)) {
                  $model = new TravelProgramRisk();
                  $model->partner_id = $partner_id;
                  $model->program_id = $p->id;
                  $model->risk_id = $r->id;
                }

                $program_risks[] = $model;
            }
        }

        if(Model::loadMultiple($program_risks, Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                foreach ($program_risks as $pr) {
                    if (! ($flag = $pr->save(false))) {
                        $transaction->rollBack();
                        break;
                    }
                }

                if ($flag) {
                    $transaction->commit();
                    return $this->redirect(['travel-program-period/index']);
                }
            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }

        return $this->render('set-amounts', [
            'partner' => $partner,
            'programs' => $programs,
            'risks' => $risks,
            'program_risks' => $program_risks
        ]);
    }

    /**
     * Deletes an existing TravelRisk model.
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
     * Finds the TravelRisk model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TravelRisk the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TravelRisk::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
