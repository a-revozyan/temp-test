<?php

namespace console\controllers\seeder;

class TravelGroupTypeSeederController extends BaseSeederController
{
    public function actionRun()
    {
        $data = [
            [
                "id" => 1,
                "name_ru" => "Индивидуал",
                "name_uz" => "Individual",
                "name_en" => "Individual",
                "status" => true
            ],
            [
                "id" => 2,
                "name_ru" => "Семья",
                "name_uz" => "Oilaviy",
                "name_en" => "Family",
                "status" => true
            ],
            [
                "id" => 3,
                "name_ru" => "Группа",
                "name_uz" => "Guruh",
                "name_en" => "Group",
                "status" => true
            ]
        ];

        $this->insertData('travel_group_type', ["id","name_ru","name_uz","name_en","status"], $data);
    }
}