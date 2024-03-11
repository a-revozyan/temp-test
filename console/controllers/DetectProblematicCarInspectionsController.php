<?php

namespace console\controllers;

use common\models\CarInspection;
use common\models\StatusHistory;
use yii\base\Controller;
use yii\helpers\VarDumper;

class DetectProblematicCarInspectionsController extends Controller
{
    public function actionRun()
    {
        $car_inspections = CarInspection::find()
            ->rightJoin('status_history', 'model_id = car_inspection.id and car_inspection.status = status_history.to_status')
            ->where([
                'model_class' => CarInspection::className(),
                'status' => [CarInspection::STATUS['rejected'], CarInspection::STATUS['processing']],
            ])
            ->groupBy('car_inspection.id')
            ->having(['<', 'max(status_history.created_at)', date('Y-m-d H:i:s', strtotime("-2 hours"))])
            ->all();

        $this->makeProblematic($car_inspections);

        $car_inspections = CarInspection::find()
            ->where(['in', 'status', [CarInspection::STATUS['created']]])
            ->andWhere(['<', 'created_at', date('Y-m-d H:i:s', strtotime("-2 hours"))])
            ->all();

        $this->makeProblematic($car_inspections);
    }

    public function makeProblematic($car_inspections)
    {
        /** @var CarInspection $car_inspection */
        foreach ($car_inspections as $car_inspection) {
            $car_inspection->status = CarInspection::STATUS['problematic'];
            $car_inspection->save();
        }
    }
}