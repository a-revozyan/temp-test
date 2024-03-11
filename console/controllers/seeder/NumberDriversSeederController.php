<?php

namespace console\controllers\seeder;

class NumberDriversSeederController extends BaseSeederController
{
    public function actionRun()
    {
        $data = [
            [
                "id" => 1,
                "name_ru" => "Не ограничено",
                "name_uz" => "Cheklanmagan",
                "name_en" => "Unlimited",
                "coeff" => 3.0,
                "description_uz" => null,
                "description_ru" => null,
                "description_en" => null
            ],
            [
                "id" => 2,
                "name_ru" => "До 5 человек",
                "name_uz" => "5 nafargacha",
                "name_en" => "Up to 5 drivers",
                "coeff" => 1.0,
                "description_uz" => null,
                "description_ru" => null,
                "description_en" => null
            ],
            [
                "id" => 4,
                "name_ru" => "До 5 человек",
                "name_uz" => "5 nafargacha",
                "name_en" => "Up to 5 drivers",
                "coeff" => 1.0,
                "description_uz" => null,
                "description_ru" => null,
                "description_en" => null
            ]
        ];

        $this->insertData('number_drivers', ["id","name_ru","name_uz","name_en","coeff","description_uz","description_ru","description_en"], $data);
    }
}