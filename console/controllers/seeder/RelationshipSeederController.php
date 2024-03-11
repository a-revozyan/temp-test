<?php

namespace console\controllers\seeder;

class RelationshipSeederController extends BaseSeederController
{
    public function actionRun()
    {
        $data = [
            [
                "id" => 1,
                "name_ru" => "Отец",
                "name_uz" => "Otasi",
                "name_en" => "Father"
            ],
            [
                "id" => 4,
                "name_ru" => "Жена",
                "name_uz" => "Xotini",
                "name_en" => "Wife"
            ],
            [
                "id" => 9,
                "name_ru" => "Старшая сестра",
                "name_uz" => "Opasi",
                "name_en" => "Sister"
            ],
            [
                "id" => 10,
                "name_ru" => "Младшая сестра",
                "name_uz" => "Singlisi",
                "name_en" => "Sister"
            ],
            [
                "id" => 2,
                "name_ru" => "Старший брат",
                "name_uz" => "Akasi",
                "name_en" => "Older Brother"
            ],
            [
                "id" => 3,
                "name_ru" => "Младший брат",
                "name_uz" => "Ukasi",
                "name_en" => "Little Brother"
            ],
            [
                "id" => 5,
                "name_ru" => "Мать",
                "name_uz" => "Onasi",
                "name_en" => "Wife"
            ],
            [
                "id" => 6,
                "name_ru" => "Муж",
                "name_uz" => "Eri",
                "name_en" => "Husband"
            ],
            [
                "id" => 7,
                "name_ru" => "Сын",
                "name_uz" => "O'g'li",
                "name_en" => "Son"
            ],
            [
                "id" => 8,
                "name_ru" => "Дочь",
                "name_uz" => "Qizi",
                "name_en" => "Daughter"
            ]
        ];

        $this->insertData('relationship', ['id', 'name_ru', 'name_uz', 'name_en'], $data);
    }
}