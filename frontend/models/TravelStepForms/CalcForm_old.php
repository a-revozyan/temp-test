<?php

namespace frontend\models\TravelStepForms;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\Country;
use common\models\Currency;
use common\models\Partner;
use common\models\PartnerProduct;
use common\models\Travel;
use common\models\TravelAgeGroup;
use common\models\TravelExtraInsurance;
use common\models\TravelFamilyKoef;
use common\models\TravelMember;
use common\models\TravelMultiplePeriod;
use common\models\TravelPartnerExtraInsurance;
use common\models\TravelPartnerInfo;
use common\models\TravelPartnerPurpose;
use common\models\TravelProgramCountry;
use common\models\TravelProgramPeriod;
use common\models\TravelPurpose;
use common\services\TravelService;
use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class CalcForm_old extends Model
{
    public $country_ids;
    public $travel_purpose_id;
    public $begin_date;
    public $end_date;
    public $is_multiple;
    public $has_covid;
    public $is_family;
    public $birthdays;
    public $positions;
    public $available_interval_days;
    public $days;
    public $extra_insurance_ids;

    public function rules()
    {
        return [
            [
                [
                    'country_ids',
                    'travel_purpose_id',
                    'begin_date',
                    'is_multiple',
                    'has_covid',
                    'is_family',
                    'birthdays',
                    'positions',
                    'extra_insurance_ids'
                ],
                'required'
            ],
            ['end_date', 'required', 'when' => function($model){
                return !$model->is_multiple;
            }],
            [['available_interval_days', 'days'], 'required', 'when' => function($model){
                return $model->is_multiple;
            }],
            [['travel_purpose_id', 'is_multiple', 'has_covid', 'is_family'], 'integer'],
            [['is_multiple', 'has_covid', 'is_family'], 'in', 'range' => [0,1]],
            [['begin_date', 'end_date'], 'date', 'format' => 'php: d.m.Y'],
            ['country_ids', 'each', 'rule' => ['exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_ids' => 'id']]],
            ['extra_insurance_ids', 'each', 'rule' => [ 'exist', 'skipOnError' => true, 'targetClass' => TravelExtraInsurance::className(), 'targetAttribute' => ['extra_insurance_ids' => 'id'], 'filter' => ['status' => true]]],
            [['travel_purpose_id'], 'exist', 'skipOnError' => true, 'targetClass' => TravelPurpose::className(), 'targetAttribute' => ['travel_purpose_id' => 'id'], 'filter' => ['status' => true]],
            ['birthdays', 'each', 'rule' => ['date', 'format' => 'php: d.m.Y']],
            ['positions', 'each', 'rule' => ['integer']],
            ['positions', 'each', 'rule' => ['in', 'range' => array_values(TravelMember::POSITIONS)]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'country_ids' => Yii::t('app', 'country_ids'),
            'travel_purpose_id' => Yii::t('app', 'travel_purpose_id'),
            'begin_date' => Yii::t('app', 'begin_date'),
            'end_date' => Yii::t('app', 'end_date'),
            'is_multiple' => Yii::t('app', 'is_multiple'),
            'has_covid' => Yii::t('app', 'has_covid'),
            'is_family' => Yii::t('app', 'is_family'),
            'birthdays' => Yii::t('app', 'birthdays'),
            'positions' => Yii::t('app', 'positions'),
            'extra_insurance_ids' => Yii::t('app', 'extra_insurances'),
            'available_interval_days' => Yii::t('app', 'available_interval_days'),
            'days' => Yii::t('app', 'days'),
        ];
    }

    public function calc()
    {
        if ($this->is_family)
            TravelService::validateFamily($this->birthdays, $this->positions);

        $travel = New Travel();
        $travel->begin_date =  DateHelper::date_format($this->begin_date, 'd.m.Y', 'm.d.Y');
        if (!$this->is_multiple)
        {
            $travel->end_date =  DateHelper::date_format($this->end_date, 'd.m.Y', 'm.d.Y');
            $travel->days = DateHelper::between_days($this->begin_date, $this->end_date, "d.m.Y");
        }
        else
        {
            if (!TravelMultiplePeriod::find()->where(['available_interval_days' => $this->available_interval_days, 'days' => $this->days])->exists())
                throw new NotFoundHttpException(Yii::t('app', "available_interval_days and days is not found"));
            $travel->days = $this->days;
        }

        //--------------------------
        $countries = Country::find()->where(['id' => $this->country_ids])->asArray()->all();
        $parent_ids = array_unique(array_column($countries, 'parent_id'));

        $partners = Partner::find()->where(['status' => 0])->all();
        $usd = Currency::getUsdRate();
        $eu = Currency::getEuroRate();

        $result = [];
        foreach($partners as $partner) :
            $program_countries = TravelProgramCountry::find()->select('program_id')->where(['country_id' => $parent_ids, 'partner_id' => $partner->id])->asArray()->all();
            $program_ids = array_unique(array_column($program_countries, 'program_id'));

            //programs for all chosen countries
            $_program_ids = [];
            foreach($program_ids as $program_id) {
                $program_is_for_all_chosen_countries = true;
                foreach($parent_ids as $parent_id) {
                    if(is_null(TravelProgramCountry::find()->where(['program_id' => $program_id, 'country_id' => $parent_id, 'partner_id' => $partner->id])->one()))
                        $program_is_for_all_chosen_countries = false;
                }
                if($program_is_for_all_chosen_countries) $_program_ids[] = $program_id;
            }

            if (!$this->is_multiple)
                $period_program = TravelProgramPeriod::find()
                    ->innerJoinWith('program')
                    ->where($travel->days . " between travel_program_period.from_day and travel_program_period.to_day")
                    ->andWhere(['travel_program_period.partner_id' => $partner->id, 'travel_program_period.program_id' => $_program_ids, 'travel_program.has_covid' => $this->has_covid])
                    ->orderBy('amount')
                    ->one();
            else
                $period_program = TravelMultiplePeriod::findOne(['available_interval_days' => $this->available_interval_days, 'days' => $this->days, 'partner_id' => $partner->id]);

            $this->extra_insurance_ids = array_filter($this->extra_insurance_ids, function ($extra_insurance_id){
                return !empty($extra_insurance_id);
            });

            $partnerExtraInsurances = TravelPartnerExtraInsurance::find()
                ->where(['extra_insurance_id' => $this->extra_insurance_ids, 'partner_id' => $partner->id])
                ->all();

            $has_all_extra = false;
            if (count($partnerExtraInsurances) >= count($this->extra_insurance_ids))
                $has_all_extra = true;

            $extraInsAmount = TravelPartnerExtraInsurance::find()
                ->where(['extra_insurance_id' => $this->extra_insurance_ids, 'partner_id' => $partner->id])
                ->sum('sum_insured * coeff');

            if (!$extraInsAmount)
                $extraInsAmount = 0;

            $partner_purpose = TravelPartnerPurpose::find()->where(['partner_id' => $partner->id, 'purpose_id' => $this->travel_purpose_id])->one();

            $family_koef = 1;
            if ($this->is_family and $travel_family = TravelFamilyKoef::find()->where(['partner_id' => $partner->id, 'members_count' => count($this->birthdays)])->one())
                $family_koef = $travel_family->koef;

            if (!$period_program or !$has_all_extra or !$partner_purpose or ($this->is_family and is_null($travel_family)))
                continue;

            $total = 0;
            if ($this->is_family)
                $total = count($this->birthdays);
            else
            {
                foreach($this->birthdays as $birthday) {
                    $age = DateHelper::calc_age('d.m.Y', $birthday);
                    $age_group = TravelAgeGroup::find()
                        ->where($age . " between from_age and to_age")
                        ->andWhere(['partner_id' => $partner->id])
                        ->one();

                    $total +=  $age_group->coeff;
                }
            }

            $total_days_amount = $period_program->amount * $travel->days;
            if ($this->is_multiple)
                $total_days_amount = $period_program->amount;
            $amount = $total_days_amount * $usd;
            $amount = $total * $amount * $partner_purpose->coeff * $family_koef + count($this->birthdays) * $extraInsAmount*$eu/100;
            //$amount = $family_koef * ($total * $amount * $partner_purpose->coeff + count($this->birthdays) * $extraInsAmount*$eu/100);   ?????????????

            $partner_product = PartnerProduct::find()->where(['product_id' => 3, 'partner_id' => $partner->id])->one();

            $info = TravelPartnerInfo::find()->where(['partner_id' => $partner->id])->one();

            if(!$info) {
                $info = new TravelPartnerInfo();
            }

            $lang = GeneralHelper::lang_of_local();
            if ($lang == "ru")
                $lang = "";
            else
                $lang = "_$lang";
            $result[] = [
                'partner' => $partner->name,
                'partner_id' => $partner->id,
                'partner_image' => 'uploads/partners/' . $partner->image,
                'program' => $period_program->program,
                'amount' => Travel::ceiling($amount, 1000),
                'amount_usd' => round(Travel::ceiling($amount, 1000) / $usd, 2),
                'franchise' => $info->{"franchise$lang"},
                'info' => [
                    'id' => $info->id,
                    'assistance' => $info->{"assistance$lang"},
                    'limitation' => $info->{"limitation$lang"},
                    'franchise' => $info->{"franchise$lang"},
                    'rules' => isset($info->{"rules" . $lang}) ? "uploads/travel_info/" .  $info->{"rules" . $lang} : "",
                    'policy_example' => isset($info->{"policy_example" . $lang}) ? "uploads/travel_info/" .  $info->{"policy_example" . $lang} : "",
                ],
                'star' => $partner_product->star ?? 0,
                'risks' => $period_program->program->risks
            ];
        endforeach;

        return $result;
    }

}