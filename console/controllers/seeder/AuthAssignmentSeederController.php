<?php

namespace console\controllers\seeder;

class AuthAssignmentSeederController extends BaseSeederController
{
    public function actionRun()
    {
        $data = [
            [
                "item_name" => "surveyer",
                "user_id" => "75",
                "created_at" => 1652852667
            ],
            [
                "item_name" => "surveyer",
                "user_id" => "94",
                "created_at" => 1658840738
            ],
            [
                "item_name" => "admin",
                "user_id" => "104",
                "created_at" => 1674661804
            ],
            [
                "item_name" => "surveyer",
                "user_id" => "104",
                "created_at" => 1674661804
            ],
            [
                "item_name" => "admin",
                "user_id" => "75",
                "created_at" => 1674662316
            ],
            [
                "item_name" => "admin",
                "user_id" => "106",
                "created_at" => 1674663248
            ],
            [
                "item_name" => "surveyer",
                "user_id" => "106",
                "created_at" => 1674663248
            ],
            [
                "item_name" => "admin",
                "user_id" => "107",
                "created_at" => 1674663265
            ],
            [
                "item_name" => "surveyer",
                "user_id" => "107",
                "created_at" => 1674663265
            ],
            [
                "item_name" => "admin",
                "user_id" => "108",
                "created_at" => 1674663297
            ],
            [
                "item_name" => "surveyer",
                "user_id" => "108",
                "created_at" => 1674663297
            ],
            [
                "item_name" => "admin",
                "user_id" => "94",
                "created_at" => 1674663297
            ],
            [
                "item_name" => "callcenter",
                "user_id" => "109",
                "created_at" => 1679642776
            ],
            [
                "item_name" => "statistic",
                "user_id" => "110",
                "created_at" => 1684220813
            ],
            [
                "item_name" => "partner",
                "user_id" => "111",
                "created_at" => 1687856091
            ],
            [
                "item_name" => "partner",
                "user_id" => "112",
                "created_at" => 1688966010
            ],
            [
                "item_name" => "admin",
                "user_id" => "112",
                "created_at" => 1688966010
            ],
            [
                "item_name" => "partner",
                "user_id" => "113",
                "created_at" => 1690011382
            ],
            [
                "item_name" => "partner",
                "user_id" => "114",
                "created_at" => 1692077869
            ],
            [
                "item_name" => "partner",
                "user_id" => "115",
                "created_at" => 1695708557
            ],
            [
                "item_name" => "bridge_company",
                "user_id" => "116",
                "created_at" => 1697619187
            ],
            [
                "item_name" => "bridge_company",
                "user_id" => "117",
                "created_at" => 1703581869
            ]
        ];

        $this->insertData('auth_assignment', ["item_name", "user_id", "created_at"], $data);
    }
}
