<?php

namespace frontend\models\TravelStepForms;

use common\helpers\DateHelper;
use common\models\Country;
use common\models\Travel;
use common\models\TravelCountry;
use common\models\TravelExtraInsurance;
use common\models\TravelExtraInsuranceBind;
use common\models\TravelMember;
use common\models\TravelMultiplePeriod;
use common\models\TravelProgram;
use common\models\TravelPurpose;
use common\services\TravelService;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class Step1Form_old extends Model
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
    public $travel_program_id;

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
                    'extra_insurance_ids',
                    'travel_program_id'
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
            [['travel_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => TravelProgram::className(), 'targetAttribute' => ['travel_program_id' => 'id'], 'filter' => ['status' => true]],
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


    public function save()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($this->is_family)
                TravelService::validateFamily($this->birthdays, $this->positions);

            $calc_model = new CalcForm();
            $calc_model->setAttributes($this->attributes);
            $calc_results = $calc_model->calc();
            $calc_result_program = null;
            foreach ($calc_results as $calc_result) {
                if ($calc_result['program']['id'] == $this->travel_program_id)
                    $calc_result_program = $calc_result;
            }
            if ($calc_result_program == null)
                throw new BadRequestHttpException(Yii::t("app", "program_id is incorrect"));

            $travel = New Travel();

            $travel->amount_uzs = $calc_result_program['amount'];
            $travel->amount_usd = $calc_result_program['amount_usd'];

            $travel->f_user_id = Yii::$app->user->identity->getId();
            $travel->purpose_id = $this->travel_purpose_id;
            $travel->program_id = $this->travel_program_id;
            $travel->partner_id = $calc_result_program["partner_id"];
            $travel->begin_date =  DateHelper::date_format($this->begin_date, 'd.m.Y', 'm.d.Y');
            if (!$this->is_multiple)
            {
                $travel->end_date =  DateHelper::date_format($this->end_date, 'd.m.Y', 'm.d.Y');
                $travel->days = DateHelper::between_days($this->begin_date, $this->end_date, "d.m.Y");
            }
            else
            {
                $travel_multiple_period = TravelMultiplePeriod::findOne(['available_interval_days' => $this->available_interval_days, 'days' => $this->days]);
                if (!$travel_multiple_period)
                    throw new NotFoundHttpException(Yii::t('app', "available_interval_days and days is not found"));
                $travel->days = $travel_multiple_period->days;
                $travel->end_date = date('m.d.Y', strtotime($this->begin_date. " + $travel->days days"));
            }

            $travel->is_multiple = $this->is_multiple;
            $travel->isFamily = $this->is_family;
            $travel->has_covid = $this->has_covid;

            if ($this->is_family == 1)
                $travel->group_type_id = 2;
            elseif (count($this->birthdays) > 1)
                $travel->group_type_id = 3;
            else
                $travel->group_type_id = 1;

            $travel->status = Travel::STATUSES['step1'];
            $travel->created_at = time();
            $travel->save();
            $insurer = new TravelMember();
            $insurer->age = 0;
            foreach($this->birthdays as $key => $birthday) {
                $age = DateHelper::calc_age('d.m.Y', $birthday);
                $travel_member = new TravelMember();
                $travel_member->position = $this->positions[$key];
                if ($this->is_family == 0)
                    $travel_member->position = TravelMember::POSITIONS['simple_member'];
                $travel_member->travel_id = $travel->id;
                $travel_member->birthday = DateHelper::date_format($birthday, 'd.m.Y', 'm.d.Y');
                $travel_member->age = $age;
                $travel_member->save();
                if ($insurer->age < $age)
                    $insurer = $travel_member;
            }

            $travel->insurer_birthday = $insurer->birthday;
            $travel->save();
            $insurer->delete();

            foreach ($this->country_ids as $country_id) {
                $travel_country = new TravelCountry();
                $travel_country->travel_id = $travel->id;
                $travel_country->country_id = $country_id;
                $travel_country->save();
            }

            foreach ($this->extra_insurance_ids as $extra_insurance_id) {
                $travel_bind_extra_insurance = new TravelExtraInsuranceBind();
                $travel_bind_extra_insurance->extra_insurance_id = $extra_insurance_id;
                $travel_bind_extra_insurance->travel_id = $travel->id;
                $travel_bind_extra_insurance->save();
            }

            $transaction->commit();
            return $travel;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

}