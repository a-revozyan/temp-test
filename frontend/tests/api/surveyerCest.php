<?php
namespace frontend\tests;
use \frontend\tests\api\core\ApiTester;

class surveyerCest
{
    const already_taken = 545;
    private function getCascoJson()
    {
        return [
            "id" => 'integer|null',
            "tariff_id" => 'integer|null',
            "autocomp_id" => 'integer|null',
            "year" => 'integer|null',
            "price" => 'integer|null',
            "autonumber" => "string|null",
            "amount_uzs" => 'integer|null',
            "amount_usd" => 'integer|float|null',
            "begin_date" => "string|null",
            "end_date" => "string|null",
            "status" => 'integer|null',
            "created_at" => "string|null",
            "insurer_name" => "string|null",
            "insurer_address" => "string|null",
            "insurer_phone" => "string|null",
            "insurer_passport_series" => "string|null",
            "insurer_passport_number" => "string|null",
            "insurer_tech_pass_series" => "string|null",
            "insurer_tech_pass_number" => "string|null",
            "insurer_pinfl" => "string|null",
            "partner_id" => 'integer|null',
            "policy_number" => 'string|null',
            "promo_id" => 'integer|null',
            "promo_percent" => 'integer|null',
            "promo_amount" => 'integer|null',
            "f_user_id" => 'integer|null',
            "surveyer_id" => 'integer|null',
            "payed_date" => 'string|null',
            "surveyer_comment" => 'string|null',
            "processed_date" => 'string|null',
            "warehouse_id" => 'integer|null',
            "bridge_company_id" => 'integer|null',
            "step4_date" => "string|null",
            "autobrand_name" => "string|null",
            "automodel_name" => "string|null",
            "autocomp_name" => "string|null",
            "agent_amount" => 'integer|null',
            "surveyer_amount" => 'integer|null',
            "autocomp" => "array|null",
            "tariff" => 'array|null',
            "kaskoFile" => 'array|null',
            "deadline_date" => "string|null"
        ];
    }
    private function getSurveyerProfileJson()
    {
        return [
            "username" => "string",
            "first_name" => "string",
            "last_name" => "string",
            "email" => "string",
            "phone_number" => 'string',
            "region" => [
                "id" => 'integer',
                "name_ru" => "string",
                "name_uz" => "string",
                "name_en" => "string",
                "coeff" => 'integer|float'
            ],
            "total_sum" => 'integer|float'
        ];
    }
    public function _before(ApiTester $I)
    {
        $I->setSurveyerHeader();
    }

    // tests
//    public function sendVerificationCode(ApiTester $I)
//    {
//        $I->sendPut('surveyer/send-verification-code', ["phone_number" => 998946464400]);
//        $I->expectation(['boolean'], 'true');
//    }

    public function availableKaskos(ApiTester $I)
    {
        $I->sendGet('surveyer/available-kaskos');
        $I->expectation(['models' => [$this->getCascoJson()]]);
    }

    public function activKaskos(ApiTester $I)
    {
        $I->sendGet('surveyer/active-kaskos', []);
        $I->expectation(['models' => [$this->getCascoJson()]]);
    }

    public function processedKaskos(ApiTester $I)
    {
        $I->sendGet('surveyer/processed-kaskos');
        $I->expectation(['models' => []]);
    }

    public function attachCascos(ApiTester $I)
    {
        $response = $I->sendPut('surveyer/attach-casco', ['kasko_id' => self::already_taken]);
        $I->expectation(['error' => ['message' => 'string']], "",
            ['error' => ['message' => 'This order is already taken by another surveyer or not ready yet']],
            404);

    }

    public function profileInfo(ApiTester $I)
    {
        $I->sendGet('surveyer/profile-info');
        $I->expectation($this->getSurveyerProfileJson());
    }
}
