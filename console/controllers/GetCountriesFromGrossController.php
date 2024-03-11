<?php

namespace console\controllers;

use common\jobs\GetCountriesFromGrossJob;
use common\models\GrossCountry;
use common\models\KapitalSugurtaRequest;
use common\models\OsagoRequest;
use common\models\Travel;
use Yii;
use yii\base\Controller;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class GetCountriesFromGrossController extends Controller
{
    public function actionRun()
    {
        Yii::$app->queue1->push(new GetCountriesFromGrossJob());
    }
}