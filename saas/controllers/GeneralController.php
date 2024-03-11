<?php

namespace saas\controllers;

use common\helpers\GeneralHelper;
use common\models\Kasko;
use common\models\KaskoFile;
use common\models\KaskoRisk;
use common\models\News;
use common\models\Osago;
use common\models\Partner;
use common\models\Qa;
use common\models\Travel;
use common\models\TravelMember;
use common\models\User;
use frontend\controllers\BaseController;
use frontend\models\GeneralForms\CreateOpinionForm;
use OpenApi\Generator;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class GeneralController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
//                'opinion' => 'POST'
            ]
        ];

        $behaviors['authenticator']['except'] = ["*"];

        return $behaviors;
    }

    public function actionGenerateDoc()
    {
        $openapi = Generator::scan([Yii::getAlias('@saas') . "/controllers"]);
        header('Content-Type: application/json');
        return json_decode($openapi->toJson(), JSON_FORCE_OBJECT);
    }
}