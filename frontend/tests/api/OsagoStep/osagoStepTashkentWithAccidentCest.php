<?php
namespace frontend\OsagoStep\tests;
use frontend\tests\api\core\ApiTester;

class osagoStepTashkentWithAccidentCest
{
    public $osago_id;
    public const PAYMENT_VARIANT = [
        'PAYME' => 0,
        'CLICK' => 1,
        'PAYZE' => 2,
    ];

    public $osago_template = [
        "id" => "integer",
        "period" => "array|null",
        "region" => "array|null",
        "autonumber" => "string",
        "amount_uzs" => "integer",
        "status" => "integer",
        "f_user_is_owner" => "boolean|integer",
        "owner_with_accident" => "boolean|null",
        "payed_date" => "string|null",
        "policy_pdf_url" => "string|null",
        "policy_number" => "string|null",
        "applicant_is_driver" => "boolean|null",
        "begin_date" => "string|null",
        "end_date" => "string|null",
        "numberDrivers" => "array",
        "drivers" => "array",
        "partner" => [
            'id' => "integer",
            'name' => "string",
        ],
        "is_juridic" => "integer",
        'accident_policy_pdf_url' => "integer|null",
        'accident_policy_number' => "string|null",
        'accident_amount' => "integer|null",
        'insurer_passport_series' => "string|null",
        'insurer_passport_number' => "string|null",
        'insurer_license_series' => "string|null",
        'insurer_license_number' => "string|null",
        'insurer_tech_pass_series' => "string|null",
        'insurer_tech_pass_number' => "string|null",
        "insurer_birthday" => "string|null",
        "promo" => [
            "id" => "integer|null",
            "promo_percent" => "float|null",
            "promo_amount" => "string|null",
        ],
    ];

    public $responseContaion = [
        "insurer_tech_pass_series" => "AAF",
        "insurer_tech_pass_number" => "0390422",
        "autonumber" => "01Y195DA",
        "status" => 1,
        "period" => [
            "id" => 1,
            "name" => "1 год",
        ],
        "f_user_is_owner" => true,
        "region" => [
            'id' => 1,
            "name" => "г.Ташкент и Ташкентская область"
        ],
        "is_juridic" => 0,
        "amount_uzs" => 168000,
        "numberDrivers" => [
            "id" => 1,
            "name" => "Не ограничено",
            "description" => null,
        ],
        "drivers" => [],
        "partner" => [
            "id" => 1,
            "name" => "Gross Insurance"
        ],
        "applicant_is_driver" => false,
    ];

    public function _before(ApiTester $I)
    {
        $I->setUserHeader();
    }

    // tests
    public function step1(ApiTester $I)
    {
        $I->haveHttpHeader('content-type', 'application/x-www-form-urlencoded');
        $osago = $I->sendPost('osago-step/step1', [
            'insurer_tech_pass_series' => 'AAF',
            'insurer_tech_pass_number' => '0390422',
            'autonumber' => '01Y195DA',
        ]);

        $I->expectation($this->osago_template, '', $this->responseContaion);
        $this->osago_id = json_decode($osago)->id;
    }

    public function step2(ApiTester $I)
    {
        $I->sendPut('osago-step/step2', [
            'osago_id' => $this->osago_id,
            'number_drivers_id' => 1,
            'change_status' => 1,
            'period_id' => 1,
            'owner_with_accident' => 1,
        ]);
        $this->responseContaion = array_merge($this->responseContaion, [
            "period" => [
                "id" => 1,
                "name" => "1 год",
            ],
            "numberDrivers" => [
                "id" => 1,
                "name" => "Не ограничено",
                "description" => null,
            ],
            "amount_uzs" => 168000,
            "accident_amount" => 10000,
            "status" => 2,
        ]);

        $I->expectation($this->osago_template, '', $this->responseContaion);
    }

    public function step3(ApiTester $I)
    {
        $I->sendPut('osago-step/step3', [
            'osago_id' => $this->osago_id,
            'drivers' => [
                [
                    "relationship_id" => 1,
                    "birthday" => "23.07.1999",
                    "passport_series" => "KA",
                    "passport_number" => "0829728",
                    "license_series" => "AF",
                    "license_number" => "0557857",
                    "with_accident" => true,
                ],
                [
                    "relationship_id" => 1,
                    "birthday" => "23.07.1999",
                    "passport_series" => "KA",
                    "passport_number" => "0829728",
                    "license_series" => "AF",
                    "license_number" => "0557857"
                ]
            ]
        ]);

        $this->responseContaion = array_merge($this->responseContaion, [
            "drivers" => [
                [
                    "birthday" => "23.07.1999",
                    "passport_series" => "KA",
                    "passport_number" => "0829728",
                    "license_series" => "AF",
                    "license_number" => "0557857",
                    "relationship" => [
                        "id" => 1,
                        "name" => "Отец"
                    ],
                    "with_accident" => true,
                ],
                [
                    "birthday" => "23.07.1999",
                    "passport_series" => "KA",
                    "passport_number" => "0829728",
                    "license_series" => "AF",
                    "license_number" => "0557857",
                    "relationship" => [
                        "id" => 1,
                        "name" => "Отец"
                    ],
                    "with_accident" => null,
                ]
            ],
            "status" => 3,
            "accident_amount" => 20000,
        ]);
        $I->expectation($this->osago_template, '', $this->responseContaion);
    }

    public function step4PayzeSaveCard(ApiTester $I)
    {
        $I->haveHttpHeader('content-type', 'application/x-www-form-urlencoded');
        $I->sendPost('osago-step/step4', [
            'osago_id' => $this->osago_id,
            'payment_variant' => self::PAYMENT_VARIANT['PAYZE'],
            'phone_number' => '998946464400',
        ]);
        $I->expectation([], 'https://payze.uz/api/redirect/transaction');
    }

    public function step4Payme(ApiTester $I)
    {
        $I->haveHttpHeader('content-type', 'application/x-www-form-urlencoded');
        $I->sendPost('osago-step/step4', [
            'osago_id' => $this->osago_id,
            'payment_variant' => self::PAYMENT_VARIANT['PAYME'],
            'phone_number' => '998946464400',
        ]);
        $I->expectation([], 'https://checkout.paycom.uz');
    }

    public function step4Click(ApiTester $I)
    {
        $I->haveHttpHeader('content-type', 'application/x-www-form-urlencoded');
        $I->sendPost('osago-step/step4', [
            'osago_id' => $this->osago_id,
            'payment_variant' => self::PAYMENT_VARIANT['CLICK'],
            'phone_number' => '998946464400',
        ]);
        $I->expectation([], 'https://my.click.uz/services/pay');
    }
}
