<?php

namespace console\controllers;

use common\jobs\GetCountriesFromGrossJob;
use common\models\KaskoBySubscription;
use thamtech\uuid\helpers\UuidHelper;
use Yii;
use yii\base\Controller;

class HelperController extends Controller
{
    public function actionRun()
    {
        $kbss = KaskoBySubscription::find()->where(['in', 'status', [
            KaskoBySubscription::STATUS['payed'],
            KaskoBySubscription::STATUS['canceled'],
        ]])->andWhere(['uuid' => null])->all();

        foreach ($kbss as $kbs) {
            $kbs->uuid = UuidHelper::uuid();
            $kbs->save();
        }
    }
}