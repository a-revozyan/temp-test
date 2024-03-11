<?php

namespace common\models;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\jobs\AccidentRequestJob;
use common\jobs\BridgeCompanyOsagoRequestJob;
use common\jobs\CheckStatusKapitalAccidentJob;
use common\jobs\NotifyBridgeCompanyJob;
use common\jobs\OsagoRequestJob;
use common\jobs\SendMessageAllJob;
use common\services\fond\FondService;
use common\services\partnerProduct\grossProductService;
use common\services\partnerProduct\partnerProductService;
use common\services\TelegramService;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "osago".
 *
 * @property int $id
 * @property string $uuid
 * @property int $partner_id
 * @property int $f_user_id
 * @property int $autotype_id
 * @property int $citizenship_id
 * @property int $period_id
 * @property int $region_id
 * @property int $number_drivers_id
 * @property string $insurer_name
 * @property string $insurer_address
 * @property string $insurer_phone
 * @property string $insurer_passport_series
 * @property string $insurer_passport_number
 * @property string $insurer_tech_pass_series
 * @property string $insurer_tech_pass_number
 * @property string $insurer_pinfl
 * @property string $passport_file
 * @property string $tech_passport_file_front
 * @property string $tech_passport_file_back
 * @property string $autonumber
 * @property string $address_delivery
 * @property float $amount_uzs
 * @property float $amount_usd
 * @property int $status
 * @property int $created_at
 * @property int $trans_id
 * @property bool $viewed
 * @property int $promo_id
 * @property float $promo_percent
 * @property float $promo_amount
 * @property integer $insurer_birthday
 * @property integer $payed_date
 * @property string $insurer_inn
 * @property boolean $f_user_is_owner
 * @property integer $accident_amount
 *
 * @property UniqueCode $usedUniqueCode
 * @property BridgeCompany $bridgeCompany
 * @property UniqueCode[] $generatedUniqueCodes
 * @property Autotype $autotype
 * @property Citizenship $citizenship
 * @property NumberDrivers $numberDrivers
 * @property Partner $partner
 * @property Promo $promo
 * @property Period $period
 * @property Region $region
 * @property Transaction $trans
 * @property OsagoDriver[] $drivers
 * @property User $user
 * @property Accident $accident
 * @property boolean $applicant_is_driver
 * @property integer $order_id_in_gross
 * @property string $policy_number
 * @property string $policy_pdf_url
 * @property string $begin_date
 * @property string $end_date
 * @property string $insurer_license_series
 * @property string $insurer_license_number
 * @property string $is_juridic
 * @property int $unique_code_id
 * @property boolean|null $owner_with_accident
 * @property bool $created_in_telegram
 * @property integer $gross_auto_id
 * @property string|null $insurer_license_given_date
 * @property string|null $bridge_company_id
 * @property integer|null $partner_ability
 */
class Osago extends \yii\db\ActiveRecord
{
    public $passFile;
    public $techPassFileFront;
    public $techPassFileBack;
    public $promo_code;
    public $code;

    public const PARTNER_ABILITY = [
        'without_kapital' => -1,
        'without_gross' => 1,
        'without_gross_and_kapital' => 2,
        'does_not_metter' => 0,
    ];

    public function fields()
    {
        $lang = GeneralHelper::lang_of_local();
        $fields = parent::fields();
        $fields['insurer_birthday'] = function ($model){
            if (is_null($this->insurer_birthday))
                return null;
            return date('d.m.Y', $model->insurer_birthday);
        };
        $fields['numberDrivers'] = function ($model) use($lang){
            return $model->numberDrivers->{'name_' . $lang} ?? null;
        };
        $fields['drivers'] = function ($model){
            return $model->drivers;
        };
        $fields['partner'] = function ($model){
            return [
                'name' => $model->partner->name ?? null
            ];
        };

        $fields['accident_policy_pdf_url'] = fn ($model) => !is_null($model->accident) ? $model->accident->policy_pdf_url : null;
        $fields['accident_policy_number'] = fn($model) => !is_null($model->accident) ? $model->accident->policy_number : null;
        $fields[] = 'accident_amount';

        return $fields;
    }

    const STATUS = [
        'step1' => 1,
        'step2' => 2,
        'step3' => 3,
        'step4' => 4, //generate payment link
        'payed' => 6,
        'waiting_for_policy' => 7,
        'received_policy' => 8,
        'canceled' => 9,
        'stranger' => 10,
        'step11' => 11, //choose partner
    ];

    const CODE = [
        'incorrect_pinfl' => 1,
    ];

    const FRONT_ERROR_CODE = [
        'driver_info_not_found_in_fond' => 4,
        'driver_license_not_found_in_fond' => 5,
        'applicant_license_not_found_in_fond' => 8,
        'owner_info_not_found_in_fond' => -9,
        'pinfl_required' => 1,
    ];

    const NO_LIMIT_NUMBER_DRIVERS_ID = 1;  //bez agranicheniya
    const TILL_5_NUMBER_DRIVERS_ID = 4;  //5 nafargacha
    const WITH_RESTRICTION_NUMBER_DRIVERS_ID = 4;  //c agranicheniya
    const DEFAULT_PERIOD_ID = 1; //12 months
    const PERIOD_6_ID = 2; //6 months
    const REGION_TASHKENT_ID = 1;
    const AUTONUMBER_TASHKENT_CODES = ['01', '10'];
    const REGION_ANOTHER_ID = 2;
    const DEFAULT_APPLICANT_IS_DRIVER = 1;

    const KAPITAL_SUGURTA_NUMBER_DRIVERS_ID = [
        self::NO_LIMIT_NUMBER_DRIVERS_ID => 0,
        self::TILL_5_NUMBER_DRIVERS_ID => 1,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'osago';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['f_user_is_owner', 'insurer_birthday', 'insurer_passport_series', 'insurer_passport_number', 'insurer_inn', 'payed_date', 'created_in_telegram', 'accident_amount', 'owner_with_accident', 'comment', 'insurer_license_given_date'], 'safe'],
            [['insurer_tech_pass_series', 'insurer_tech_pass_number',  'autonumber', 'status', 'created_at'], 'required', 'message' => Yii::t('app', 'Необходимо заполнить')],
            [['passFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, pdf, JPEG'],
            [['techPassFileFront'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, pdf, JPEG'],
            [['techPassFileBack'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, pdf, JPEG'],
            [['partner_id', 'autotype_id', 'citizenship_id', 'period_id', 'region_id', 'number_drivers_id', 'status', 'created_at'], 'default', 'value' => null],
            [['partner_id', 'autotype_id', 'citizenship_id', 'period_id', 'region_id', 'number_drivers_id', 'status', 'created_at', 'trans_id', 'promo_id', 'is_juridic', 'unique_code_id', 'reason_id', 'gross_auto_id', 'bridge_company_id', 'partner_ability'], 'integer'],
            [['amount_uzs', 'amount_usd', 'promo_percent', 'promo_amount'], 'number'],
            [['insurer_name', 'insurer_address', 'insurer_phone', 'insurer_passport_series', 'insurer_passport_number', 'insurer_tech_pass_series', 'insurer_tech_pass_number', 'insurer_pinfl', 'autonumber', 'address_delivery', 'promo_code', 'uuid'], 'string', 'max' => 255],
            //[['autotype_id'], 'exist', 'skipOnError' => true, 'targetClass' => Autotype::className(), 'targetAttribute' => ['autotype_id' => 'id']],
            [['citizenship_id'], 'exist', 'skipOnError' => true, 'targetClass' => Citizenship::className(), 'targetAttribute' => ['citizenship_id' => 'id']],
            [['number_drivers_id'], 'exist', 'skipOnError' => true, 'targetClass' => NumberDrivers::className(), 'targetAttribute' => ['number_drivers_id' => 'id']],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['period_id'], 'exist', 'skipOnError' => true, 'targetClass' => Period::className(), 'targetAttribute' => ['period_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'uuid' => Yii::t('app', 'UUID'),
            'partner_id' => Yii::t('app', 'Partner ID'),
            'autotype_id' => Yii::t('app', 'Autotype ID'),
            'citizenship_id' => Yii::t('app', 'Citizenship ID'),
            'period_id' => Yii::t('app', 'Period ID'),
            'region_id' => Yii::t('app', 'Region ID'),
            'number_drivers_id' => Yii::t('app', 'Number Drivers ID'),
            'insurer_name' => Yii::t('app', 'Insurer Name'),
            'insurer_address' => Yii::t('app', 'Insurer Address'),
            'insurer_phone' => Yii::t('app', 'Insurer Phone'),
            'insurer_passport_series' => Yii::t('app', 'Insurer Passport Series'),
            'insurer_passport_number' => Yii::t('app', 'Insurer Passport Number'),
            'insurer_tech_pass_series' => Yii::t('app', 'Insurer Tech Pass Series'),
            'insurer_tech_pass_number' => Yii::t('app', 'Insurer Tech Pass Number'),
            'insurer_pinfl' => Yii::t('app', 'Insurer Pinfl'),
            'autonumber' => Yii::t('app', 'Autonumber'),
            'amount_uzs' => Yii::t('app', 'Amount Uzs'),
            'amount_usd' => Yii::t('app', 'Amount Usd'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'is_juridic' => Yii::t('app', 'Is Juridic'),
        ];
    }

    public function beforeSave($insert)
    {
        StatusHistory::create($this);
        return parent::beforeSave($insert);
    }

    public function uploadPass()
    {
        if ($this->validate()) {
            $this->passFile->saveAs('uploads/passport_files/osago/' . $this->passport_file);
            return true;
        } else {
            return false;
        }
    }

    public function uploadTechPassFront()
    {
        if ($this->validate()) {
            $this->techPassFileFront->saveAs('uploads/passport_files/osago/' . $this->tech_passport_file_front);
            return true;
        } else {
            return false;
        }
    }

    public function uploadTechPassBack()
    {
        if ($this->validate()) {
            $this->techPassFileBack->saveAs('uploads/passport_files/osago/' . $this->tech_passport_file_back);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets query for [[Autotype]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutotype()
    {
        return $this->hasOne(Autotype::className(), ['id' => 'autotype_id']);
    }

    public function getOsagoFondData()
    {
        return $this->hasOne(OsagoFondData::className(), ['osago_id' => 'id']);
    }

    /**
     * Gets query for [[Citizenship]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCitizenship()
    {
        return $this->hasOne(Citizenship::className(), ['id' => 'citizenship_id']);
    }

    /**
     * Gets query for [[NumberDrivers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNumberDrivers()
    {
        return $this->hasOne(NumberDrivers::className(), ['id' => 'number_drivers_id']);
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

    public function getPromo()
    {
        return $this->hasOne(Promo::className(), ['id' => 'promo_id']);
    }

    /**
     * Gets query for [[Period]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeriod()
    {
        return $this->hasOne(Period::className(), ['id' => 'period_id']);
    }

    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
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

    public function getDrivers()
    {
        return $this->hasMany(OsagoDriver::className(), ['osago_id' => 'id'])
            ->where(['osago_driver.status' => [OsagoDriver::STATUS['verified'], null]]);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'f_user_id']);
    }

    public function getBridgeCompany()
    {
        return $this->hasOne(BridgeCompany::className(), ['id' => 'bridge_company_id']);
    }

    public function getAccident()
    {
        return $this->hasOne(Accident::className(), ['osago_id' => 'id']);
    }

    public function getUsedUniqueCode()
    {
        return $this->hasOne(UniqueCode::className(), ['id' => 'unique_code_id']);
    }

    public function getGeneratedUniqueCodes()
    {
        return $this->hasMany(UniqueCode::className(), ['clonable_id' => 'id']);
    }

    public function getReason()
    {
        return $this->hasOne(Reason::className(), ['id' => 'reason_id']);
    }

    public function getStatuHistories()
    {
        return $this->hasMany(StatusHistory::className(), ['model_id' => 'id'])->where(['model_class' => self::className()]);
    }

    public function calc()
    {
        $amount = OsagoAmount::find()->one();

        if(!is_null($this->autotype_id) && !is_null($this->period_id) && !is_null($this->region_id) && !is_null($this->number_drivers_id)) {
            $this->amount_uzs = $this->autotype->coeff * $this->period->coeff * $this->region->coeff * $this->numberDrivers->coeff * $amount->insurance_premium;
        } else {
            $this->amount_uzs = null;
        }
    }

    public function get_use_territory()
    {
        $use_territory = null;
        $region_code = (int)substr($this->autonumber, 0, 2);
        if ($region_code >= 1 and $region_code <= 9)
            $use_territory = 1;
        elseif($region_code >= 10 and $region_code <= 19)
            $use_territory = 2;
        elseif($region_code >= 20 and $region_code <= 24)
            $use_territory = 11;
        elseif($region_code >= 25 and $region_code <= 29)
            $use_territory = 5;
        elseif($region_code >= 30 and $region_code <= 39)
            $use_territory = 10;
        elseif($region_code >= 40 and $region_code <= 49)
            $use_territory = 13;
        elseif($region_code >= 50 and $region_code <= 59)
            $use_territory = 9;
        elseif($region_code >= 60 and $region_code <= 69)
            $use_territory = 3;
        elseif($region_code >= 70 and $region_code <= 74)
            $use_territory = 6;
        elseif($region_code >= 75 and $region_code <= 79)
            $use_territory = 12;
        elseif($region_code >= 80 and $region_code <= 84)
            $use_territory = 4;
        elseif($region_code >= 85 and $region_code <= 89)
            $use_territory = 8;
        elseif($region_code >= 90 and $region_code <= 94)
            $use_territory = 14;
        elseif($region_code >= 95 and $region_code <= 99)
            $use_territory = 7;

        return $use_territory;
    }

    public function getAmountUzs($get_auto_from_gross = true)
    {
        $amount = null;

        if ($get_auto_from_gross)
            $response_array = FondService::getAutoInfo($this->insurer_tech_pass_series, $this->insurer_tech_pass_number, $this->autonumber, true, $this);
        else
            $response_array = [
                'VEHICLE_TYPE_ID' => $this->autotype_id,
                'USE_TERRITORY' => $this->get_use_territory(),
            ];

//        $response_array = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['calc'], $this, [
//            'vehicle' => $response_array['VEHICLE_TYPE_ID'],
//            'use_territory' => $response_array['USE_TERRITORY'],
//            'period' => KapitalSugurtaRequest::PERIOD[$this->period_id],
//            'driver_limit' => KapitalSugurtaRequest::DRIVER_LIMIT[$this->number_drivers_id],
//            'discount' => 1,
//        ],true, [], 'post');
//        if (is_array($response_array) and array_key_exists('prem', $response_array))
//            $amount = $response_array['prem'];

        $osago_price = OsagoPrice::find()->where([
            'vehicle' => $response_array['VEHICLE_TYPE_ID'],
            'use_territory' => $response_array['USE_TERRITORY'],
            'period' => KapitalSugurtaRequest::PERIOD[$this->period_id],
            'driver_limit' => KapitalSugurtaRequest::DRIVER_LIMIT[$this->number_drivers_id],
            'discount' => 1,
        ])->orderBy('updated_at desc')->one();

        if (empty($osago_price) or empty($osago_price->amount))
            throw new NotFoundHttpException('osago price not found');

        $amount = $osago_price->amount;

        if (!empty($this->usedUniqueCode))
            $amount = round($amount * (100 + $this->usedUniqueCode->discount_percent) / 100, 2);

        if (!empty($this->id) and !empty($this->promo) and $this->promo->amount_type == Promo::AMOUNT_TYPE['percent'])
        {
            $promo_amount = (($amount + $this->accident_amount) * $this->promo->amount/100);
            $this->promo_amount = $promo_amount;
        }
        $amount -= $this->promo_amount;

        return $amount;
    }

    public function getIsJuridic()
    {
        if (
            strlen($this->autonumber) == 8
            and is_numeric($this->autonumber[0])
            and is_numeric($this->autonumber[1])
            and is_numeric($this->autonumber[2])
            and is_numeric($this->autonumber[3])
            and is_numeric($this->autonumber[4])
            and is_string($this->autonumber[5])
            and is_string($this->autonumber[6])
            and is_string($this->autonumber[7])
        )
            return 1;
        else
            return 0;

        $request_body = [
            "gos_number" => $this->autonumber,
            "tech_sery" => $this->insurer_tech_pass_series,
            "tech_number" => $this->insurer_tech_pass_number,
        ];
        $response_array = OsagoRequest::sendRequest(OsagoRequest::URLS['is_juridic'], $this, $request_body);
        if (is_array($response_array) and array_key_exists('result', $response_array) and $response_array['result'])
            return $response_array['response'] ?? null;
    }

    public function getAutoInfo($throw_error = false)
    {
        if (!$auto_info = FondService::getAutoInfo($this->insurer_tech_pass_series, $this->insurer_tech_pass_number, $this->autonumber, $throw_error, $this))
            return false;

        $gross_auto = GrossAuto::findOne(['name' => trim($auto_info['MODEL_NAME'] ?? '')]);
        if (is_null($gross_auto))
        {
            $gross_auto = new GrossAuto();
            $gross_auto->name = trim($auto_info['MODEL_NAME'] ?? '');
            $gross_auto->created_at = date('Y-m-d H:i:s');
            $gross_auto->save();
        }
        $this->gross_auto_id = $gross_auto->id;

        $osago_fond_data = OsagoFondData::find()->where(['osago_id' => $this->id])->one();
        if (empty($osago_fond_data))
        {
            $osago_fond_data = new OsagoFondData();
            $osago_fond_data->osago_id = $this->id;
        }

        $osago_fond_data->marka_id = $auto_info['MARKA_ID'];
        $osago_fond_data->model_id = $auto_info['MODEL_ID'];
        $osago_fond_data->model_name = $auto_info['MODEL_NAME'];
        $osago_fond_data->orgname = $auto_info['ORGNAME'];
        $osago_fond_data->vehicle_type_id = $auto_info['VEHICLE_TYPE_ID'];
        $osago_fond_data->tech_passport_issue_date = $auto_info['TECH_PASSPORT_ISSUE_DATE'];
        $osago_fond_data->issue_year = $auto_info['ISSUE_YEAR'];
        $osago_fond_data->body_number = $auto_info['BODY_NUMBER'];
        $osago_fond_data->engine_number = $auto_info['ENGINE_NUMBER'];
        $osago_fond_data->use_territory = $auto_info['USE_TERRITORY'];
        $osago_fond_data->fy = $auto_info['FY'];
        $osago_fond_data->last_name_latin = $auto_info['LAST_NAME'];
        $osago_fond_data->first_name_latin = $auto_info['FIRST_NAME'];
        $osago_fond_data->middle_name_latin = $auto_info['MIDDLE_NAME'];
        $osago_fond_data->save();

        if (!empty($auto_info['ORGNAME']))
            $this->insurer_name = $auto_info['ORGNAME'];

        $birthday = DateHelper::birthday_from_pinfl($auto_info['PINFL'], false);
        if (!empty($auto_info['PINFL']) and !empty($birthday))
        {
            $this->insurer_pinfl = $auto_info['PINFL'];
            $this->insurer_birthday = date_create_from_format('d.m.Y', $birthday)->getTimestamp();
        }

        if (!empty($auto_info['INN']))
            $this->insurer_inn = $auto_info['INN'];
        if (!empty($auto_info['VEHICLE_TYPE_ID']))
            $this->autotype_id = $auto_info['VEHICLE_TYPE_ID'];

        $this->save();

        return $auto_info;
    }

    public function getAutoAndOwnerInfo($throw_error = false)
    {
        $auto_info = $this->getAutoInfo($throw_error);
//        if (empty($auto_info['PINFL']))
            return [
                'auto_info' => $auto_info,
                'person_info' => false,
            ];

        $person_info = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['person_info_by_pinfl'] . $auto_info['PINFL'], $this, [], $throw_error);

        if (!is_array($person_info) or array_key_exists('error', $person_info))
        {
            $this->code = self::CODE['incorrect_pinfl'];
            return [
                'auto_info' => $auto_info,
                'person_info' => false,
            ];
        }

        if (!empty($person_info['address']))
            $this->insurer_address = $person_info['address'];
        if (!empty($person_info['passSeries']))
            $this->insurer_passport_series = $person_info['passSeries'];
        if (!empty($person_info['passNumber']))
            $this->insurer_passport_number = $person_info['passNumber'];
        $this->insurer_inn = $person_info['inn'] ?? null;

        $this->save();

        return [
            'auto_info' => $auto_info,
            'person_info' => $person_info,
        ];
    }

    /**
     * @throws \Exception
     */
    public function getFullInfoFromKapital($throw_error = false)
    {
        $auto_and_owner_info = $this->getAutoAndOwnerInfo($throw_error);

        ['auto_info' => $auto_info, 'person_info' => $person_info] = $auto_and_owner_info;

        $insurer_passport_series = !empty($person_info['passSeries']) ? $person_info['passSeries'] : $this->insurer_passport_series;
        $insurer_passport_number = !empty($person_info['passNumber']) ? $person_info['passNumber'] : $this->insurer_passport_number;

        $driver_info = FondService::getDriverInfoByPinfl($auto_info['PINFL'], $insurer_passport_series, $insurer_passport_number, $throw_error, $this);

        if (!$driver_info)
            return [
                'auto_info' => $auto_info,
                'person_info' => $person_info,
                'driver_info' => false,
            ];

        if (!empty($driver_info['BIRTH_DATE']))
            $this->insurer_birthday = $driver_info['BIRTH_DATE'];
        if (!empty($driver_info['LICENSE_SERIA']))
            $this->insurer_license_series = $driver_info['LICENSE_SERIA'];
        if (!empty($driver_info['LICENSE_NUMBER']))
            $this->insurer_license_number = $driver_info['LICENSE_NUMBER'];
        $this->save();

        return [
            'auto_info' => $auto_info,
            'person_info' => $person_info,
            'driver_info' => $driver_info,
        ];
    }
    public function create_osago_in_partner_system()
    {
        return partnerProductService::osagoSave($this);
    }

    public function partner_payment()
    {
        if ($response_arr = partnerProductService::osagoConfirm($this))
        {
            $this->policy_number = $response_arr['policy_number'];
            $this->policy_pdf_url = $response_arr['policy_pdf_url'];
            $this->begin_date = $response_arr['begin_date'];
            $this->end_date = $response_arr['end_date'];
            $this->status = self::STATUS['received_policy'];

            $this->save();
        }

        if ($this->status == self::STATUS['received_policy'])
        {
            TelegramService::send($this);
            if (empty($this->bridge_company_id))
                Yii::$app->queue1->push(new SendMessageAllJob([
                    'phone' => $this->user->phone,
                    'message' => Yii::t('app', "Sug'urta Bozor OSAGO polis: ") .  $this->policy_pdf_url,
                    'telegram_chat_ids' => $this->user->telegram_chat_ids()
                ]));
            elseif(!in_array($this->bridge_company_id, [BridgeCompany::BRIDGE_COMPANY['paynet']]))
                Yii::$app->queue1->push(new NotifyBridgeCompanyJob(['osago_id' => $this->id]));
        }

        return $response_arr;
    }

    /**
     * @throws BadRequestHttpException
     */
    public function get_policy_from_partner()
    {
        if (Osago::find()->where(['id' => $this->id])->andWhere(['in', 'status', [Osago::STATUS['canceled']]])->exists())
            return false;
        if (Osago::find()->where(['id' => $this->id])->andWhere(['in', 'status', [Osago::STATUS['received_policy']]])->exists())
            return true;

        if ($this->partner_id == Partner::PARTNER['kapital'])
            throw new BadRequestHttpException('you must pay to kapital to generate policy from kapital');

        if ($order_id = $this->create_osago_in_partner_system())
        {
            $this->order_id_in_gross = $order_id;
            $this->save();

            $response_arr = $this->partner_payment();

            $osago_drivers = OsagoDriver::find()->where(['osago_id' => $this->id, 'with_accident' => true])->all();
            if (count($osago_drivers) > 0 or $this->owner_with_accident)
            {
                $accident = (new Accident())->save_accident_from_osago($this, $osago_drivers);
                Yii::$app->queue1->push(new AccidentRequestJob(['accident_id' => $accident->id]));
            }

        }

        return $response_arr;
    }

    public function saveAfterPayed()
    {
        if ($this->partner_id == Partner::PARTNER['kapital'])
            return true;

        $this->status = self::STATUS['payed'];
        $this->payed_date = time();
        $this->save();

        if (!empty($this->bridge_company_id))
            Yii::$app->queue1->push(new BridgeCompanyOsagoRequestJob(['osago_id' => $this->id]));
        else
            Yii::$app->queue1->push(new OsagoRequestJob(['osago_id' => $this->id]));

        return true;
    }

    public function statusToBackBeforePayment()
    {
        $this->status = self::STATUS['canceled'];
        $this->save();
    }

    public function setAccidentAmount($save = true)
    {
        $insurer_count = OsagoDriver::find()->where(['osago_id' => $this->id, 'with_accident' => true])->count();
        if ($this->owner_with_accident)
            $insurer_count++;

        $discount = 0;
        if (!empty($this->usedUniqueCode))
            $discount = $this->usedUniqueCode->discount_percent;

        $this->accident_amount = round(Accident::getAccidentAmount($insurer_count, $this->partner_id) * (100 + $discount) / 100, 2);

        if ($save)
            $this->save();
    }

    public function getAmountUzsWithoutDiscount()
    {
        $amount_uzs = $this->amount_uzs;
        if (!empty($this->usedUniqueCode))
            $amount_uzs = round($amount_uzs * 100 / (100 + $this->usedUniqueCode->discount_percent), 2);
        if (!empty($this->promo_amount))
            $amount_uzs = $amount_uzs + $this->promo_amount;

        return $amount_uzs;
    }

    public function getAccidentAmountWithoutDiscount()
    {
        $accident_amount = $this->accident_amount;
        if (!empty($this->usedUniqueCode))
            $accident_amount = round($accident_amount * 100 / (100 + $this->usedUniqueCode->discount_percent), 2);

        return $accident_amount;
    }

    public static function getShortClientArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getShortClientArr();
        }

        return $_models;
    }

    public function getShortClientArr()
    {
        return [
            "id" => $this->id,
            "uuid" => $this->uuid,
            "autonumber" => $this->autonumber,
            "payed_date" => $this->payed_date == null ? null : date('d.m.Y H:i', $this->payed_date),
            "begin_date" => ($this->begin_date == null) ? null : DateHelper::date_format($this->begin_date, 'Y-m-d', 'd.m.Y'),
            "end_date" => ($this->end_date == null) ? null : DateHelper::date_format($this->end_date, 'Y-m-d', 'd.m.Y'),
            "policy_pdf_url" => $this->policy_pdf_url,
            "policy_number" => $this->policy_number,
            "status" => $this->status,
        ];
    }

    public function getFullClientArr()
    {
        $amount_uzs = $this->getAmountUzsWithoutDiscount();

        $accident_amount = $this->getAccidentAmountWithoutDiscount();

        $partner = !empty($this->partner) ? $this->partner->getForIdNameAccidentArr() : null;
        if ($partner and $partner['accident'])
            $partner['accident']['in_step3'] = 1;
        if ($partner and $partner['accident'] and ($partner['id'] == Partner::PARTNER['kapital'] or $this->number_drivers_id == Osago::NO_LIMIT_NUMBER_DRIVERS_ID))
            $partner['accident']['in_step3'] = 0;
        if ($partner and $partner['accident'] and $partner['id'] == Partner::PARTNER['kapital'] and !($this->number_drivers_id == Osago::TILL_5_NUMBER_DRIVERS_ID and $this->region_id == Osago::REGION_TASHKENT_ID))
            $partner['accident']['required'] = 0;

        return [
            "id" => $this->id,
            "uuid" => $this->uuid,
            "period" => !is_null($this->period) ? $this->period->getShortArr() : null,
            "region" => !is_null($this->region) ? $this->region->getShortArr() : null,
            "autonumber" => $this->autonumber,
            "amount_uzs" => $amount_uzs,
            "status" => $this->status,
            "f_user_is_owner" => (bool)$this->f_user_is_owner,
            "owner_with_accident" => (bool)$this->owner_with_accident,
            "payed_date" => empty($this->payed_date) ? null : date('Y-m-d H:i:s', $this->payed_date),
            "policy_pdf_url" => $this->policy_pdf_url,
            "policy_number" => $this->policy_number,
            "applicant_is_driver" => (bool)$this->applicant_is_driver,
            "begin_date" => $this->begin_date,
            "end_date" => $this->end_date,
            "numberDrivers" => !empty($this->numberDrivers) ? $this->numberDrivers->getShortArr() : [],
            "drivers" => OsagoDriver::getShortArrCollection($this->drivers),
            "partner" => $partner,
            "is_juridic" => $this->is_juridic,
            'accident_policy_pdf_url' => !is_null($this->accident) ? $this->accident->policy_pdf_url : null,
            'accident_policy_number' => !is_null($this->accident) ? $this->accident->policy_number : null,
            'accident_amount' => $accident_amount,
            'insurer_name' => "",
            'insurer_passport_series' => $this->insurer_passport_series,
            'insurer_passport_number' => $this->insurer_passport_number,
            'insurer_license_series' => $this->insurer_license_series,
            'insurer_license_number' => $this->insurer_license_number,
            'insurer_license_given_date' => !empty($this->insurer_license_given_date) ?  DateHelper::date_format($this->insurer_license_given_date, 'Y-m-d', 'd.m.Y') : null,
            'insurer_tech_pass_series' => $this->insurer_tech_pass_series,
            'insurer_tech_pass_number' => $this->insurer_tech_pass_number,
            "insurer_birthday" => empty($this->insurer_birthday) ? null :date('Y-m-d', $this->insurer_birthday),
            "insurer_inn" => $this->insurer_inn,
            "promo" => [
                "id" => $this->promo_id,
                "promo_code" => is_null($this->promo) ? null : $this->promo->code,
                "promo_percent" => $this->promo_percent,
                "promo_amount" => $this->promo_amount,
            ],
            "used_unique_code" => (!is_null($this->usedUniqueCode) and $this->partner_id != Partner::PARTNER['kapital']) ? $this->usedUniqueCode->getShortArr() : null,
            "code" => $this->code,
            "partner_ability" => $this->partner_ability,
            "insurer_pinfl" => $this->insurer_pinfl,
        ];
    }

    public function getFullAdminArr()
    {
        return [
            "id" => $this->id,
            "uuid" => $this->uuid,
            "period" => !is_null($this->period) ? $this->period->getShortInRuArr() : null,
            "region" => !is_null($this->region) ? $this->region->getShortInRuArr() : null,
            "autonumber" => $this->autonumber,
            "amount_uzs" => $this->amount_uzs,
            "status" => $this->status,
            "promo" => [
                "id" => $this->promo_id,
                "promo_code" => is_null($this->promo) ? null : $this->promo->code,
                "promo_percent" => $this->promo_percent,
                "promo_amount" => $this->promo_amount,
            ],
            "f_user_is_owner" => $this->f_user_is_owner,
            "payed_date" => $this->payed_date,
            "policy_pdf_url" => $this->policy_pdf_url,
            "policy_number" => $this->policy_number,
            "applicant_is_driver" => $this->applicant_is_driver,
            "begin_date" => $this->begin_date,
            "end_date" => $this->end_date,
            "numberDrivers" => !empty($this->numberDrivers) ? $this->numberDrivers->getShortInRuArr() : [],
            "drivers" => OsagoDriver::getShortArrCollection($this->drivers),
            "partner" => $this->partner->getForIdNameArr(),
            "created_at" => $this->created_at,
            "is_juridic" => $this->is_juridic,
            "user" => !is_null($this->user) ? $this->user->getShortArr() : null,
            'created_in_telegram' => $this->created_in_telegram,
            "owner_with_accident" => $this->owner_with_accident,
            "accident_amount" => $this->accident_amount,
            'accident_policy_pdf_url' => !is_null($this->accident) ? $this->accident->policy_pdf_url : null,
            'accident_policy_number' => !is_null($this->accident) ? $this->accident->policy_number : null,
            'insurer_passport_series' => $this->insurer_passport_series,
            'insurer_passport_number' => $this->insurer_passport_number,
            'insurer_license_given_date' => !empty($this->insurer_license_given_date) ?  DateHelper::date_format($this->insurer_license_given_date, 'Y-m-d', 'd.m.Y') : null,
            'insurer_license_series' => $this->insurer_license_series,
            'insurer_license_number' => $this->insurer_license_number,
            'insurer_tech_pass_series' => $this->insurer_tech_pass_series,
            'insurer_tech_pass_number' => $this->insurer_tech_pass_number,
            "used_unique_code" => !is_null($this->usedUniqueCode) ? $this->usedUniqueCode->getShortArr() : null,
            "generated_unique_codes" => !empty($this->generatedUniqueCodes) ? UniqueCode::getShortArrCollection($this->generatedUniqueCodes) : [],
            "payment_type" => !is_null($this->trans) ? $this->trans->payment_type : null,
            "insurer_pinfl" => $this->insurer_pinfl,
        ];
    }
}
