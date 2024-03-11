<?php

namespace frontend\models\CascoStepForms;

use common\models\Kasko;
use common\models\Osago;
use Yii;


class GetautoPersonInfoForm extends \yii\base\Model
{
    public $autonumber;
    public $insurer_tech_pass_series;
    public $insurer_tech_pass_number;

    public function rules()
    {
        return [
            [['autonumber', 'insurer_tech_pass_series', 'insurer_tech_pass_number'], 'required'],
            [['autonumber', 'insurer_tech_pass_series', 'insurer_tech_pass_number'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'autonumber' => Yii::t('app', 'autonumber'),
            'insurer_tech_pass_series' => Yii::t('app', 'insurer_tech_pass_series'),
            'insurer_tech_pass_number' => Yii::t('app', 'insurer_tech_pass_number'),
        ];
    }

    public function save()
    {
        $casco = new Kasko();
        $casco->setAttributes($this->attributes);

        $casco->setAttributes($this->getPinflAndName());

        return $casco;
    }

    public function getPinflAndName()
    {
       $temp_osago = new Osago();
       $temp_osago->insurer_tech_pass_series = strtoupper($this->insurer_tech_pass_series);
       $temp_osago->insurer_tech_pass_number = $this->insurer_tech_pass_number;
       $temp_osago->autonumber = strtoupper($this->autonumber);

       ['auto_info' => $auto_info, 'person_info' => $person_info] = $temp_osago->getAutoAndOwnerInfo();

        $pinfl_and_name = [];

        if ($auto_info)
            $pinfl_and_name = [
                'insurer_pinfl' => $auto_info['PINFL'] ?? null,
                'insurer_name' =>  $auto_info['ORGNAME'] ?? null,
            ];

        if ($person_info)
            $pinfl_and_name = array_merge($pinfl_and_name, [
                'insurer_passport_series' =>  $person_info['passSeries'] ?? null,
                'insurer_passport_number' =>  $person_info['passNumber'] ?? null,
                'insurer_address' =>  $person_info['address'] ?? null,
            ]);

        return $pinfl_and_name;
    }
}