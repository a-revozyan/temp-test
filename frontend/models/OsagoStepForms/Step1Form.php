<?php

namespace frontend\models\OsagoStepForms;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\BridgeCompany;
use common\models\Osago;
use common\models\OsagoFondData;
use common\models\Partner;
use common\models\Promo;
use common\services\fond\FondService;
use common\services\TelegramService;
use thamtech\uuid\helpers\UuidHelper;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class Step1Form extends \yii\base\Model
{
    public $insurer_tech_pass_series;
    public $insurer_tech_pass_number;
    public $autonumber;
    public $osago_uuid;
    public $data_check_string;

    public $insurer_passport_series;
    public $insurer_passport_number;

    public $insurer_birthday;
    public $insurer_pinfl;
    public $insurer_inn;

    public $promo_code;

    public $super_agent_key;

    public function rules()
    {
        return [
            [['insurer_tech_pass_series', 'insurer_tech_pass_number', 'autonumber'], 'required'],
            [['insurer_tech_pass_series', 'insurer_tech_pass_number', 'autonumber', 'osago_uuid', 'insurer_passport_series', 'insurer_passport_number', 'super_agent_key', 'insurer_pinfl'], 'string'],
            [['osago_uuid'], UuidValidator::className()],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['not in', 'status', [
                        Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']
                    ]]);
            }],
            [['data_check_string', 'insurer_inn', 'promo_code'], 'safe'],
            [['insurer_birthday'], 'date', 'format' => 'php:d.m.Y'],
            [['promo_code'], 'exist', 'skipOnError' => true, 'targetClass' => Promo::className(), 'targetAttribute' => ['promo_code' => 'code'], 'filter' => function($query){
                return $query->andWhere(['status' => Promo::STATUS['active'], 'type' => Promo::TYPE['unique_link']]);
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'insurer_tech_pass_series' => Yii::t('app', 'insurer_tech_pass_series'),
            'insurer_tech_pass_number' => Yii::t('app', 'insurer_tech_pass_number'),
            'insurer_passport_series' => Yii::t('app', 'insurer_passport_series'),
            'insurer_passport_number' => Yii::t('app', 'insurer_passport_number'),
            'autonumber' => Yii::t('app', 'autonumber'),
            'osago_uuid' => Yii::t('app', 'osago_uuid'),
            'data_check_string' => Yii::t('app', 'data_check_string'),
        ];
    }

    public function save()
    {
        if (empty($this->insurer_passport_series))
            $this->insurer_passport_series = null;
        if (empty($this->insurer_passport_number))
            $this->insurer_passport_number = null;
        if (empty($this->insurer_birthday))
            $this->insurer_birthday = null;
        if (empty($this->insurer_pinfl))
            $this->insurer_pinfl = null;
        if (empty($this->insurer_inn))
            $this->insurer_inn = null;

        $osago = $this->osago_uuid ? Osago::findOne(['uuid' => $this->osago_uuid]) : new Osago();

        if ($bridge_company = BridgeCompany::findOne(['code' => $this->super_agent_key]))
            $osago->bridge_company_id = $bridge_company->id;

        if (!empty($osago->bridge_company_id) and $fuser = GeneralHelper::fUser())
            $osago->f_user_id = $fuser->id;

        if (!empty($osago->created_at) and !empty($this->insurer_passport_number))
        {
            $osago_fond_data = OsagoFondData::find()->where(['osago_id' => $osago->id])->one();
            $this->insurer_passport_series = strtoupper($this->insurer_passport_series);

            if (!empty($osago->bridge_company_id))
                $osago->partner_ability = Osago::PARTNER_ABILITY['without_kapital'];

            if (!empty($this->insurer_pinfl))
                $osago->insurer_pinfl = $this->insurer_pinfl;
            if (!empty($this->insurer_birthday))
                $osago->insurer_birthday = date_create_from_format('d.m.Y', $this->insurer_birthday)->getTimestamp();

            $birthday = null;
            if (!empty($osago->insurer_birthday))
                $birthday = date('d.m.Y', $osago->insurer_birthday);

            $driver_info = FondService::getDriverInfoByPinflOrBirthday($this->insurer_passport_series, $this->insurer_passport_number, $osago->insurer_pinfl, $birthday,false, $osago);

            $without_gross_condition = (
                    empty($driver_info['LAST_NAME_LATIN'])
                    and empty($driver_info['FIRST_NAME_LATIN'])
                    and empty($driver_info['MIDDLE_NAME_LATIN'])
                    and empty($driver_info['OBLAST'])
                    and empty($driver_info['RAYON'])
                    and empty($driver_info['BIRTH_DATE'])
            );

            if ($without_gross_condition)
                $osago->partner_ability = Osago::PARTNER_ABILITY['without_gross'];

            if (!empty($osago->bridge_company_id) and $without_gross_condition)
                $osago->partner_ability = Osago::PARTNER_ABILITY['without_gross_and_kapital'];

            if (!empty($osago_fond_data) and !empty($driver_info))
            {
                if (!empty($driver_info['LAST_NAME_LATIN']))
                    $osago_fond_data->last_name_latin = $driver_info['LAST_NAME_LATIN'];
                if (!empty($driver_info['FIRST_NAME_LATIN']))
                    $osago_fond_data->first_name_latin = $driver_info['FIRST_NAME_LATIN'];
                if (!empty($driver_info['MIDDLE_NAME_LATIN']))
                    $osago_fond_data->middle_name_latin = $driver_info['MIDDLE_NAME_LATIN'];
                $osago_fond_data->oblast = $driver_info['OBLAST'];
                $osago_fond_data->rayon = $driver_info['RAYON'];
                $osago_fond_data->ispensioner = $driver_info['ISPENSIONER'] ?? null;
                $osago_fond_data->save();
            }

            if (
                empty($osago_fond_data->last_name_latin)
                or empty($osago_fond_data->first_name_latin)
                or empty($osago_fond_data->middle_name_latin)
            )
                throw new BadRequestHttpException(Yii::t('app', 'fond_not_found owner info'), Osago::FRONT_ERROR_CODE['owner_info_not_found_in_fond']);

            if (!empty($driver_info))
            {
                $osago->insurer_license_series = $driver_info['LICENSE_SERIA'];
                $osago->insurer_license_number = $driver_info['LICENSE_NUMBER'];
                $osago->insurer_license_given_date = !empty($driver_info['ISSUE_DATE']) ? DateHelper::date_format($driver_info['ISSUE_DATE'], 'd.m.Y', 'Y-m-d') : null;
                $osago->insurer_address = $driver_info['ADDRESS'] ?? null;
            }

            $osago->insurer_passport_series = $this->insurer_passport_series;
            $osago->insurer_passport_number = $this->insurer_passport_number;

            if (!empty($this->insurer_inn))
                $osago->insurer_inn = $this->insurer_inn;
            $osago->save();

            return $osago;
        }

        if (empty($osago->created_at))
        {
            $osago->created_at = time();
            $osago->uuid = UuidHelper::uuid();
        }

        $osago->insurer_passport_series = null;
        $osago->insurer_passport_number = null;
        $osago->insurer_birthday = null;
        $osago->insurer_inn = null;
        $osago->insurer_pinfl = null;
        $osago->partner_ability = null;
        $osago->setAttributes($this->attributes);
        $osago->insurer_birthday = !empty($this->insurer_birthday) ? date_create_from_format('d.m.Y', $this->insurer_birthday)->getTimestamp() : null;
        $osago->status = Osago::STATUS['step1'];
        $osago->number_drivers_id = Osago::NO_LIMIT_NUMBER_DRIVERS_ID;
        $osago->partner_id = Partner::PARTNER['gross'];
        $osago->period_id = Osago::DEFAULT_PERIOD_ID;
        $osago->f_user_is_owner = Osago::DEFAULT_APPLICANT_IS_DRIVER;
        $osago->region_id = Osago::REGION_ANOTHER_ID;
        $osago->owner_with_accident = true;

        $osago->is_juridic = $osago->getIsJuridic();
        if ($osago->is_juridic)
            $osago->owner_with_accident = false;

        if (in_array(substr($this->autonumber, 0, 2), Osago::AUTONUMBER_TASHKENT_CODES))
            $osago->region_id = Osago::REGION_TASHKENT_ID;

        $osago->created_in_telegram = false;
        if (TelegramService::checkFromTelegram($this->data_check_string))
            $osago->created_in_telegram = true;

        $osago->save();

        $osago->getAutoAndOwnerInfo(true);

        $osago->setAccidentAmount();

        $osago = $this->setPromo($osago);

        $amount_uzs = $osago->getAmountUzs(false);
        $osago->amount_uzs = $amount_uzs;
        $osago->save();

        return $osago;
    }

    public function setPromo($osago)
    {
        if (empty($this->promo_code))
            return $osago;

        $promo = Promo::findOne(['code' => $this->promo_code, 'status' => Promo::STATUS['active']]);
        if (is_null($promo))
            throw new BadRequestHttpException(Yii::t('app', 'Xato Promocode kiritdingiz'));

        if (Osago::find()->where(['promo_id' => $promo->id])->exists())
            throw new BadRequestHttpException(Yii::t('app', 'Bu linkdan allaqachon foydalanilgan'));

        $setPromoForm = new SetPromoForm();
        return $setPromoForm->setPromo($osago, $promo);
    }
}