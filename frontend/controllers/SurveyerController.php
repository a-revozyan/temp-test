<?php

namespace frontend\controllers;

use common\models\Kasko;
use common\models\LoginSurveyerForm;
use common\models\Region;
use common\models\ResetPasswordSurveyerForm;
use common\models\SendVerificationCodeSurveyerForm;
use frontend\models\Surveyer;
use frontend\models\SurveyerForms\SurveyerAttachKaskoForm;
use frontend\models\SurveyerForms\SurveyerProcessingKaskoForm;
use mdm\admin\models\User;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;


class SurveyerController extends BaseController
{

    public $surveyer;
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'attach-casco' => ['PUT'],
                'processing-casco' => ['POST'],
            ]
        ];

        $behaviors['authenticator']['except'] = ["*"];

        return $behaviors;
    }

    public function beforeAction($action)
    {
        $access_token = null;
        if (!is_null($this->headers['authorization']))
            $access_token = substr($this->headers['authorization'], 7);

        if (
            $access_token and ($this->surveyer = User::findOne(['access_token' => $access_token]))
            or $this->action->id == "login"
            or $this->action->id == "send-verification-code"
            or $this->action->id == "reset-password"
        )
            return parent::beforeAction($action);

        throw new UnauthorizedHttpException(Yii::t('app', 'please_login_message'));
    }


    public function actionLogin()
    {
        $model = new LoginSurveyerForm();
        $model->setAttributes($this->get);
        if ($access_token = $model->login())
            return $access_token;

        return $this->sendFailedResponse($model->errors, 403);
    }

    public function actionSendVerificationCode()
    {
        $model = new SendVerificationCodeSurveyerForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->send();

        return $this->sendFailedResponse($model->errors, 403);
    }

    public function actionResetPassword()
    {
        $model = new ResetPasswordSurveyerForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->send();

        return $this->sendFailedResponse($model->errors, 403);
    }

    public function actionProfileInfo()
    {
        return [
            'username' => $this->surveyer->username,
            'first_name' => $this->surveyer->first_name,
            'last_name' => $this->surveyer->last_name,
            'email' => $this->surveyer->email,
            'phone_number' => $this->surveyer->phone_number,
            'region' => Region::findOne($this->surveyer->region_id),
            'total_sum' => 100000 * Kasko::find()->where([
                    'status' => Kasko::STATUS['processed'],
                    'surveyer_id' => $this->surveyer->id
                ])->count()
        ];
    }

    public function actionAvailableKaskos()
    {
        return Kasko::with_pagination(['status' => Kasko::STATUS['payed']]);
    }

    public function actionActiveKaskos()
    {
        return Kasko::with_pagination([
            'status' => Kasko::STATUS['attached'],
            'surveyer_id' => $this->surveyer->id
        ]);
    }

    public function actionProcessedKaskos()
    {
        return Kasko::with_pagination([
            'status' => Kasko::STATUS['processed'],
            'surveyer_id' => $this->surveyer->id
        ]);
    }

    public function actionAttachCasco()
    {
        $model = new SurveyerAttachKaskoForm();
        $model->setAttributes($this->put);
        $model->surveyer = $this->surveyer;
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionProcessingCasco()
    {
        $model = new SurveyerProcessingKaskoForm();

        $model->surveyer = $this->surveyer;
        $model->setAttributes($this->post);
        $model->docs = UploadedFile::getInstancesByName('docs');
        $model->images = UploadedFile::getInstancesByName('images');

        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}