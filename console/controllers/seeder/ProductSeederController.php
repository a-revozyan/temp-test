<?php

namespace console\controllers\seeder;

class ProductSeederController extends BaseSeederController
{
    public function actionRun()
    {
        $data = [
            [
                "id" => 1,
                "name" => "ОСАГО",
                "code" => "osago",
                "status" => 1,
                "created_at" => 1598589998,
                "updated_at" => 1598589998
            ],
            [
                "id" => 2,
                "name" => "КАСКО",
                "code" => "kasko",
                "status" => 1,
                "created_at" => 1598594499,
                "updated_at" => 1598594499
            ],
            [
                "id" => 3,
                "name" => "Travel",
                "code" => "travel",
                "status" => 1,
                "created_at" => 1598594514,
                "updated_at" => 1598594514
            ],
            [
                "id" => 4,
                "name" => "Accident",
                "code" => "accident",
                "status" => 1,
                "created_at" => 1606631233,
                "updated_at" => 1606631233
            ],
            [
                "id" => 5,
                "name" => "Каско по подписку",
                "code" => "kasko-by-subscription",
                "status" => 10,
                "created_at" => 1674741288,
                "updated_at" => 1674741288
            ]
        ];

        $this->insertData('product', ['id', 'name', 'code', 'status', 'created_at', 'updated_at'], $data);
    }
}