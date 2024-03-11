<?php
namespace frontend\tests\api;
use common\models\KaskoBySubscription;
use frontend\tests\api\core\ApiTester;

class KaskoBySubscriptionStepCest
{
    public $kbs_id;

    public const PROGRAM_ID = 1;
    public const AMOUNT_UZS = 60000;
    public const AMOUNT_AVTO = 10000000;
    public const APPLICANT_NAME = "Jobir Yusupov Jamol o'g'li";
    public const APPLICANT_PASS_SERIES = "AA";
    public const APPLICANT_PASS_NUMBER = "7923897";
    public const APPLICANT_BIRTHDAY = "1998-10-25";

    public $kbs_template = [
        'id' => "integer",
        'program_id' => "integer",
        'amount_uzs' => "integer",
        'amount_avto' => "integer",
        'autonumber' => "string|null",
        'tech_pass_series' => "string|null",
        'tech_pass_number' => "string|null",
        'applicant_name' => "string|null",
        'applicant_pass_series' => "string|null",
        'applicant_pass_number' => "string|null",
        'applicant_birthday' => "string|null",
        'status' => "integer",
        'saved_card' => "array|null",
        'last_kasko_by_subscription_policy' => [
            'remaining_days' => "integer|null",
            'payed_date' => "string|null",
            'end_date' => "string|null",
        ]
    ];

    public $responseContaion = [
        'program_id' => self::PROGRAM_ID,
        'amount_uzs' => self::AMOUNT_UZS,
        'amount_avto' => self::AMOUNT_AVTO,
        'status' => KaskoBySubscription::STATUS['step1'],
    ];

    public function _before(ApiTester $I)
    {
        $I->setUserHeader();
    }

    // tests
    public function step1(ApiTester $I)
    {
        $I->haveHttpHeader('content-type', 'application/x-www-form-urlencoded');
        $kbs = $I->sendPost('kasko-by-subscription-step/step1', [
            'program_id' => self::PROGRAM_ID,
        ]);

        $I->expectation();
        $this->kbs_id = json_decode($kbs)->id;
    }

    public function step2(ApiTester $I)
    {
        $I->sendPut('kasko-by-subscription-step/step2', [
            'kasko_by_subscription_id' => $this->kbs_id,
            'autonumber' => "80U950JA",
            'tech_pass_series' => "AAF",
            'tech_pass_number' => "3565875",
        ]);

        $this->responseContaion = array_merge($this->responseContaion, [
            "autonumber" => "80U950JA",
            "tech_pass_series" => "AAF",
            "tech_pass_number" => "3565875",
            "status" => KaskoBySubscription::STATUS['step2'],
        ]);
        $I->expectation($this->kbs_template, '', $this->responseContaion);
    }

    public function step3(ApiTester $I)
    {
        $I->sendPut('kasko-by-subscription-step/step3', [
            'kasko_by_subscription_id' => $this->kbs_id,
            'applicant_name' => self::APPLICANT_NAME,
            'applicant_pass_series' => self::APPLICANT_PASS_SERIES,
            'applicant_pass_number' => self::APPLICANT_PASS_NUMBER,
            'applicant_birthday' => self::APPLICANT_BIRTHDAY,
        ]);

        $this->responseContaion = array_merge($this->responseContaion, [
            'applicant_name' => self::APPLICANT_NAME,
            'applicant_pass_series' => self::APPLICANT_PASS_SERIES,
            'applicant_pass_number' => self::APPLICANT_PASS_NUMBER,
            'applicant_birthday' => self::APPLICANT_BIRTHDAY,
            "status" => KaskoBySubscription::STATUS['step3'],
        ]);
        $I->expectation($this->kbs_template, '', $this->responseContaion);
    }

//    public function step4(ApiTester $I)
//    {
//        $I->haveHttpHeader('content-type', 'application/x-www-form-urlencoded');
//        $I->sendPost('kasko-by-subscription-step/step4', [
//            'kasko_by_subscription_id' => $this->kbs_id,
//            'card_number' => "8600 4954 7331 6478",
//            'card_expiry' => '03/99',
//        ]);
//
//        $this->responseContaion = array_merge($this->responseContaion, [
//            'sent' => true,
//            'phone' => "99890*****91",
//            'wait' => 60000,
//        ]);
//        $I->expectation($this->kbs_template, '', $this->responseContaion);
//    }
}
