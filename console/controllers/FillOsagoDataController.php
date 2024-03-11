<?php

namespace console\controllers;

use common\models\Osago;
use common\models\Partner;
use yii\base\Controller;
use yii\helpers\VarDumper;

class FillOsagoDataController extends Controller
{
    public function actionRun()
    {
        /** @var Osago[] $osagos */
        $osagos = Osago::find()
            ->andWhere(['>=', 'created_at', strtotime("-3 days")])
            ->andWhere([
                'or',
                ['gross_auto_id' => null],
                ['insurer_name' => null],
                ['insurer_address' => null],
                ['insurer_passport_series' => null],
                ['insurer_passport_number' => null],
                ['insurer_inn' => null],
                ['insurer_birthday' => null],
                ['insurer_license_series' => null],
                ['insurer_license_number' => null],
            ])
            ->all();

        foreach ($osagos as $osago) {
            $osago->getFullInfoFromKapital();
        }

    }
}