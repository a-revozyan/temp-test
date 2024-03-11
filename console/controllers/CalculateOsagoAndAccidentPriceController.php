<?php

namespace console\controllers;

use common\jobs\CalculateAccidentPriceJob;
use common\jobs\CalculateOsagoJob;
use common\models\KapitalSugurtaRequest;
use Yii;
use yii\base\Controller;

class CalculateOsagoAndAccidentPriceController extends Controller
{
    public function actionRun()
    {
        Yii::$app->queue1->push(new CalculateAccidentPriceJob());

        $vehicle_types = [1, 6, 9, 15];
        $use_territories = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
        $number_drivers = KapitalSugurtaRequest::DRIVER_LIMIT;
        $periods = KapitalSugurtaRequest::PERIOD;

        foreach ($vehicle_types as $vehicle_type) {
            foreach ($use_territories as $use_territory) {
                foreach ($number_drivers as $number_driver) {
                    foreach ($periods as $period) {
                        Yii::$app->queue1->push(new CalculateOsagoJob([
                            'vehicle' => $vehicle_type,
                            'use_territory' => $use_territory,
                            'period' => $period,
                            'driver_limit' => $number_driver
                        ]));
                    }
                }
            }
        }
    }
}