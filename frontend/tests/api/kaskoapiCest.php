<?php
namespace frontend\tests;
use common\models\Autobrand;
use common\models\Autocomp;
use common\models\Automodel;
use common\models\Kasko;
use common\models\Osago;
use \frontend\tests\api\core\ApiTester;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class kaskoapiCest
{
    public $brand_id;
    public $model_id;
    public $autocomp_id;
    public $year;
    public $price;
    public function _before(ApiTester $I)
    {
        $I->setUserHeader();
    }

    protected function getCalcResultJson()
    {
        return [
            "tariff_id" => 'integer',
            "partner" => "string",
            "partner_image" => "string|null",
            "tariff_file" => "string|null",
            "tariff" => "string",
            "risks" => [
//                [
//                    "id" => 'integer|null',
//                    "name" => "string|null",
//                    "amount" => 'integer|float|null',
//                    "category_id" => 'integer|null',
//                    "description" => 'string|null'
//                ]
            ],
            "amount_without_margin" => 'integer|float',
            "amount_usd" => 'integer|float',
            "amount" => 'integer|float',
            "star" => 'integer|null',
            "franchise" => "string|null",
            "only_first_risk" => "string|null",
            "is_conditional" => "integer|null"
        ];
    }
    protected function getDataOfPolicyJson()
    {
        return [
            'partner_name' => 'string',
            'insurer_passport_series' => 'string',
            'insurer_passport_number' => 'string',
            'autonumber' => 'string',
            'tariff_name' => 'string',
            'insurer_name' => 'string',
            'autocomp' => 'string',
            'year' => 'integer',
            'automodel' => 'string',
            'product' => 'string',
            'begin_date' => 'string',
            'end_date' => 'string',
            'tariff_risks' => 'array',
        ];
    }

    // tests
    public function getAutoBrand(ApiTester $I)
    {
        $brands = json_decode($I->sendGet('/kaskoapi/autobrand'));
        $this->brand_id = $brands[rand(0, count($brands)-1)]->id;
        $I->expectation([
            'id' => 'integer',
            'name' => 'string'
        ]);
    }

    public function getAutoModel(ApiTester $I)
    {
        $models = json_decode($I->sendGet('/kaskoapi/automodel', ["autobrand_id" => $this->brand_id]));
        $this->model_id = $models[rand(0, count($models)-1)]->id;
        $I->expectation([
            'id' => 'integer',
            'name' => 'string'
        ]);
    }

    public function getAutoComp(ApiTester $I)
    {
        $comps = json_decode($I->sendGet('kaskoapi/autocomp', ["automodel_id" => $this->model_id]));
        $this->autocomp_id = $comps[rand(0, count($comps)-1)]->id;
        $I->expectation([
            'id' => 'integer',
            'name' => 'string'
        ]);
    }

    public function getYears(ApiTester $I)
    {
        $years = json_decode($I->sendGet('kaskoapi/years'));
        $this->year = $years[rand(0, count($years)-1)];
        $I->expectation(['integer']);
    }

    public function calcAutoCompPrice(ApiTester $I)
    {
        $this->price = $I->sendGet('kaskoapi/calc-auto-comp-price', [
            'autocomp_id' => $this->autocomp_id,
            'year' => $this->year,
        ]);
        $I->expectation(['integer']);
    }

    public function calcKasko(ApiTester $I)
    {
        $I->sendGet('/kaskoapi/calc-kasko', [
            'autocomp_id' => $this->autocomp_id,
            'year' => $this->year,
            'selected_price' => $this->price,
            'is_islomic' => 0,
            'car_accessory_ids' => [],
            'car_accessory_amounts' => [],
        ]);
        $I->expectation($this->getCalcResultJson());
    }

    public function dataOfPolicy(ApiTester $I)
    {
        $I->sendGet('kaskoapi/data-of-policy', ['kasko_id' => 565]);
        $I->expectation($this->getDataOfPolicyJson());
    }
}
