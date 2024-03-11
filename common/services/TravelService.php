<?php

namespace common\services;

use common\helpers\DateHelper;
use common\models\TravelMember;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class TravelService
{

    public static function validateFamily($birthdays, $positions)
    {
        $parent_ages = [];
        $child_ages = [];
        foreach ($birthdays as $key => $birthday) {
            $age = DateHelper::calc_age('d.m.Y', $birthday);
            if ($positions[$key] == TravelMember::POSITIONS['parent'])
                $parent_ages[] = $age;
            if ($positions[$key] == TravelMember::POSITIONS['child'])
                $child_ages[] = $age;
        }

        if (count($birthdays) < 3 or count($parent_ages) == 0 or count($child_ages) == 0)
            throw new BadRequestHttpException(Yii::t('app',
                "Oila sifatida sug'urtalanish uchun sayohat ishtirokchilari kamida 3 kishidan iborat bo'lishi kerak va kamida bitta katta yoshli odam va bitta bola bo'lishi kerak."
            ));

        $min_parent_age = min($parent_ages);
        $max_child_age = max($child_ages);
        if ($min_parent_age - $max_child_age < 18)
            throw new BadRequestHttpException(Yii::t('app',
                "Oila sifatida sug'urtalanish uchun katta yoshdagi odamlar yoshi bilan bolalar yoshi kamida 18 yilga farq qilishi kerak"
            ));
    }
}