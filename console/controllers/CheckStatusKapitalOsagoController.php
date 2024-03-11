<?php

namespace console\controllers;

use common\models\Osago;
use common\models\Partner;
use yii\base\Controller;

class CheckStatusKapitalOsagoController extends Controller
{
    public function actionRun()
    {
        /** @var Osago[] $osagos */
        $osagos = Osago::find()
            ->where(['partner_id' => Partner::PARTNER['kapital']])
            ->andWhere(['status' => Osago::STATUS['step4']])
            ->andWhere(['>=', 'created_at', strtotime("-1 days")])
            ->all();

        foreach ($osagos as $osago) {
           $osago->partner_payment();
        }
    }
}