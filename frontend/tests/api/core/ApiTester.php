<?php

namespace frontend\tests\api\core;


use yii\helpers\VarDumper;

class ApiTester extends \frontend\tests\ApiTester
{
    public function setUserHeader()
    {
        $response = $this->sendGet('userapi/login', ['phone' => '998946464400', 'password' => 'test']);
        $token = json_decode($response)->access_token;
        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->haveHttpHeader('accept', '*/*');
        $this->haveHttpHeader('content-type', 'application/json');
    }

    public function setSurveyerHeader()
    {
        $token = $this->sendGet('surveyer/login', ['phone_number' => '998946464400', 'password' => 'test']);
        $this->haveHttpHeader('Authorization', 'Bearer ' . json_decode($token));
        $this->haveHttpHeader('accept', '*/*');
        $this->haveHttpHeader('content-type', 'application/json');
    }

    public function expectation($responsJsonTemplate = [], $responseContain = '', $responseContainJson = [], $statusCode = 200)
    {
        $this->seeResponseCodeIs($statusCode);
        $this->seeResponseIsJson();
        $this->seeResponseMatchesJsonType($responsJsonTemplate);
        $this->seeResponseContains($responseContain);
        $this->seeResponseContainsJson($responseContainJson);
    }
}