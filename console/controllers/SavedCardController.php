<?php

namespace console\controllers;

use common\models\SavedCard;
use yii\base\Controller;

class SavedCardController extends Controller
{
    public function actionSeed()
    {
        $card1 = new SavedCard();
        $card1->setAttributes([
            'trans_no' => 'F1EDA74C0C5746E4B48817812E048FB9',
            'card_id' => 'FDBE7CF7F62349FC810EF7E8418419C6',
            'card_mask' => '528058xxxxxx1419',
            'status' => SavedCard::STATUS['saved'],
            'f_user_id' => 31,
            'created_at' => 1664870126
        ]);
        $card1->save();

        $card1 = new SavedCard();
        $card1->setAttributes([
            'trans_no' => 'F1EDA74C0C5746E4B48817812E048FB9',
            'card_id' => 'FDBE7CF7F62349FC810EF7E8418419C6',
            'card_mask' => '581058xxxxxx1222',
            'status' => SavedCard::STATUS['saved'],
            'f_user_id' => 31,
            'created_at' => 1664870126
        ]);
        $card1->save();

        $card1 = new SavedCard();
        $card1->setAttributes([
            'trans_no' => 'F1EDA74C0C5746E4B488178111111111',
            'card_id' => 'FDBE7CF7F62349FC810EF7E811111111',
            'card_mask' => '458058xxxxxx1422',
            'status' => SavedCard::STATUS['saved'],
            'f_user_id' => 31,
            'created_at' => 1664870126
        ]);
        $card1->save();
    }
}