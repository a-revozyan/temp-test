<?php

namespace console\controllers\seeder;

class RegionSeederController extends BaseSeederController
{
    public function actionRun()
    {
        $data = [
            [
                "id" => 1,
                "name_ru" => "г.Ташкент и Ташкентская область",
                "name_uz" => "Toshkent shahri va Toshkent viloyati",
                "name_en" => "Tashkent city and Tashkent region",
                "coeff" => 1.4
            ],
            [
                "id" => 2,
                "name_ru" => "Другие регионы",
                "name_uz" => "Boshqa hududlar",
                "name_en" => "Other regions	",
                "coeff" => 1
            ]
        ];

        $this->insertData('region', ["id","name_ru","name_uz","name_en","coeff"], $data);
    }
}