<?php

namespace backend\controllers;

use Yii;
use common\models\Partner;
use common\models\PartnerSearch;
use common\models\TravelProgramPeriod;
use common\models\TravelProgramPeriodSearch;
use common\models\TravelPartnerInfo;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * TravelProgramPeriodController implements the CRUD actions for TravelProgramPeriod model.
 */
class TravelProgramPeriodController extends Controller
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
     * Lists all TravelProgramPeriod models.
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

    public function actionSetAmounts($partner_id)
    {
        $program_periods = TravelProgramPeriod::find()->where(['partner_id' => $partner_id])->orderBy('program_id', 'from_day')->all();


        $partner = Partner::findOne($partner_id);

        if(empty($program_periods)) {
            $n = new TravelProgramPeriod();
            $n->partner_id = $partner_id;

            $program_periods = [$n];
        }

        if(Yii::$app->request->isPost) {
            $models = Yii::$app->request->post('TravelProgramPeriod');
            
            foreach ($models as $pr) {
                if ($pr['program_id'] && $pr['from_day'] && $pr['to_day'] && $pr['amount']) {
                    if(isset($pr['id']) && !empty($pr['id'])) {
                        $n = TravelProgramPeriod::findOne($pr['id']);
                    } else {
                        $n = new TravelProgramPeriod();
                    }
                    
                    $n->partner_id = $partner_id;
                    $n->program_id = $pr['program_id'];
                    $n->from_day = $pr['from_day'];
                    $n->to_day = $pr['to_day'];
                    $n->amount = $pr['amount'];
                    $n->save();
                }
            }

            $remove = array_diff(array_column($program_periods, 'id'), array_map('intval', array_column($models, 'id')));

            //var_dump($remove);

            foreach($remove as $r) {
                $m = TravelProgramPeriod::findOne($r);
                $m->delete();
            }

            $partner->travel_currency_id = Yii::$app->request->post('Partner')['travel_currency_id'];
            $partner->save();

            return $this->redirect(['index']);
        }

        return $this->render('set-amounts', [
            'program_periods' => $program_periods,
            'partner' => $partner
        ]);
    }

    public function actionSetTravelInfo($partner_id)
    {
        $model = TravelPartnerInfo::find()->where(['partner_id' => $partner_id])->one();

        $partner = Partner::findOne($partner_id);

        if(!$model) {
            $model = new TravelPartnerInfo();
            $model->partner_id = $partner_id;
        }
        

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model->rulesFile = UploadedFile::getInstance($model, 'rulesFile');
            if($model->rulesFile) {
              $model->rules = 'rules_'.$model->getPrimaryKey().'.'.$model->rulesFile->extension;
              $model->save();
              $model->uploadRules();
            }

            $model->rulesFile = null;
            $model->policyFile = UploadedFile::getInstance($model, 'policyFile');
            if($model->policyFile) {
              $model->policy_example = 'policy_'.$model->getPrimaryKey().'.'.$model->policyFile->extension;
              $model->save();
              $model->uploadPolicy();
            }

            $model->policyFile = null;
            $model->rulesFileUz = UploadedFile::getInstance($model, 'rulesFileUz');
            if($model->rulesFileUz) {
              $model->rules_uz = 'rules_uz_'.$model->getPrimaryKey().'.'.$model->rulesFileUz->extension;
              $model->save();
              $model->uploadRulesUz();
            }

            $model->rulesFileUz = null;
            $model->policyFileUz = UploadedFile::getInstance($model, 'policyFileUz');
            if($model->policyFileUz) {
              $model->policy_example_uz = 'policy_uz_'.$model->getPrimaryKey().'.'.$model->policyFileUz->extension;
              $model->save();
              $model->uploadPolicyUz();
            }

            $model->policyFileUz = null;
            $model->rulesFileEn = UploadedFile::getInstance($model, 'rulesFileEn');
            if($model->rulesFileEn) {
              $model->rules_en = 'rules_en_'.$model->getPrimaryKey().'.'.$model->rulesFileEn->extension;
              $model->save();
              $model->uploadRulesEn();
            }

            $model->rulesFileEn = null;
            $model->policyFileEn = UploadedFile::getInstance($model, 'policyFileEn');
            if($model->policyFileEn) {
              $model->policy_example_en = 'policy_en_'.$model->getPrimaryKey().'.'.$model->policyFileEn->extension;
              $model->save();
              $model->uploadPolicyEn();
            }

            return $this->redirect(['index']);
        }

        return $this->render('set-travel-info', [
            'model' => $model,
            'partner' => $partner
        ]);
    }

    /**
     * Displays a single TravelProgramPeriod model.
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
     * Creates a new TravelProgramPeriod model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TravelProgramPeriod();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TravelProgramPeriod model.
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

    public function actionProgramList($id)
    {               
        $programs = \common\models\TravelProgram::find()
                ->where(['partner_id' => $id])
                ->orderBy('id')
                ->all();
                
        if (!empty($programs)) {
            echo "<option>- select -</option>";

            foreach($programs as $program) {
                echo "<option value='".$program->id."'>".$program->name."</option>";
            }
        } else {
            echo "<option>- select -</option>";
        }
        
    }

    /**
     * Deletes an existing TravelProgramPeriod model.
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
     * Finds the TravelProgramPeriod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TravelProgramPeriod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TravelProgramPeriod::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
