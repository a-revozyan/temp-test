<?php
namespace common\jobs;

use common\models\GrossCountry;
use common\models\KapitalSugurtaRequest;
use common\models\OsagoRequest;
use common\models\Travel;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\queue\RetryableJobInterface;
use yii\web\BadRequestHttpException;

class GetCountriesFromGrossJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    protected $attempt_times = 60;

    public function execute($queue)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $countries = array_values(array_filter(OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_countries'], (new Travel()), ['lang' => 'uz'])['response'], function ($country) {
                return !is_null($country->code);
            }));

            $country_ids = ArrayHelper::map($countries, 'code', 'id');

            $countries_uz = ArrayHelper::map($countries, 'code', 'name');

            $countries_ru = ArrayHelper::map(array_values(array_filter(OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_countries'], (new Travel()), ['lang' => 'ru'])['response'], function ($country) {
                return !is_null($country->code);
            })), 'code', 'name');

            $countries_en = ArrayHelper::map(array_values(array_filter(OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_countries'], (new Travel()), ['lang' => 'en'])['response'], function ($country) {
                return !is_null($country->code);
            })), 'code', 'name');

            $kapital_countries_en = array_map(function ($item){
                $item->NAME = trim($item->NAME);
                return $item;
            }, KapitalSugurtaRequest::sendTravelRequest(KapitalSugurtaRequest::URLS['travel_countries'], (new Travel()), [], true, ['accept-language' => 'en']));
            $kapital_countries_en = ArrayHelper::map($kapital_countries_en, 'NAME', 'ID');

            if (empty($countries_uz) or empty($countries_ru) or empty($countries_en) or empty($kapital_countries_en))
                throw new BadRequestHttpException('countries are empty');

            GrossCountry::deleteAll();
            Yii::$app->db->createCommand("ALTER SEQUENCE gross_country_id_seq RESTART WITH 1")->execute();

            $gross_countries = [];
            foreach ($countries_uz as $code => $name_uz) {
                $gross_country_en = trim(explode(', ', strtoupper($countries_en[$code]))[0]);
                if ($gross_country_en == "RUSSIA")
                    $gross_country_en = "RUSSIAN FEDERATION";
                elseif ($gross_country_en == "UNITED KINGDOM OF GREAT BRITAIN")
                    $gross_country_en = "UNITED KINGDOM OF GREAT BRITAIN AND NORTHERN IRELAND";
                elseif ($gross_country_en == "UAE (UNITED ARAB EMIRATES)")
                    $gross_country_en = "UNITED ARAB EMIRATES";
                elseif ($gross_country_en == "SYRIA")
                    $gross_country_en = "SYRIAN ARAB REPUBLIC";
                elseif ($gross_country_en == "NORTH KOREA")
                    $gross_country_en = "KOREAN PEOPLE'S DEMOCRATIC REPUBLIC";
                elseif ($gross_country_en == "IRAN")
                    $gross_country_en = "ISLAMIC REPUBLIC OF IRAN";
                elseif ($gross_country_en == "MOLDAVIA")
                    $gross_country_en = "THE REPUBLIC OF MOLDOVA";
                elseif ($gross_country_en == "ANGOLA")
                    $gross_country_en = "THE PEOPLE'S REPUBLIC OF ANGOLA";
                elseif ($gross_country_en == "SRI-LANKA")
                    $gross_country_en = "SRI LANKA";
                elseif ($gross_country_en == "DJIBOUTI")
                    $gross_country_en = "JIBOUTI";
                elseif ($gross_country_en == "COMOROS")
                    $gross_country_en = "COMOR ISLANDS";
                elseif ($gross_country_en == "MYANMAR")
                    $gross_country_en = "MYANMAN";
                elseif ($gross_country_en == "PUERTO RICO")
                    $gross_country_en = "PUERTO-RICO";
                elseif ($gross_country_en == "BURKINA FASO")
                    $gross_country_en = "BURKINA-FASO";
                elseif ($gross_country_en == "ERITREA")
                    $gross_country_en = "";
                elseif ($gross_country_en == "TURKS AND CAICOS ISLANDS")
                    $gross_country_en = "THE ISLAND OF TERKS AND CAIKOS";
                elseif ($gross_country_en == "BELIZE")
                    $gross_country_en = "BELIZ";
                elseif ($gross_country_en == "NEW CONIA")
                    $gross_country_en = "NEW CALEDONIA";
                elseif ($gross_country_en == "COCOS (KEELING) ISLANDS")
                    $gross_country_en = "COCONUT ISLANDS";
                elseif ($gross_country_en == "EL SALVADOR")
                    $gross_country_en = "";
                elseif ($gross_country_en == "AMERICAN SAMOA")
                    $gross_country_en = "SAMOA";
                elseif ($gross_country_en == "US VIRGIN ISLANDS")
                    $gross_country_en = "VIRGIN ISLANDS (USA)";
                elseif ($gross_country_en == "SAO TOME AND PRINCIPE")
                    $gross_country_en = "SAN-TOMA AND PRINCIPE";
                elseif ($gross_country_en == "SAINT LUCIA")
                    $gross_country_en = "SAINT-LUCIA";
                elseif ($gross_country_en == "WALLIS AND FUTUNA ISLANDS")
                    $gross_country_en = "ISLAND OF WALLIS AND FUTUNA";
                elseif ($gross_country_en == "CHRISTMAS ISLAND")
                    $gross_country_en = "";
                elseif ($gross_country_en == "THAILAND ZONE")
                    $gross_country_en = "THAILAND";
                elseif ($gross_country_en == "BONAIRE")
                    $gross_country_en = "";
                elseif ($gross_country_en == "SAR (SOUTH AFRICAN REPUBLIC)")
                    $gross_country_en = "";
                elseif ($gross_country_en == "ANGUILLA")
                    $gross_country_en = "";
                elseif ($gross_country_en == "SANGUILLA")
                    $gross_country_en = "";
                elseif ($gross_country_en == "CHAD")
                    $gross_country_en = "";
                elseif ($gross_country_en == "FRENCH POLYNESIA")
                    $gross_country_en = "";
                elseif ($gross_country_en == "SOUTH KOREA")
                    $gross_country_en = "";
                elseif ($gross_country_en == "MACAU")
                    $gross_country_en = "MACAO";
                elseif ($gross_country_en == "LIBYA")
                    $gross_country_en = "LIBYAN ARAB JAMAHIRIYA";
                elseif ($gross_country_en == "LAOS")
                    $gross_country_en = "LAOS PEOPLE'S DEMOCRATIC REPUBLIC";
                elseif ($gross_country_en == "TANZANIA")
                    $gross_country_en = "UNITED REPUBLIC OF TANZANIA";
                elseif ($gross_country_en == "BAHAMAS")
                    $gross_country_en = "THE BAHAMAS ISLANDS";
                elseif ($gross_country_en == "BRUNEI DARUSSALAM")
                    $gross_country_en = "BRUNEI-DARUSSALAM";
                elseif ($gross_country_en == "FRENCH GUIANA")
                    $gross_country_en = "";
                elseif ($gross_country_en == "FEDERATED STATES OF MICRONESIA")
                    $gross_country_en = "";
                elseif ($gross_country_en == "WESTERN SAHARA")
                    $gross_country_en = "WEST SAHARA";
                elseif ($gross_country_en == "ST. PIERRE AND MIQUELON")
                    $gross_country_en = "SAINT PIERRE AND MICHELON";
                elseif ($gross_country_en == "EQUATORIAL GUINEA")
                    $gross_country_en = "REPUBLIC OF EQUATORIAL GUINEA";
                elseif ($gross_country_en == "SWAZILAND")
                    $gross_country_en = "SWAISILAND";
                elseif ($gross_country_en == "GUAM (USA)")
                    $gross_country_en = "GUAM";
                elseif ($gross_country_en == "BHUTAN")
                    $gross_country_en = "BUTANE";
                elseif ($gross_country_en == "LIECHTENSTEIN")
                    $gross_country_en = "LICHTENSTEIN";
                elseif ($gross_country_en == "USA (UNITED STATES AMERICA)")
                    $gross_country_en = "USA";
                elseif ($gross_country_en == "ANTARCTICA")
                    $gross_country_en = "ANTARCTIC";
                elseif ($gross_country_en == "BOUVET ISLAND")
                    $gross_country_en = "";
                elseif ($gross_country_en == "BRITISH INDIAN OCEAN TERRITORY (THE)")
                    $gross_country_en = "BRITISH TERRITORY IN THE INDIAN OCEAN";
                elseif ($gross_country_en == "CAYMAN ISLANDS (THE)")
                    $gross_country_en = "CAYMAN ISLANDS";
                elseif ($gross_country_en == "CONGO (THE DEMOCRATIC REPUBLIC OF THE)")
                    $gross_country_en = "CONGO";
                elseif ($gross_country_en == "CURAçAO")
                    $gross_country_en = "";
                elseif ($gross_country_en == "FRENCH SOUTHERN TERRITORIES")
                    $gross_country_en = "";
                elseif ($gross_country_en == "GUADELOUPE")
                    $gross_country_en = "";
                elseif ($gross_country_en == "GUERNSEY")
                    $gross_country_en = "";
                elseif ($gross_country_en == "HEARD ISLAND AND MCDONALD ISLANDS")
                    $gross_country_en = "";
                elseif ($gross_country_en == "HOLY SEE")
                    $gross_country_en = "";
                elseif ($gross_country_en == "ISLE OF MAN")
                    $gross_country_en = "";
                elseif ($gross_country_en == "JERSEY")
                    $gross_country_en = "";
                elseif ($gross_country_en == "MAYOTTE")
                    $gross_country_en = "MAYOTTA";
                elseif ($gross_country_en == "PALESTINE")
                    $gross_country_en = "";
                elseif ($gross_country_en == "PITCAIRN")
                    $gross_country_en = "";
                elseif ($gross_country_en == "SAINT BARTHéLEMY")
                    $gross_country_en = "";
                elseif ($gross_country_en == "SAINT HELENA")
                    $gross_country_en = "THE ISLAND OF SAINT HELENA";
                elseif ($gross_country_en == "SAINT MARTIN")
                    $gross_country_en = "";
                elseif ($gross_country_en == "SAINT VINCENT AND THE GRENADINES")
                    $gross_country_en = "SAINT VINCENT AND GRENADINES";
                elseif ($gross_country_en == "SINT MAARTEN (DUTCH PART)")
                    $gross_country_en = "";
                elseif ($gross_country_en == "SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS")
                    $gross_country_en = "GEORGIA";
                elseif ($gross_country_en == "SOUTH SUDAN")
                    $gross_country_en = "SUDAN";
                elseif ($gross_country_en == "SVALBARD AND JAN MAYEN")
                    $gross_country_en = "";
                elseif ($gross_country_en == "UNITED STATES MINOR OUTLYING ISLANDS")
                    $gross_country_en = "";
                elseif ($gross_country_en == "ÅLAND ISLAN")
                    $gross_country_en = "";
                elseif ($gross_country_en == "ALL COUNTRIES EXCLUDING USA")
                    $gross_country_en = "";
                elseif ($gross_country_en == "TIMOR-LESTE")
                    $gross_country_en = "EAST TIMOR";

                $gross_countries[] = [
                    'id' => $country_ids[$code],
                    'name_uz' => $name_uz,
                    'name_ru' => $countries_ru[$code],
                    'name_en' => $countries_en[$code],
                    'code' => $code,
                    'kapital_id' => $kapital_countries_en[$gross_country_en] ?? null,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }

            Yii::$app->db->createCommand()->batchInsert('gross_country', ['id', 'name_uz', 'name_ru', 'name_en', 'code', 'kapital_id', 'created_at'], $gross_countries)->execute();

            $transaction->commit();
        }
        catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function getTtr()
    {
        return 120;
    }

    public function canRetry($attempt, $error)
    {
        return  $attempt < $this->attempt_times;
    }
}