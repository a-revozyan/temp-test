<?php

namespace frontend\models\TravelStepForms;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\GrossCountry;
use common\models\KapitalSugurtaRequest;
use common\models\OsagoRequest;
use common\models\Partner;
use common\models\Travel;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class CalcForm extends Model
{
    public $country_codes;
    public $travel_purpose_id;
    public $begin_date;
    public $end_date;
    public $has_covid;
    public $is_family;
    public $birthdays;

    public function rules()
    {
        return [
            [
                [
                    'country_codes',
                    'travel_purpose_id',
                    'begin_date',
                    'end_date',
                    'has_covid',
                    'is_family',
                    'birthdays',
                ],
                'required'
            ],
            [['travel_purpose_id', 'has_covid', 'is_family'], 'integer'],
            [['has_covid', 'is_family'], 'in', 'range' => [0,1]],
            [['country_codes'], 'each', 'rule' => ['string']],
            [['begin_date', 'end_date'], 'date', 'format' => 'php: d.m.Y'],
            ['birthdays', 'each', 'rule' => ['date', 'format' => 'php: d.m.Y']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'country_codes' => Yii::t('app', 'country_codes'),
            'travel_purpose_id' => Yii::t('app', 'travel_purpose_id'),
            'begin_date' => Yii::t('app', 'begin_date'),
            'end_date' => Yii::t('app', 'end_date'),
            'has_covid' => Yii::t('app', 'has_covid'),
            'is_family' => Yii::t('app', 'is_family'),
            'birthdays' => Yii::t('app', 'birthdays'),
        ];
    }

    public function calc()
    {
        $cache_key = 'travel_calc_result_' . json_encode($this->toArray()) . "_" . GeneralHelper::lang_of_local();
        $cachedData = Yii::$app->cache->get($cache_key);

        if ($cachedData !== false)
            return $cachedData;

        $result = $this->getResult();
        Yii::$app->cache->set($cache_key, $result, 3600); // Cache for 1 hour (3600 seconds)
        return $result;

    }

    public function getResult()
    {
        $gross_program_arr = $this->getGrossPrograms();
//        $kapital_program_arr = $this->getKapitalPrograms();

        $result = $gross_program_arr;
//        $result = array_merge($gross_program_arr, $kapital_program_arr);

        ArrayHelper::multisort($result, ['amount_uzs'], [SORT_ASC]);

        return $result;
    }

    public function getKapitalPrograms()
    {
        $kapital_ids = array_values(ArrayHelper::map(GrossCountry::find()->where(['code' => $this->country_codes])->all(), 'code', 'kapital_id'));
        if (in_array(null, $kapital_ids))
            return [];

        $countries = array_filter($kapital_ids, function ($kapital_id){
            return !is_null($kapital_id);
        });

        $programs = KapitalSugurtaRequest::sendTravelRequest(KapitalSugurtaRequest::URLS['travel_programs'], new Travel(), [
            'countries' => $countries,
        ], true, ['accept-language' => GeneralHelper::lang_of_local()], 'POST');

        $kapital_program_arr = [];
        foreach($programs as $program) :
            $price = $this->send_calc_request_kapital($program->ID);
            $gross_program = [
                'amount_usd' => $price['COST_USD'],
                'amount_uzs' => $price['COST_UZS'],
                'currency' => "",
                'program_id' => $program->ID,
                'program_name' => $program->NAME,
                'total' => $program->OTV,
                'partner' => Partner::findOne(Partner::PARTNER['kapital'])->getForIdNameArr(),
            ];
            $gross_program['risks'] = [
                [
                    "name" => Yii::t('app', 'medex'),
                    "amount" => $program->MEDEX,
                ],
                [
                    "name" => Yii::t('app', 'accident'),
                    "amount" => $program->ACCIDENT,
                ],
                [
                    "name" => Yii::t('app', 'ticket'),
                    "amount" => $program->TICKET,
                ]
            ];
            $kapital_program_arr[] = $gross_program;
        endforeach;

        return $kapital_program_arr;
    }

    public function getGrossPrograms()
    {
        $programs = OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_programs'], new Travel(), [
            'countries' => $this->country_codes,
            'lang' => GeneralHelper::lang_of_local(),
            'has_covid' => $this->has_covid,
        ]);
        $programs = $programs['response']->programs;
        $programs = ArrayHelper::index($programs, 'program_id');

        $_gross_program_arr = $this->send_calc_request(array_keys($programs))['response'];

        $gross_program_arr = [];
        foreach($_gross_program_arr as $program) :
            $program->risks = $programs[$program->program_id]->risks;
            $program->partner = Partner::findOne(Partner::PARTNER['gross'])->getForIdNameArr();
            $gross_program_arr[] = $program;
        endforeach;

        return $gross_program_arr;
    }

    public function send_calc_request($program_ids)
    {
        if (!is_array($program_ids))
            $program_ids = [$program_ids];

        return OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_calc_amount_by_multiple_program'], new Travel(), [
            'countries' => $this->country_codes,
            'purpose_id' => $this->travel_purpose_id,
            'is_family' => $this->is_family,
            'begin_date' => $this->begin_date,
            'end_date' => $this->end_date,
            'has_covid' => (bool)$this->has_covid,
            'birthdays' => $this->birthdays,
            'program_id' => $program_ids,
        ]);
    }

    public function send_calc_request_kapital($program_id)
    {
        return KapitalSugurtaRequest::sendTravelRequest(KapitalSugurtaRequest::URLS['travel_calc_amount'], new Travel(), [
//            'countries' => array_values(ArrayHelper::map(GrossCountry::find()->where(['code' => $this->country_codes])->all(), 'code', 'kapital_id')),
            'activity_id' => KapitalSugurtaRequest::TRAVEL_PURPOSE[$this->travel_purpose_id],
            'group_id' => !empty($this->is_family) ? KapitalSugurtaRequest::TRAVEL_GROUP['family'] :  KapitalSugurtaRequest::TRAVEL_GROUP['individual'] ,
            'date_reg' => date('d.m.Y'),
            'day' => DateHelper::between_days($this->begin_date, $this->end_date, 'd.m.Y'),
            'type_id' => KapitalSugurtaRequest::TRAVEL_TYPE['one_time'],
            'date_births' => array_values($this->birthdays),
            'program_id' => $program_id,
        ], true, [], 'POST');
    }

}