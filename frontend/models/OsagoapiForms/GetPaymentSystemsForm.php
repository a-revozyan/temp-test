<?php

namespace frontend\models\OsagoapiForms;

use common\models\Osago;
use frontend\models\OsagoStepForms\Step4Form;

class GetPaymentSystemsForm extends \yii\base\Model
{
    public $autonumber;

    public function rules()
    {
        return [
            [['autonumber'], 'required'],
            [['autonumber'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [];
    }

    public function save()
    {
        $temp_osago = new Osago();
        $temp_osago->autonumber = $this->autonumber;

        $temp_osago->region_id = Osago::REGION_ANOTHER_ID;
        if (in_array(substr($this->autonumber, 0, 2), Osago::AUTONUMBER_TASHKENT_CODES))
            $temp_osago->region_id = Osago::REGION_TASHKENT_ID;

        $temp_osago->is_juridic = $temp_osago->getIsJuridic();

        $payment_systems = [];
        foreach (Step4Form::PAYMENT_VARIANT as $payment_system => $code) {
            $payment_systems[] = [
                'title' => $payment_system,
                'code' => $code,
            ];
        }

        return $payment_systems;
    }
}