<?php
namespace frontend\tests;
use frontend\tests\api\core\ApiTester;

class osagoapiCest
{
    public function _before(ApiTester $I)
    {
        $I->setUserHeader();
    }

    // tests
    public function getAutoType (ApiTester $I)
    {
        $I->sendGet('/osagoapi/autotype');
        $I->expectation([
            'id' => 'integer',
            'name' => 'string'
        ]);
    }

    public function getRegion (ApiTester $I)
    {
        $I->sendGet('/osagoapi/region');
        $I->expectation([
            'id' => 'integer',
            'name' => 'string'
        ]);
    }

    public function getRelationships (ApiTester $I)
    {
        $I->sendGet('/osagoapi/relationships');
        $I->expectation([
            'id' => 'integer',
            'name' => 'string'
        ]);
    }

    public function getPeriod (ApiTester $I)
    {
        $I->sendGet('/osagoapi/period');
        $I->expectation([
            'id' => 'integer',
            'name' => 'string'
        ]);
    }

    public function getCitizenship (ApiTester $I)
    {
        $I->sendGet('/osagoapi/citizenship');
        $I->expectation([
            'id' => 'integer',
            'name' => 'string'
        ]);
    }

    public function getNumberDrivers (ApiTester $I)
    {
        $I->sendGet('/osagoapi/number-drivers');
        $I->expectation([
            'id' => 'integer',
            'name' => 'string'
        ]);
    }
}
