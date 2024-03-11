<?php

namespace frontend\models\OsagoapiForms;

use common\helpers\DateHelper;
use common\models\BridgeCompany;
use common\models\NumberDrivers;
use common\models\Osago;
use common\models\Period;
use common\services\fond\FondService;

class GetPartners2Form extends \yii\base\Model
{
    public $autonumber;
    public $number_drivers_id;
    public $period_id;

    public $insurer_tech_pass_series;
    public $insurer_tech_pass_number;

    public $insurer_passport_series;
    public $insurer_passport_number;

    public $insurer_birthday;
    public $insurer_pinfl;

    public $super_agent_key;

    public function rules()
    {
        return [
            [['autonumber', 'number_drivers_id', 'period_id'], 'required'],
            [['number_drivers_id'], 'integer'],
            [['autonumber', 'super_agent_key', 'insurer_pinfl', 'insurer_tech_pass_series', 'insurer_tech_pass_number', 'insurer_passport_series', 'insurer_passport_number'], 'string', 'max' => 255],
            [['number_drivers_id'], 'exist', 'skipOnError' => true, 'targetClass' => NumberDrivers::className(), 'targetAttribute' => ['number_drivers_id' => 'id']],
            [['period_id'], 'exist', 'skipOnError' => true, 'targetClass' => Period::className(), 'targetAttribute' => ['period_id' => 'id']],
            [['insurer_birthday'], 'date', 'format' => 'php:d.m.Y'],
        ];
    }

    public function attributeLabels()
    {
        return [];
    }

    public function save()
    {
        $partner_ability = null;

        $auto_info = FondService::getAutoInfo($this->insurer_tech_pass_series, $this->insurer_tech_pass_number, $this->autonumber, true);

        $birthday = DateHelper::birthday_from_pinfl($auto_info['PINFL'], false);
        if (!empty($auto_info['PINFL']) and !empty($birthday))
        {
            $this->insurer_pinfl = $auto_info['PINFL'];
            $this->insurer_birthday = $birthday;
        }

        $bridge_company = BridgeCompany::findOne(['code' => $this->super_agent_key]);
        if ($bridge_company)
            $partner_ability = Osago::PARTNER_ABILITY['without_kapital'];

        $driver_info = FondService::getDriverInfoByPinflOrBirthday($this->insurer_passport_series, $this->insurer_passport_number, $this->insurer_pinfl, $this->insurer_birthday);

        $without_gross_condition = (
                empty($driver_info['LAST_NAME_LATIN'])
                and empty($driver_info['FIRST_NAME_LATIN'])
                and empty($driver_info['MIDDLE_NAME_LATIN'])
                and empty($driver_info['OBLAST'])
                and empty($driver_info['RAYON'])
                and empty($driver_info['BIRTH_DATE'])
        );

        if ($without_gross_condition)
            $partner_ability = Osago::PARTNER_ABILITY['without_gross'];

        if ($bridge_company and $without_gross_condition)
            $partner_ability = Osago::PARTNER_ABILITY['without_gross_and_kapital'];

        $get_partners_form = new GetPartnersForm();
        $get_partners_form->autonumber = $this->autonumber;
        $get_partners_form->number_drivers_id = $this->number_drivers_id;
        $get_partners_form->period_id = $this->period_id;
        $get_partners_form->partner_ability = $partner_ability;

        return $get_partners_form->save();
    }
}