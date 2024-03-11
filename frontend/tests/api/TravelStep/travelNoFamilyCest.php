<?php
namespace frontend\tests\TravelStep;
use \frontend\tests\api\core\ApiTester;

class travelNoFamilyCest
{
    public $travel_id;
    public $members;
    public $countries;
    public $purpose_id;
    public $program_id;
    public const promo_code = "123456";

    public const PAYMENT_VARIANT = [
        'PAYME' => 0,
        'CLICK' => 1,
    ];
    private function getTravelJson()
    {
        return [
            "id" => "integer|null",
            "countries" => [
                [
                    "code" => "string"
                ]
            ],
            "purpose_id" => "integer|null",
            "program_id" => "integer|null",
            "partner" => [
                "id" => "integer|null",
                "name" => "string|null",
                "image" => "string|null",
            ],
            "begin_date" => "string|null",
            "end_date" => "string|null",
            "is_family" => "integer|null",
            "has_covid" => "integer|null",
            "amount_uzs" => "integer|null",
            "price" => "integer|null",
            "status" => "integer|null",
            "travel_members" => [
                [
                    "id" => "integer",
                    "name" => "string|null",
                    "passport_series" => "string|null",
                    "passport_number" => "string|null",
                    "birthday" => "string|null",
                    "age" => "integer|null"
                ]
            ],
            "promo" => [
                "id" => "integer|null",
                "promo_code" => "string|null",
                "promo_percent" => "integer|null",
                "promo_amount" => "integer|null"
            ],
            "policy_number" => "string|null",
            "policy_pdf_url" => "string|null"
        ];
    }

    public function _before(ApiTester $I)
    {
        $I->setUserHeader();
    }

    // tests
    public function countries(ApiTester $I)
    {
        $I->haveHttpHeader('content-type', 'application/x-www-form-urlencoded');
        $I->sendGet('travel-step/countries', []);
        $I->expectation([
                'id' => 'integer',
                'name' => 'string',
                'code' => 'string',
        ], '');
    }

    public function purposes(ApiTester $I)
    {
        $purposes = $I->sendGet('travel-step/purposes', []);

        $I->expectation([
                'id' => 'integer',
                'name' => 'string',
        ]);

        $purposes = json_decode($purposes);
        $this->purpose_id = $purposes[rand(0, count($purposes) - 1)]->id;
    }

    public function daysInterval(ApiTester $I)
    {
        $I->sendGet('travel-step/days-interval', []);
        $I->expectation([
            'min' => 'integer',
            'max' => 'integer',
        ]);
    }

    public function calc(ApiTester $I)
    {
        $countries = ['EG', 'TH', 'TR', 'DE', 'SK', 'SI', 'HU', 'GR', 'LT', 'IT', 'ES', 'FR', 'LV', 'CZ'];
        $this->countries[] = $countries[rand(0, count($countries) - 1)];
        if (rand(0,1) == 1)
            $this->countries[] = $countries[rand(0, count($countries) - 1)];
        if (rand(0,1) == 1)
            $this->countries[] = $countries[rand(0, count($countries) - 1)];

        $this->begin_date = date('d.m.Y', strtotime('+3 days'));
        $this->end_date = date('d.m.Y', strtotime('+13 days'));

        $programs = $I->sendGet('travel-step/calc', [
            'country_codes' => $this->countries,
            'travel_purpose_id' => $this->purpose_id,
            'begin_date' => $this->begin_date,
            'end_date' => $this->end_date,
            'has_covid' => "0",
            'is_family' => "0",
            'birthdays' => [
                "25.10.1998",
                "25.10.1998",
                "25.10.1998",
            ],
        ]);
        $I->expectation([
            'partner' => [
                "id" => "integer",
                "name" => "string",
                "image" => "string",
            ],
            'programs' => [
                [
                    "amount_uzs" => "integer",
                    "amount_usd" => "float|integer",
                    "program_name" => "string",
                    "program_id" => "integer",
                    "currency" => "string",
                    "risks" => [
                        [
                            "name" => "string",
                            "amount" => "integer",
                        ]
                    ],
                    "total" => "integer"
                ]
            ],
        ]);

        $programs = json_decode($programs);
        $this->program_id = $programs[0]->programs[rand(0, count($programs[0]->programs) - 1)]->program_id;
    }

    public function step1(ApiTester $I)
    {
        $I->haveHttpHeader('content-type', 'application/x-www-form-urlencoded');
        $travel = $I->sendPost('travel-step/step1', [
            'country_codes' => $this->countries,
            'travel_purpose_id' => $this->purpose_id,
            'begin_date' => $this->begin_date,
            'end_date' => $this->end_date,
            'has_covid' => "1",
            'is_family' => "0",
            'birthdays' => [
                "25.10.1998",
                "08.12.1971",
            ],
            'program_id' => 6,
            'partner_id' => 1,
        ]);
        $I->expectation($this->getTravelJson());

        $this->travel_id = json_decode($travel)->id;
        $this->members = json_decode($travel)->travel_members;
    }

    public function step2(ApiTester $I)
    {
        $members = array_map(function ($member){
            return [
                'id' => $member->id,
                'name' => "Jobir",
                'passport_series' => "AA",
                'passport_number' => "2345671",
            ];
        }, $this->members);

        $I->sendPut('travel-step/step2', [
            'travel_id' => $this->travel_id,
            'members' => $members,
        ]);
        $I->expectation($this->getTravelJson());
    }

    public function step3(ApiTester $I)
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPost('travel-step/step3', [
            'travel_id' => $this->travel_id,
            'payment_variant' => 1,
        ]);
        $I->expectation(['checkout' => 'string']);
    }
}
