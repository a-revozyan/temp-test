<?php

namespace console\controllers;

use common\models\OsagoDriver;
use yii\base\Controller;

class DeleteExpiredDriversController extends Controller
{
    public function actionRun()
    {
        OsagoDriver::deleteAll([
            'and',
            ['osago_id' => null],
            ['<=', 'created_at', date('Y-m-d H:i:s', strtotime('-24 hours'))]
        ]);
    }
}