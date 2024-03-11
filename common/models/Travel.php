<?php

namespace common\models;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\jobs\TravelRequestJob;
use common\services\SMSService;
use common\services\TelegramService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "travel".
 *
 * @property int $id
 * @property string $uuid
 * @property int $partner_id
 * @property int $program_id
 * @property string $begin_date
 * @property string $end_date
 * @property int $days
 * @property int $purpose_id
 * @property int $group_type_id
 * @property float $amount_uzs
 * @property float $amount_usd
 * @property string $insurer_name
 * @property string $insurer_address
 * @property string $insurer_phone
 * @property string $insurer_passport_series
 * @property string $insurer_passport_number
 * @property string $insurer_pinfl
 * @property string $insurer_birthday
 * @property string $insurer_email
 * @property string $address_delivery
 * @property int $status
 * @property int $created_at
 * @property int $trans_id
 * @property bool $viewed
 * @property string $policy_number
 * @property int $promo_id
 * @property float $promo_percent
 * @property float $promo_amount
 *
 * @property Partner $partner
 * @property TravelGroupType $groupType
 * @property TravelProgram $program
 * @property TravelPurpose $purpose
 * @property TravelCountry[] $travelCountries
 * @property TravelMember[] $travelMembers
 * @property TravelExtraInsuranceBind[] $travelExtraInsuranceBinds
 * @property Traveler[] $travelers
 * @property Transaction $trans
 * @property User $f_user_id,
 * @property int $is_multiple,
 * @property int $has_covid,
 * @property int $step3_date,
 * @property int $payed_date,
 * @property int $order_id_in_gross,
 * @property string $policy_pdf_url,
 * @property string $program_name,
 * @property float $price,
 *
 * @property int $agent_amount
 * @property int $surveyer_amount
 * @property User $user
 *
 */
class Travel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $countries;
    public $extraInsurances;
    public $travelerBirthdays;
    public $isFamily;
    public $is_shengen;
    public $promo_code;

    public const STATUSES = [
        'step1' => 1,
        'step2' => 2,
        'step3' => 3,
        'payed' => 4,
        'waiting_for_policy' => 5,
        'received_policy' => 6,
        'canceled' => 7,
    ];

    public const MIN_DAYS = 5;
    public const MAX_DAYS = 365;

    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'travelMembers';

        $fields['partner']  = function ($model) {
            $partner = $model->partner;
            return [
                'id' => $partner->id,
                'name' => $partner->name,
                'status' => GeneralHelper::env('frontend_project_website') . '/uploads/partners/'.$partner->image
            ];
        };

        $fields['groupType']  = function ($model) {
            $group_type = $model->groupType;
            return [
                'id' => $group_type->id,
                'name' => $group_type->{'name_' . GeneralHelper::lang_of_local()},
                'status' => $group_type->status
            ];
        };

        return $fields;
    }
    
    public static function tableName()
    {
        return 'travel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['begin_date', 'end_date', 'days', 'purpose_id', 'group_type_id', 'status', 'created_at'], 'required', 'message' => Yii::t('app', 'Необходимо заполнить')],
            [['partner_id', 'program_id', 'days', 'purpose_id', 'group_type_id', 'status', 'created_at'], 'default', 'value' => null],
            [['partner_id', 'program_id', 'days', 'purpose_id', 'group_type_id', 'status', 'created_at', 'trans_id', 'promo_id', 'agent_amount', 'surveyer_amount', 'order_id_in_gross', 'reason_id'], 'integer'],
            [['begin_date', 'end_date', 'step3_date', 'comment'], 'safe'],
            [['amount_uzs', 'amount_usd', 'promo_percent', 'promo_amount', 'price'], 'number'],
            [['insurer_email'], 'email'],
            [['insurer_name', 'insurer_address', 'insurer_phone', 'insurer_passport_series', 'insurer_passport_number', 'insurer_pinfl', 'insurer_birthday', 'address_delivery', 'promo_code', 'policy_pdf_url', 'program_name', 'uuid'], 'string', 'max' => 255],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['group_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => TravelGroupType::className(), 'targetAttribute' => ['group_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'partner_id' => Yii::t('app', 'Partner ID'),
            'program_id' => Yii::t('app', 'Program ID'),
            'begin_date' => Yii::t('app', 'Begin Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'days' => Yii::t('app', 'Days'),
            'purpose_id' => Yii::t('app', 'Purpose ID'),
            'group_type_id' => Yii::t('app', 'Group Type ID'),
            'amount_uzs' => Yii::t('app', 'Amount Uzs'),
            'amount_usd' => Yii::t('app', 'Amount Usd'),
            'insurer_name' => Yii::t('app', 'Insurer Name'),
            'insurer_address' => Yii::t('app', 'Insurer Address'),
            'insurer_phone' => Yii::t('app', 'Insurer Phone'),
            'insurer_passport_series' => Yii::t('app', 'Insurer Passport Series'),
            'insurer_passport_number' => Yii::t('app', 'Insurer Passport Number'),
            'insurer_pinfl' => Yii::t('app', 'Insurer Pinfl'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    public function beforeSave($insert)
    {
        StatusHistory::create($this);
        return parent::beforeSave($insert);
    }

    public static function getCountriesArr()
    {
        $lang = GeneralHelper::lang_of_local();

        return GrossCountry::find()->select("id, name_$lang as name, code")->orderBy('name')->asArray()->all();
//        return array_values(array_filter(OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_countries'], (new Travel()), ['lang' => $lang])['response'], function ($country){
//            return !is_null($country->code);
//        }));
    }

    public static function getPurposes()
    {
        $lang = GeneralHelper::lang_of_local();
        return TravelPurpose::find()->select("id, name_$lang as name")->asArray()->all();
//        return OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_purposes'], (new Travel()), ['lang' => $lang])['response'];
    }

    /**
     * Gets query for [[Partner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }

    public function getTravelMembers()
    {
        return $this->hasMany(TravelMember::className(), ['travel_id' => 'id']);
    }

    /**
     * Gets query for [[GroupType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroupType()
    {
        return $this->hasOne(TravelGroupType::className(), ['id' => 'group_type_id']);
    }

    /**
     * Gets query for [[Program]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(TravelProgram::className(), ['id' => 'program_id']);
    }

    /**
     * Gets query for [[Purpose]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurpose()
    {
        return $this->hasOne(TravelPurpose::className(), ['id' => 'purpose_id']);
    }

    /**
     * Gets query for [[TravelCountries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelCountries()
    {
        return $this->hasMany(TravelCountry::className(), ['travel_id' => 'id']);
    }

    /**
     * Gets query for [[TCountries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTCountries()
    {
        return $this->hasMany(Country::className(), ['id' => 'country_id'])->via('travelCountries');
    }

    /**
     * Gets query for [[TravelExtraInsuranceBinds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelExtraInsuranceBinds()
    {
        return $this->hasMany(TravelExtraInsuranceBind::className(), ['travel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTravelExtraInsurances()
    {
        return $this->hasMany(TravelExtraInsurance::className(), ['id' => 'extra_insurance_id'])->via("travelExtraInsuranceBinds");
    }

    /**
     * Gets query for [[Transaction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrans()
    {
        return $this->hasOne(Transaction::className(), ['id' => 'trans_id']);
    }

    /**
     * Gets query for [[Travelers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelers()
    {
        return $this->hasMany(Traveler::className(), ['travel_id' => 'id']);
    }

    public function getPromo()
    {
        return $this->hasOne(Promo::className(), ['id' => 'promo_id']);
    }

    public function getReason()
    {
        return $this->hasOne(Reason::className(), ['id' => 'reason_id']);
    }

    public static function ceiling($number, $significance = 1)
    {
        return ( is_numeric($number) && is_numeric($significance) ) ? (ceil($number/$significance)*$significance) : false;
    }

    public function calc() 
    {
        $countries = Country::find()->where(['id' => $this->countries])->asArray()->all();
        $this->is_shengen = false;

        foreach($countries as $c) {
            if($c['schengen']) $this->is_shengen = true;
        }

        $parent_ids = array_unique(array_column($countries, 'parent_id'));

        //$extra_insurance_ids = Yii::$app->request->get('extraInsurances');

        if($this->partner_id) {
            $partners = [Partner::findOne($this->partner_id)];
        } else {
            $partners = Partner::find()->all();
        }
        
        $purpose = TravelPurpose::findOne([$this->purpose_id]);

        $this->days = abs(round((strtotime($this->begin_date) - strtotime($this->end_date)) / 86400)) + 1; 
        
        $usd = Currency::getUsdRate();

        $result = [];

        $promo = Promo::find()->where(['code' => $this->promo_code])->one();

        if($promo) {
            $this->promo_id = $promo->id;
            $this->promo_percent = $promo->percent;
            $promo_percent = $promo->percent;
        } else {
            $promo_percent = 0;
            $this->promo_percent = 0;
        }

        foreach($partners as $i => $partner) :
            $pr_countries = TravelProgramCountry::find()->select('program_id')->where(['country_id' => $parent_ids, 'partner_id' => $partner->id])->asArray()->all();

            $p_ids = array_unique(array_column($pr_countries, 'program_id'));

            $result_programs = [];

            foreach($p_ids as $pr) {
              $av = true;
              foreach($parent_ids as $par) {
                if(is_null(TravelProgramCountry::find()->where(['program_id' => $pr, 'country_id' => $par, 'partner_id' => $partner->id])->one()))
                  $av = false;
              }
              if($av) $result_programs[] = $pr;
            }


            if($this->program_id) {
                $period_pr_with_covid = TravelProgramPeriod::find()
                    ->innerJoinWith('program')
                    ->where($this->days . " between travel_program_period.from_day and travel_program_period.to_day")
                    ->andWhere(['travel_program.id' => $this->program_id, 'travel_program_period.partner_id' => $partner->id, 'travel_program_period.program_id' => $result_programs, 'travel_program.has_covid' => true])
                    ->orderBy('amount')
                    ->one();

                $period_pr = TravelProgramPeriod::find()
                    ->innerJoinWith('program')
                    ->where($this->days . " between travel_program_period.from_day and travel_program_period.to_day")
                    ->andWhere(['travel_program.id' => $this->program_id, 'travel_program_period.partner_id' => $partner->id, 'travel_program_period.program_id' => $result_programs, 'travel_program.has_covid' => false])
                    ->orderBy('amount')
                    ->one();
            } else {
                $period_pr_with_covid = TravelProgramPeriod::find()
                    ->innerJoinWith('program')
                    ->where($this->days . " between travel_program_period.from_day and travel_program_period.to_day")
                    ->andWhere(['travel_program_period.partner_id' => $partner->id, 'travel_program_period.program_id' => $result_programs, 'travel_program.has_covid' => true])
                    ->orderBy('amount')
                    ->one();

                $period_pr = TravelProgramPeriod::find()
                    ->innerJoinWith('program')
                    ->where($this->days . " between travel_program_period.from_day and travel_program_period.to_day")
                    ->andWhere(['travel_program_period.partner_id' => $partner->id, 'travel_program_period.program_id' => $result_programs, 'travel_program.has_covid' => false])
                    ->orderBy('amount')
                    ->one();
            }
            

            if($this->isFamily == 1) {
                $partner_group_type = TravelPartnerGroupType::find()->where(['partner_id' => $partner->id, 'group_type_id' => 2])->one();
            } else {
                $partner_group_type = null;
            }    
            if(!empty($this->extraInsurances)) {
                $partnerExtraInsurances = TravelPartnerExtraInsurance::find()
                    ->where(['extra_insurance_id' => $this->extraInsurances, 'partner_id' => $partner->id])
                    ->all();

                $extraInsAmount = TravelPartnerExtraInsurance::find()
                    ->where(['extra_insurance_id' => $this->extraInsurances, 'partner_id' => $partner->id])
                    ->max('coeff');

            }
            else {
                $partnerExtraInsurances=[];
                $extraInsAmount=null;
                $this->extraInsurances = [];
            }

            if($extraInsAmount && count($partnerExtraInsurances) == count($this->extraInsurances)) {
                $has_extra = true;
            } else {
                $extraInsAmount = 1;
                $has_extra = false;
            }

            $partner_purpose = TravelPartnerPurpose::find()->where(['partner_id' => $partner->id, 'purpose_id' => $this->purpose_id])->one();

            $total = 0;            

            if($period_pr_with_covid && ($this->isFamily != 1 || $partner_group_type) && ((!empty($this->extraInsurances) && $has_extra) || (empty($this->extraInsurances) && !$has_extra)) && $partner_purpose) {
                if($this->isFamily == 1) {
                    $total = $period_pr_with_covid->amount * $this->days * $usd * $partner_group_type->coeff * $extraInsAmount;
                } else {
                    foreach($this->travelerBirthdays as $tr) {
                        $amount = $period_pr_with_covid->amount * $this->days * $usd;

                        $age = floor((time() - strtotime($tr)) / 31556926);

                        $age_group = TravelAgeGroup::find()
                            ->where($age . " between from_age and to_age")
                            ->andWhere(['partner_id' => $partner->id])
                            ->one();

                        $total += $amount * $age_group->coeff * $extraInsAmount;
              
                    }
                }

                $amount = $total * $partner_purpose->coeff;

                $partner_product = PartnerProduct::find()->where(['product_id' => 3, 'partner_id' => $partner->id])->one();

                // $amount_with_margin = (($partner_product->percent + 100) / 100) * $amount;
                $amount_with_margin = (($promo_percent + 100) / 100) * $amount;

                $risks = TravelProgramRisk::find()->where(['partner_id' => $partner->id, 'program_id' => $period_pr_with_covid->program_id])->orderBy('risk_id')->all();

                $info = TravelPartnerInfo::find()->where(['partner_id' => $partner->id])->one();

                if(!$info) {
                    $info = new TravelPartnerInfo();
                }

                $result[] = [
                    'partner' => $partner,
                    'program' => $period_pr_with_covid->program,
                    'margin' => $promo_percent,
                    'without_margin' => self::ceiling($amount, 1000),
                    'amount' => self::ceiling($amount_with_margin, 1000),
                    'amount_usd' => round(self::ceiling($amount, 1000) / $usd, 2),
                    'info' => $info,
                    'star' => $partner_product->star
                ];
            }     

            $total = 0;    

            if($period_pr && ($this->isFamily != 1 || $partner_group_type) && ((!empty($this->extraInsurances) && $has_extra) || (empty($this->extraInsurances) && !$has_extra)) && $partner_purpose) {
                if($this->isFamily == 1) {
                    $total = $period_pr->amount * $this->days * $usd * $partner_group_type->coeff * $extraInsAmount;
                } else {
                    foreach($this->travelerBirthdays as $tr) {
                        $amount = $period_pr->amount * $this->days * $usd;

                        $age = floor((time() - strtotime($tr)) / 31556926);

                        $age_group = TravelAgeGroup::find()
                            ->where($age . " between from_age and to_age")
                            ->andWhere(['partner_id' => $partner->id])
                            ->one();

                        $total += $amount * $age_group->coeff * $extraInsAmount;
              
                    }
                }

                $amount = $total * $partner_purpose->coeff;

                $partner_product = PartnerProduct::find()->where(['product_id' => 3, 'partner_id' => $partner->id])->one();

                //$amount_with_margin = (($partner_product->percent + 100) / 100) * $amount;
                $amount_with_margin = (($promo_percent + 100) / 100) * $amount;

                $risks = TravelProgramRisk::find()->where(['partner_id' => $partner->id, 'program_id' => $period_pr->program_id])->orderBy('risk_id')->all();

                $info = TravelPartnerInfo::find()->where(['partner_id' => $partner->id])->one();

                if(!$info) {
                    $info = new TravelPartnerInfo();
                }

                $result[] = [
                    'partner' => $partner,
                    'program' => $period_pr->program,
                    'margin' => $promo_percent,
                    'without_margin' => self::ceiling($amount, 1000),
                    'amount' => self::ceiling($amount_with_margin, 1000),
                    'amount_usd' => round(self::ceiling($amount, 1000) / $usd, 2),
                    'info' => $info,
                    'star' => $partner_product->star
                ];
            }          

        endforeach;


        return $result;
    }    

    public function setGrossPolicyNumber() {
        if($this->trans->status == 2) {
            $last_order = self::find()->where(['partner_id' => 1])->max("policy_order");

            if(is_null($last_order)) {
                $last_order = 0;
            } 

            $this->policy_number = 'NKT ';

            $this->policy_order = $last_order + 1;

            $length = strlen($this->policy_order);
            
            if($length < 7) {
                for($i = 0; $i < 7-$length; $i++) {
                    $this->policy_number .= '0';
                }
                $this->policy_number .= $this->policy_order;
            } else {
                $this->policy_number = $this->policy_order;
            }
            $this->save();      
        }  
    }

    /**
     * Gets query for [[Warehouse]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'f_user_id']);
    }

    public function getAmountUzsWithoutDiscount()
    {
        $amount_uzs = $this->amount_uzs;
        if (!empty($this->promo_amount))
            $amount_uzs = $amount_uzs + $this->promo_amount;

        return $amount_uzs;
    }

    public function send_save_to_partner_system($request_times, $interval_in_seconds = 0, $return_response_body = false)
    {
        if (Travel::find()->where(['id' => $this->id, 'status' => Travel::STATUSES['canceled']])->exists())
            return false;
        $save_returned_success = false;
        for ($i = 0; $i < $request_times; $i++)
        {
            if (Travel::find()->where(['id' => $this->id])->andWhere(['in', 'status', [Travel::STATUSES['canceled']]])->exists())
                return false;
            if (Travel::find()->where(['id' => $this->id])->andWhere(['in', 'status', [Travel::STATUSES['received_policy']]])->exists())
                return true;

            if (strtotime("today midnight") == date_create_from_format('Y-m-d', $this->begin_date)->setTime(0,0,0)->getTimestamp())
            {
                $this->begin_date = date('Y-m-d', strtotime('+1 day'));
                $this->end_date = date('Y-m-d', strtotime('+1 day', strtotime($this->end_date)));
                $this->save();
            }

            if ($this->partner_id == Partner::PARTNER['gross'])
            {
                $response_arr = OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_save_policy'], $this, [
                    "purpose_id" => $this->purpose_id,
                    "program_id" => $this->program_id,
                    "is_family" => ($this->group_type_id == TravelGroupType::GROUP_TYPE['family']) ? 1 : null,
                    "countries" => ArrayHelper::getColumn($this->tCountries, 'code'),
                    "begin_date" => DateHelper::date_format($this->begin_date, 'Y-m-d', 'd.m.Y'),
                    "end_date" => DateHelper::date_format($this->end_date, 'Y-m-d', 'd.m.Y'),
                    "has_covid" => $this->has_covid,
                    "insurer_passport_series" => $this->insurer_passport_series,
                    "insurer_passport_number" => $this->insurer_passport_number,
                    "insurer_birthday" => DateHelper::date_format($this->insurer_birthday, 'Y-m-d', 'd.m.Y'),
                    "insurer_phone" => $this->insurer_phone,
                    "insurer_name" => $this->insurer_name,
                    "travelers" => ArrayHelper::toArray($this->travelMembers, [
                        TravelMember::className() => [
                            'name',
                            'birthday' => function($model){
                                return DateHelper::date_format($model->birthday, 'Y-m-d', 'd.m.Y');
                            },
                            'passport_series',
                            'passport_number'
                        ]
                    ]),
                ], false);
                if (is_array($response_arr) and array_key_exists('response', $response_arr) and isset($response_arr['response']->order_id))
                {
                    $save_returned_success = true;
                    break;
                }
            }elseif ($this->partner_id == Partner::PARTNER['kapital'])
            {
                $applicant = TravelMember::find()->where(['travel_id' => $this->id])->orderBy('birthday desc')->one();

                $response_arr = KapitalSugurtaRequest::sendTravelRequest(KapitalSugurtaRequest::URLS['travel_save'], $this, [
                    "applicant" => [
                        "date_birth" => DateHelper::date_format($applicant->birthday, 'Y-m-d', 'd.m.Y'),
                        "pass_sery" => $applicant->passport_series,
                        "pass_num" => $applicant->passport_number,
                        "first_name" => $applicant->first_name,
                        "last_name" => $applicant->last_name,
                        "pinfl" => $applicant->pinfl,
                        "phone" => Yii::$app->user->identity->phone,
                    ],
                    "activity_id" => KapitalSugurtaRequest::TRAVEL_PURPOSE[$this->purpose_id],
                    "countries" => ArrayHelper::getColumn($this->tCountries, 'kapital_id'),
                    "date_reg" => date('d.m.Y'),
                    "days" => DateHelper::between_days($this->begin_date, $this->end_date, 'Y-m-d'),
                    "end_date" => DateHelper::date_format($this->end_date, 'Y-m-d', 'd.m.Y'),
                    "group_id" => ($this->group_type_id == TravelGroupType::GROUP_TYPE['family']) ? KapitalSugurtaRequest::TRAVEL_GROUP['family'] : KapitalSugurtaRequest::TRAVEL_GROUP['individual'],
                    "insured" => ArrayHelper::toArray($this->travelMembers, [
                        TravelMember::className() => [
                            'date_birth' => function($model){
                                return DateHelper::date_format($model->birthday, 'Y-m-d', 'd.m.Y');
                            },
                            'pass_sery' => function($model){
                                return $model->passport_series;
                            },
                            'pass_num' => function($model){
                                return $model->passport_number;
                            },
                            'first_name',
                            'last_name',
                            'pinfl',
                        ]
                    ]),
                    "program_id" => $this->program_id,
                    "start_date" => DateHelper::date_format($this->begin_date, 'Y-m-d', 'd.m.Y'),
                    "type_id" => KapitalSugurtaRequest::TRAVEL_TYPE['one_time'],
                ], true, [], 'POST');
            }

            sleep($interval_in_seconds);
        }
        if ($save_returned_success)
        {
            $this->order_id_in_gross = $response_arr['response']->order_id;
            $this->policy_number = $response_arr['response']->policy_number;
            $this->policy_pdf_url = $response_arr['response']->url;
            $this->status = self::STATUSES['received_policy'];
            $this->save();

            SMSService::sendMessageAll($this->user->phone, Yii::t('app', "Sug'urta Bozor TRAVEL polis: ") .  $this->policy_pdf_url, $this->user->telegram_chat_ids());
        }

        if ($return_response_body)
            return $response_arr;
        return $save_returned_success;
    }

    public function saveAfterPayed()
    {
        $this->status = self::STATUSES['payed'];
        $this->payed_date = time();
        $this->save();

        $save_returned_success = $this->send_save_to_partner_system(10);
        if(!$save_returned_success)
        {
            $this->status = self::STATUSES['waiting_for_policy'];
            $this->save();
            SMSService::sendMessageAll($this->user->phone, Yii::t('app', "Sug'urta Bozor. POLIS TRAVEL V OCHEREDI. Pri uspeshnom scenarii budet otpravlen v techenie 2 chasov.") , $this->user->telegram_chat_ids());
            Yii::$app->queue1->push(new TravelRequestJob(['travel_id' => $this->id]));
        }

        TelegramService::send($this);
        return $save_returned_success;
    }

    public function statusToBackBeforePayment()
    {
        $warehouse = $this->warehouse;
        if ($warehouse)
        {
            $warehouse->status = '0';
            $warehouse->save();
        }

        $this->status = self::STATUSES['step2'];
        $this->step3_date = null;
        $this->warehouse_id = null;
        $this->agent_amount = null;
        $this->save();
    }

    public static function getShortClientArrCollection($products)
    {
        $programs = KaskoBySubscription::getPrograms();
        $_products = [];
        foreach ($products as $product) {
            $_products[] = $product->getShortClientArr($programs);
        }
        return $_products;
    }

    public function getShortClientArr(){
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'partner' => !is_null($this->partner) ? $this->partner->getForIdNameArr() : null,
            'begin_date' => empty($this->begin_date) ? null : DateHelper::date_format($this->begin_date, 'Y-m-d', 'd.m.Y'),
            'end_date' => empty($this->end_date) ? null : DateHelper::date_format($this->end_date, 'Y-m-d', 'd.m.Y'),
            'status' => $this->status,
            'amount_uzs' => $this->amount_uzs,
            'policy_number' => $this->policy_number,
            'policy_pdf_url' => $this->policy_pdf_url,
            'payed_date' => !empty($this->payed_date) ? date('d.m.Y H:i:s', $this->payed_date) : null,
        ];
    }

    public function getFullClientArr(){
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'countries' => Country::getShortArrCollection($this->tCountries),
            'purpose_id' => (integer)$this->purpose_id,
            'program_id' => (integer)$this->program_id,
            'partner' => !is_null($this->partner) ? $this->partner->getForIdNameArr() : null,
            'begin_date' => empty($this->begin_date) ? null : DateHelper::date_format($this->begin_date, 'Y-m-d', 'd.m.Y'),
            'end_date' => empty($this->end_date) ? null : DateHelper::date_format($this->end_date, 'Y-m-d', 'd.m.Y'),
            'is_family' => $this->group_type_id == TravelGroupType::GROUP_TYPE['family'] ? 1 : 0,
            'has_covid' => (integer)$this->has_covid,
            'amount_uzs' => $this->amount_uzs - $this->promo_amount,
            'price' => $this->price,
            'status' => $this->status,
            'travel_members' => TravelMember::getShortArrCollection($this->travelMembers),
            "promo" => [
                "id" => $this->promo_id,
                "promo_code" => is_null($this->promo) ? null : $this->promo->code,
                "promo_percent" => $this->promo_percent,
                "promo_amount" => $this->promo_amount,
            ],
            'policy_number' => $this->policy_number,
            'policy_pdf_url' => $this->policy_pdf_url,
            'payed_date' => !empty($this->payed_date) ? date('d.m.Y H:i:s', $this->payed_date) : null,
        ];
    }

    public function getFullAdminArr(){
        $purposes = OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_purposes'], new Travel(), ['lang' => "ru"])['response'];
        $purpose_name = "";
        foreach ($purposes as $purpose) {
            if ($purpose->id == $this->purpose_id)
                $purpose_name = $purpose->name;
        }

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'countries' => Country::getShortArrForAdminCollection($this->tCountries),
            'purpose' => [
                'id' => $this->purpose_id,
                'name' => $purpose_name,
            ],
            'program_id' => [
                'id' => $this->program_id,
                'name' => $this->program_name,
            ],
            'partner' => !is_null($this->partner) ? $this->partner->getForIdNameArr() : null,
            'begin_date' => empty($this->begin_date) ? null : DateHelper::date_format($this->begin_date, 'Y-m-d', 'd.m.Y'),
            'end_date' => empty($this->end_date) ? null : DateHelper::date_format($this->end_date, 'Y-m-d', 'd.m.Y'),
            'is_family' => $this->group_type_id == TravelGroupType::GROUP_TYPE['family'] ? 1 : 0,
            'has_covid' => (integer)$this->has_covid,
            'amount_uzs' => $this->amount_uzs - $this->promo_amount,
            'price' => $this->price,
            'status' => $this->status,
            'travel_members' => TravelMember::getShortArrCollection($this->travelMembers),
            "promo" => [
                "id" => $this->promo_id,
                "promo_code" => is_null($this->promo) ? null : $this->promo->code,
                "promo_percent" => $this->promo_percent,
                "promo_amount" => $this->promo_amount,
            ],
            "user" => !is_null($this->user) ? $this->user->getShortArr() : null,
            'policy_number' => $this->policy_number,
            'policy_pdf_url' => $this->policy_pdf_url,
            'payed_date' => !empty($this->payed_date) ? date('d.m.Y H:i:s', $this->payed_date) : null,
        ];
    }
}
