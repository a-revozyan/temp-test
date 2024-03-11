<?php
namespace backend\controllers;

use common\models\BridgeCompany;
use mdm\admin\models\User;
use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\LoginForm;
use common\models\SourceMessageSearch;
use common\models\SourceMessage;
use common\models\Message;
use common\models\Osago;
use common\models\Kasko;
use common\models\Travel;
use common\models\Accident;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'kasko',],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['kasko-remove-from-surveyer', 'translates', 'translate-update', 'osago', 'osago-delete', 'osago-view', 'travel', 'travel-delete', 'travel-view', 'accident', 'accident-delete', 'accident-view', 'kasko-delete', 'kasko-view'],
                        'roles' => ['admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['kasko-view'],
                        'matchCallback' => function($rule, $action){
                            return $id = Yii::$app->request->get('id')
                                and $kasko = Kasko::findOne(['id' => $id])
                                and $bridge_company = BridgeCompany::findOne(['id' => $kasko->bridge_company_id])
                                and $user = User::findOne(['id' => $bridge_company->user_id])
                                and $user->id == Yii::$app->user->identity->getId();
                        }
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionTranslates()
    {
        $searchModel = new SourceMessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('translates', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTranslateUpdate($id)
    {
        $model = SourceMessage::findOne($id);
        $model_ru = Message::find()->where(['id' => $id, 'language' => 'ru'])->one();
        $model_uz = Message::find()->where(['id' => $id, 'language' => 'uz'])->one();
        $model_en = Message::find()->where(['id' => $id, 'language' => 'en'])->one();

        $translateModels = [$model_ru, $model_uz, $model_en];

        if (Yii::$app->request->post()) {
            if (Model::loadMultiple($translateModels, Yii::$app->request->post())) {
                foreach ($translateModels as $tr) {
                    $tr->save(false);
                }
            }

            return $this->redirect(['translates']);
        }

        return $this->render('translate_update', [
            'model' => $model,
            'translateModels' => $translateModels,
        ]);
    }

    public function actionOsago()
    {
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

        if(!empty($roles) && count($roles) > 0 && array_key_exists('partner', $roles)) {
            $query = Osago::find()->where(['partner_id' => Yii::$app->user->identity->partner_id]);
        } else {
            $query = Osago::find();
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('osago', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOsagoDelete($id)
    {
        Osago::findOne($id)->delete();

        return $this->redirect(['osago']);
    }

    public function actionOsagoView($id)
    {
        $model = Osago::findOne($id);

        if(Yii::$app->user->identity->partner_id == $model->partner_id) {
            if(!$model->viewed) {
                $model->viewed = true;
                $model->save();
            }
        }

        return $this->render('osago-view', [
            'model' => $model
        ]);
    }

    public function actionKasko()
    {
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->getId());

        if(!empty($roles) && count($roles) > 0 && array_key_exists('partner', $roles)) {
            $query = Kasko::find()->where(['partner_id' => Yii::$app->user->identity->partner_id]);
        } elseif (
            !empty($roles)
            && count($roles) > 0
            && array_key_exists(BridgeCompany::BRIDGE_COMPANY_ROLE_NAME, $roles)
            && $bridge_company = BridgeCompany::findOne(['user_id' => Yii::$app->user->identity->getId()])
        ) {
            $query = Kasko::find()->where(['bridge_company_id' => $bridge_company->id]);
        } else{
            $query = Kasko::find();
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('kasko', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionKaskoDelete($id)
    {
        Kasko::findOne($id)->delete();

        return $this->redirect(['kasko']);
    }

    public function actionKaskoView($id)
    {
        $model = Kasko::findOne($id);

        if(Yii::$app->user->identity->partner_id == $model->partner_id) {
            if(!$model->viewed) {
                $model->viewed = true;
                $model->save();
            }
        }

        return $this->render('kasko-view', [
            'model' => $model
        ]);
    }

    public function actionKaskoRemoveFromSurveyer($id)
    {
        $model = Kasko::findOne($id);
        if ($model->status != Kasko::STATUS['attached'])
        {
            Yii::$app->session->setFlash('error','Каско не прикреплено к сюрвейеру или сюрвейер уже выполнил работу');
            return $this->redirect(['kasko']);
        }

        $model->surveyer_id = null;
        $model->status = Kasko::STATUS['payed'];
        $model->save();

        return $this->redirect(['kasko']);
    }

    public function actionTravel()
    {
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

        if(!empty($roles) && count($roles) > 0 && array_key_exists('partner', $roles)) {
            $query = Travel::find()->where(['partner_id' => Yii::$app->user->identity->partner_id]);
        } else {
            $query = Travel::find();
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('travel', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTravelDelete($id)
    {
        Travel::findOne($id)->delete();

        return $this->redirect(['travel']);
    }

    public function actionTravelView($id)
    {
        $model = Travel::findOne($id);

        if(Yii::$app->user->identity->partner_id == $model->partner_id) {
            if(!$model->viewed) {
                $model->viewed = true;
                $model->save();
            }
        }

        return $this->render('travel-view', [
            'model' => $model
        ]);
    }

    public function actionAccident()
    {
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

        if(!empty($roles) && count($roles) > 0 && array_key_exists('partner', $roles)) {
            $query = Accident::find()->where(['partner_id' => Yii::$app->user->identity->partner_id]);
        } else {
            $query = Accident::find();
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('accident', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAccidentDelete($id)
    {
        Accident::findOne($id)->delete();

        return $this->redirect(['accident']);
    }

    public function actionAccidentView($id)
    {
        $model = Accident::findOne($id);

        if(Yii::$app->user->identity->partner_id == $model->partner_id) {
            if(!$model->viewed) {
                $model->viewed = true;
                $model->save();
            }
        }

        return $this->render('accident-view', [
            'model' => $model
        ]);
    }
}
