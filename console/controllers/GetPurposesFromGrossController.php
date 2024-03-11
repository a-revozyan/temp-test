<?php

namespace console\controllers;

use common\jobs\GetPurposesFromGrossJob;
use Yii;
use yii\base\Controller;

class GetPurposesFromGrossController extends Controller
{
    public function actionRun()
    {
        Yii::$app->queue1->push(new GetPurposesFromGrossJob());

    }
}