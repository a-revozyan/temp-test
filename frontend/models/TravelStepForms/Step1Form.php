<?php

namespace frontend\models\TravelStepForms;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\Country;
use common\models\GrossCountry;
use common\models\KapitalSugurtaRequest;
use common\models\OsagoRequest;
use common\models\Partner;
use common\models\Travel;
use common\models\TravelCountry;
use common\models\TravelGroupType;
use common\models\TravelMember;
use thamtech\uuid\helpers\UuidHelper;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class Step1Form extends Model
{
    public $country_codes;
    public $travel_purpose_id;
    public $begin_date;
    public $end_date;
    public $has_covid;
    public $is_family;
    public $birthdays;
    public $program_id;
    public $partner_id;
    public $travel_uuid;

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
                    'partner_id',
                    'program_id'
                ],
                'required'
            ],
            [['travel_purpose_id', 'has_covid', 'is_family', 'program_id', 'partner_id'], 'integer'],
            [['travel_uuid'], 'string', 'max' => 255],
            [['travel_uuid'], UuidValidator::className()],
            [['has_covid', 'is_family'], 'in', 'range' => [0,1]],
            [['begin_date', 'end_date'], 'date', 'format' => 'php: d.m.Y'],
            [['begin_date'], function($attribute, $params, $validator){
                $begin_date = date_create_from_format('d.m.Y', $this->begin_date)->setTime(0,0,0)->getTimestamp();
                $end_date = date_create_from_format('d.m.Y', $this->end_date)->setTime(0,0,0)->getTimestamp();
                if ($begin_date < strtotime('tomorrow midnight'))
                    $this->addError('begin_date', 'Begin date must be minimum tomorrow');

                $interval_days = ($end_date - $begin_date) / 86400 + 1;
                if ($interval_days < Travel::MIN_DAYS or $interval_days > Travel::MAX_DAYS)
                    $this->addError('end_date', Yii::t('app', 'Period must be from {min_day} to {max_day} days', [
                        'min_day' => Travel::MIN_DAYS,
                        'max_day' => Travel::MAX_DAYS,
                    ]));

            }],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['travel_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Travel::className(), 'targetAttribute' => ['travel_uuid' => 'uuid'], 'filter' => function($query){
                return $query->andWhere(['not in', 'status', [
                    Travel::STATUSES['payed'],
                    Travel::STATUSES['waiting_for_policy'],
                    Travel::STATUSES['received_policy'],
                    Travel::STATUSES['canceled'],
                ]]);
            }],
            ['birthdays', 'each', 'rule' => ['date', 'format' => 'php: d.m.Y']],
            ['birthdays', 'each', 'rule' => ['required']],
            ['country_codes', 'each', 'rule' => ['string']],
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
            'program_id' => Yii::t('app', 'program_id'),
            'partner_id' => Yii::t('app', 'partner_id'),
            'travel_uuid' => Yii::t('app', 'travel_uuid'),
        ];
    }

    public function save()
    {
        if (count($this->birthdays) > 6)
            throw new BadRequestHttpException(Yii::t('app', 'Sayohatchilar 6 nafardan oshmasligi kerak'));

        if ($this->partner_id == Partner::PARTNER['gross'])
        {
            $programs = OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_programs'], new Travel() , [
                'countries' => $this->country_codes,
                'lang' => "ru",
                'has_covid' => $this->has_covid,
            ])['response']->programs;
            foreach ($programs as $pr) {
                if ($pr->program_id == $this->program_id)
                    $program = $pr;
            }
        }elseif ($this->partner_id == Partner::PARTNER['kapital'])
        {
            $programs = KapitalSugurtaRequest::sendTravelRequest(KapitalSugurtaRequest::URLS['travel_programs'], new Travel(), [
                'countries' => array_values(ArrayHelper::map(GrossCountry::find()->where(['code' => $this->country_codes])->all(), 'code', 'kapital_id')),
            ], true, ['accept-language' => GeneralHelper::lang_of_local()], 'POST');
            foreach ($programs as $pr) {
                if ($pr->ID == $this->program_id)
                    $program = $pr;
            }
        }

        $calc_model = new CalcForm();
        $calc_model->setAttributes($this->attributes);
        if ($this->partner_id == Partner::PARTNER['gross'])
            $calc_results = $calc_model->send_calc_request($this->program_id)['response'][0];
        elseif ($this->partner_id == Partner::PARTNER['kapital'])
            $calc_results = $calc_model->send_calc_request_kapital($this->program_id);

        $travel = is_null($this->travel_uuid) ? New Travel() : Travel::findOne(['uuid' => $this->travel_uuid]);

        $travel->amount_uzs = $calc_results->amount_uzs ?? $calc_results['COST_UZS'];
        $travel->amount_usd = $calc_results->amount_usd ?? str_replace(',', '.', $calc_results['COST_USD']);
        $travel->uuid = UuidHelper::uuid();

        $travel->purpose_id = $this->travel_purpose_id;
        $travel->program_name = $program->program_name ?? $program->NAME;
        $travel->price = $program->total ?? $program->OTV;
        $travel->program_id = $this->program_id;
        $travel->partner_id = $this->partner_id;
        $travel->begin_date =  DateHelper::date_format($this->begin_date, 'd.m.Y', 'Y-m-d');
        $travel->end_date =  DateHelper::date_format($this->end_date, 'd.m.Y', 'Y-m-d');
        $travel->days = DateHelper::between_days($this->begin_date, $this->end_date, "d.m.Y");
        $travel->has_covid = $this->has_covid;

        if (count($this->birthdays) < 3)
            $this->is_family = 0;

        if ($this->is_family == 1)
            $travel->group_type_id = TravelGroupType::GROUP_TYPE['family'];
        elseif (count($this->birthdays) == 1)
            $travel->group_type_id = TravelGroupType::GROUP_TYPE['individual'];
        else
            $travel->group_type_id = TravelGroupType::GROUP_TYPE['group'];

        $travel->status = Travel::STATUSES['step1'];
        $travel->created_at = time();
        $travel->save();

        $insurer = new TravelMember();
        $insurer->age = 0;
        TravelMember::deleteAll(['travel_id' => $travel->id]);

        foreach($this->birthdays as $birthday) {
            $age = DateHelper::calc_age('d.m.Y', $birthday);
            $travel_member = new TravelMember();
            $travel_member->travel_id = $travel->id;
            $travel_member->birthday = DateHelper::date_format($birthday, 'd.m.Y', 'Y-m-d');
            $travel_member->age = $age;
            $travel_member->save();

            if ($insurer->age < $age)
                $insurer = $travel_member;
        }


        $travel->insurer_birthday = $insurer->birthday;
        $travel->save();

        foreach ($this->country_codes as $country_code) {
            $country = Country::findOne(['code' => $country_code]);
            if (is_null($country))
            {
                $country = new Country();
                $country->name_uz = "not found";
                $country->name_ru = "not found";
                $country->name_en = "not found";
                $country->code = $country_code;
                $country->save();
            }
            $country->kapital_id = GrossCountry::findOne(['code' => $country_code])->kapital_id ?? null;
            $country->save();
            $travel_country = new TravelCountry();
            $travel_country->travel_id = $travel->id;
            $travel_country->country_id = $country->id;
            $travel_country->save();
        }

        return $travel;
    }

}