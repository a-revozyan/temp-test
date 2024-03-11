<?php

namespace console\controllers\seeder;

class PartnerSeederController extends BaseSeederController
{
    public function actionRun()
    {
        $data = [
            [
                "id" => 9,
                "name" => "APEX Insurance",
                "image" => "partner1601399768.png",
                "status" => 1,
                "created_at" => 1601399768,
                "updated_at" => 1604304328,
                "travel_currency_id" => null,
                "contract_number" => null,
                "f_user_id" => null,
                "service_amount" => null,
                "hook_url" => null,
                "accident_type_id" => null,
                "travel_offer_file" => null
            ],
            [
                "id" => 7,
                "name" => "ALFA LIFE",
                "image" => "partner1598826835.png",
                "status" => 1,
                "created_at" => 1598826835,
                "updated_at" => 1604304309,
                "travel_currency_id" => null,
                "contract_number" => null,
                "f_user_id" => null,
                "service_amount" => null,
                "hook_url" => null,
                "accident_type_id" => null,
                "travel_offer_file" => null
            ],
            [
                "id" => 11,
                "name" => "NEW LIFE INSURANCE",
                "image" => "partner1605457805.png",
                "status" => 1,
                "created_at" => 1605457805,
                "updated_at" => 1605457805,
                "travel_currency_id" => null,
                "contract_number" => null,
                "f_user_id" => null,
                "service_amount" => null,
                "hook_url" => null,
                "accident_type_id" => null,
                "travel_offer_file" => null
            ],
            [
                "id" => 10,
                "name" => "ALFA Invest",
                "image" => "partner1602766341.png",
                "status" => 1,
                "created_at" => 1602766341,
                "updated_at" => 1652974789,
                "travel_currency_id" => null,
                "contract_number" => null,
                "f_user_id" => null,
                "service_amount" => null,
                "hook_url" => null,
                "accident_type_id" => null,
                "travel_offer_file" => null
            ],
            [
                "id" => 12,
                "name" => "Trust Insurance",
                "image" => "rightphoto_2022-05-19_21-11-37.jpg",
                "status" => 1,
                "created_at" => 1652974673,
                "updated_at" => 1652976756,
                "travel_currency_id" => null,
                "contract_number" => null,
                "f_user_id" => null,
                "service_amount" => null,
                "hook_url" => null,
                "accident_type_id" => null,
                "travel_offer_file" => null
            ],
            [
                "id" => 6,
                "name" => "EUROASIA INSURANCE",
                "image" => "partner1598826699.png",
                "status" => 0,
                "created_at" => 1598826699,
                "updated_at" => 1664186729,
                "travel_currency_id" => null,
                "contract_number" => null,
                "f_user_id" => null,
                "service_amount" => null,
                "hook_url" => null,
                "accident_type_id" => null,
                "travel_offer_file" => null
            ],
            [
                "id" => 1,
                "name" => "Gross Insurance",
                "image" => "partner1598589214.png",
                "status" => 1,
                "created_at" => 1598589214,
                "updated_at" => 1690893473,
                "travel_currency_id" => null,
                "contract_number" => "111",
                "f_user_id" => 28757,
                "service_amount" => 200000,
                "hook_url" => "https://gross.uz/ru/kasko-gross/kasko-pdf-save",
                "accident_type_id" => 2,
                "travel_offer_file" => null
            ],
            [
                "id" => 22,
                "name" => "NEO Insurance",
                "image" => "partner1697715678.svg",
                "status" => 1,
                "created_at" => 1694063954,
                "updated_at" => 1697715678,
                "travel_currency_id" => null,
                "contract_number" => "111",
                "f_user_id" => null,
                "service_amount" => null,
                "hook_url" => null,
                "accident_type_id" => 2,
                "travel_offer_file" => null
            ],
            [
                "id" => 23,
                "name" => "INSON",
                "image" => "partner1697713709.png",
                "status" => 1,
                "created_at" => 1695708557,
                "updated_at" => 1697713709,
                "travel_currency_id" => null,
                "contract_number" => "111",
                "f_user_id" => 37244,
                "service_amount" => 200000,
                "hook_url" => "",
                "accident_type_id" => null,
                "travel_offer_file" => null
            ],
            [
                "id" => 18,
                "name" => "Kapital sug'urta",
                "image" => "partner1699961761.jpeg",
                "status" => 1,
                "created_at" => 1660091660,
                "updated_at" => 1699961761,
                "travel_currency_id" => null,
                "contract_number" => "34343434343",
                "f_user_id" => 32035,
                "service_amount" => 200000,
                "hook_url" => "",
                "accident_type_id" => 1,
                "travel_offer_file" => null
            ],
            [
                "id" => 21,
                "name" => "test",
                "image" => "partner1701064330.png",
                "status" => 1,
                "created_at" => 1688966010,
                "updated_at" => 1701064330,
                "travel_currency_id" => null,
                "contract_number" => "test",
                "f_user_id" => 31,
                "service_amount" => 10000,
                "hook_url" => "",
                "accident_type_id" => null,
                "travel_offer_file" => "partner_test_1688966010.pdf"
            ]
        ];

        $this->insertData('partner', ['id', 'name', 'image', 'status', 'created_at', 'updated_at', 'travel_currency_id', 'contract_number', 'f_user_id', 'service_amount', 'hook_url', 'accident_type_id', 'travel_offer_file'],
            $data);
    }
}