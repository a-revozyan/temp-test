<?php

namespace console\controllers\seeder;

class PeriodSeederController extends BaseSeederController
{
    public function actionRun()
    {
        $data = [
            [
                "id" => 1,
                "name_ru" => "1 год",
                "name_uz" => "1 yil",
                "name_en" => "1 year",
                "coeff" => 1.0
            ],
            [
                "id" => 2,
                "name_ru" => "6 месяцев",
                "name_uz" => "6 oy",
                "name_en" => "6 months",
                "coeff" => 0.7
            ]
        ];

        $this->insertData('period', ["id","name_ru","name_uz","name_en","coeff"], $data);
    }
}