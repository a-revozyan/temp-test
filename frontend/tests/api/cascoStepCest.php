<?php
namespace frontend\tests;
use \frontend\tests\api\core\ApiTester;

class cascoStepCest
{
    public $casco_id;
    public const download_policy_kasko_id = 92;
    public const promo_code = "123456";

    public const PAYMENT_VARIANT = [
        'PAYME' => 0,
        'CLICK' => 1,
//        'PAYZE' => 2,
        'ZOOD_PAY' => 3,
        'PAYME_SUBSCRIBE' => 4,
    ];
    private function getCascoJson()
    {
        return [
            "id" => "integer|null",
            "autonumber" => "string|null",
            "insurer_tech_pass_series" => "string|null",
            "insurer_tech_pass_number" => "string|null",
            "insurer_passport_series" => "string|null",
            "insurer_passport_number" => "string|null",
            "insurer_address" => "string|null",
            "insurer_name" => "string|null",
            "insurer_phone" => "string|null",
            "insurer_pinfl" => "string|null",
            "amount_uzs" => "integer|null",
            "price" => "integer|null",
            "begin_date" => "string|null",
            "end_date" => "string|null",
            "year" => "integer|null",
            "status" => "integer|null",
            "promo_amount" => "integer|null",
            "promo_id" => "integer|null",
            "autocomp" => [
                "id" => "integer|null",
                "name" => "string|null",
                "price" => "integer|null",
                "production_year" => "integer|null",
                "automodel" => [
                    "id" => "integer|null",
                    "name" => "string|null",
                    "autobrand" => [
                        "id" => "integer|null",
                        "name" => "string|null"
                    ]
                ],
                "partners" => [
                ]
            ],
            "tariff" => [
                "id" => "integer|null",
                "name" => "string|null",
                "partner" => [
                    "id" => "integer|null",
                    "name" => "string|null",
                    "contract_number" => "string|null",
                    "created_at" => "integer|null",
                    "updated_at" => "integer|null",
                    "status" => "integer|null",
                    "image" => "string|null"
                ]
            ]
        ];
    }

    public $transaction;
    public function _before(ApiTester $I)
    {
        $I->setUserHeader();
    }

    // tests
    public function step1(ApiTester $I)
    {
        $I->haveHttpHeader('content-type', 'application/x-www-form-urlencoded');

        $comps = json_decode($I->sendGet('kaskoapi/autocomp'));
        $autocomp_id = $comps[rand(0, count($comps)-1)]->id;

        $years = json_decode($I->sendGet('kaskoapi/years'));
        $year = $years[rand(0, count($years)-1)];

        $price = $I->sendGet('kaskoapi/calc-auto-comp-price', [
            'autocomp_id' => $autocomp_id,
            'year' => $year,
        ]);

        $tariffs = json_decode($I->sendGet('/kaskoapi/calc-kasko', [
            'autocomp_id' => $autocomp_id,
            'year' => $year,
            'selected_price' => $price,
            'is_islomic' => 0,
            'car_accessory_ids' => [],
            'car_accessory_amounts' => [],
        ]));
        $tariff_id = $tariffs[rand(0, count($tariffs)-1)]->tariff_id;

        $casco = $I->sendPost('casco-step/step1', [
            'autocomp_id' => $autocomp_id,
            'year' => $year,
            'price' => 30000000,
            'tariff_id' => $tariff_id,
            'bridge_company_code' => null,
            'promo_code' => self::promo_code,
        ]);

        $I->expectation($this->getCascoJson(), '');

        $this->casco_id = json_decode($casco)->id;
    }

    public function step2(ApiTester $I)
    {
        $casco = $I->sendPut('casco-step/step2', [
            'autonumber' => "80U950JA",
            'insurer_tech_pass_series' => "AAF",
            'insurer_tech_pass_number' => "3565875",
            'kasko_id' => $this->casco_id
        ]);

        $I->expectation($this->getCascoJson());
    }

    public function step3(ApiTester $I)
    {
        $I->sendPut('casco-step/step3', [
            "insurer_passport_series" => "01t101xa12",
            "insurer_passport_number" => "AAF",
            "insurer_phone" => "998976543223",
            "insurer_address" => "217",
            "begin_date" => "30.01.2022",
            "insurer_name" => "Kimdirr",
            "insurer_pinfl" => "sdf131345134",
            "kasko_id" => $this->casco_id
        ]);

        $I->expectation($this->getCascoJson());
    }

    public function setPromo(ApiTester $I)
    {
        $I->sendPut('casco-step/set-promo', [
            "promo_code" => self::promo_code,
            "kasko_id" => $this->casco_id,
        ]);

        $I->expectation($this->getCascoJson());
    }

    public function removePromo(ApiTester $I)
    {
        $I->sendPut('casco-step/remove-promo', [
            "kasko_id" => $this->casco_id,
        ]);

        $I->expectation($this->getCascoJson());
    }

    public function step4Click(ApiTester $I)
    {
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('casco-step/step4', [
            "payment_variant" => self::PAYMENT_VARIANT['CLICK'],
            "kasko_id" => $this->casco_id
        ]);

        $I->expectation(array_merge(['casco' =>  $this->getCascoJson()], ['checkout' => 'string']));
    }

    public function step4Payme(ApiTester $I)
    {
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('casco-step/step4', [
            "payment_variant" => self::PAYMENT_VARIANT['PAYME'],
            "kasko_id" => $this->casco_id
        ]);

        $I->expectation(array_merge(['casco' =>  $this->getCascoJson()], ['checkout' => 'string']));
    }

//    public function step4Payze(ApiTester $I)
//    {
//        $I->haveHttpHeader('content-type', 'application/json');
//
//        $I->sendPost('casco-step/step4', [
//            "payment_variant" => self::PAYMENT_VARIANT['PAYZE'],
//            "kasko_id" => $this->casco_id
//        ]);
//
//        $I->expectation(array_merge(['casco' =>  $this->getCascoJson()], ['checkout' => 'string']));
//    }

    public function downloadPolicy(ApiTester $I)
    {
        $I->sendGet('casco-step/download-policy', [
            "kasko_id" => self::download_policy_kasko_id
        ]);

        $I->seeResponseCodeIs(200);
    }

    public function kaskoGetById(ApiTester $I)
    {
        $I->sendGet('casco-step/kasko-by-id', [
            "kasko_id" => self::download_policy_kasko_id
        ]);

        $I->expectation($this->getCascoJson());
    }

    public function kaskoOfUser(ApiTester $I)
    {
        $I->sendGet('casco-step/kaskos-of-user');
        $I->expectation(['models' => [$this->getCascoJson()]]);
    }

    public function deleteKasko(ApiTester $I)
    {
        $I->sendDelete('casco-step/delete?kasko_id=' . $this->casco_id);
        $I->seeResponseCodeIs(200);
        $I->canSeeResponseContains('true');
    }
}
