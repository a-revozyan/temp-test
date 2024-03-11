<?php

namespace console\controllers;

use common\models\Kasko;
use common\models\Travel;
use common\services\PaymentService;
use yii\base\Controller;

class KaskoController extends Controller
{
    public function actionBackKaskoToStep3()
    {
        return 0;
        $kaskos = Kasko::find()
            ->where(
                [
                    'and',
                    ['<', 'step4_date', strtotime('-10 min', time())],
                    ['status' => Kasko::STATUS['step4']]
                ]
            )->all();

        /** @var Kasko $kasko */
        foreach ($kaskos as $kasko) {
            $kasko->statusToBackBeforePayment();
        }

        //backTravelToStep2
        $travels = Travel::find()
            ->where(
                [
                    'and',
                    ['<', 'step3_date', strtotime('-10 min', time())],
                    ['status' => Travel::STATUSES['step3']]
                ]
            )->all();

        /** @var Travel $travel */
        foreach ($travels as $travel) {
            $travel->statusToBackBeforePayment();
        }
    }
}