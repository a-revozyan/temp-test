<?php

namespace frontend\models\OsagoapiForms;

use common\helpers\GeneralHelper;
use common\models\NumberDrivers;
use common\models\Osago;

class GetNumberDriversForm extends \yii\base\Model
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

        if ($temp_osago->is_juridic)
            return $this->getNumberDriversByIds([Osago::NO_LIMIT_NUMBER_DRIVERS_ID]);

        return $this->getNumberDriversByIds([Osago::NO_LIMIT_NUMBER_DRIVERS_ID, Osago::TILL_5_NUMBER_DRIVERS_ID]);
    }

    public function getNumberDriversByIds($ids)
    {
        $lang = GeneralHelper::lang_of_local();
        return NumberDrivers::find()->select(['id', "name" => "name_$lang"])->where(['id' => $ids])->asArray()->all();
    }
}