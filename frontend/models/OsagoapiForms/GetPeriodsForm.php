<?php

namespace frontend\models\OsagoapiForms;

use common\helpers\GeneralHelper;
use common\models\NumberDrivers;
use common\models\Osago;
use common\models\Partner;
use common\models\Period;

class GetPeriodsForm extends \yii\base\Model
{
    public $autonumber;
    public $number_drivers_id;

    public function rules()
    {
        return [
            [['autonumber', 'number_drivers_id'], 'required'],
            [['number_drivers_id'], 'integer'],
            [['autonumber'], 'string', 'max' => 255],
            [['number_drivers_id'], 'exist', 'skipOnError' => true, 'targetClass' => NumberDrivers::className(), 'targetAttribute' => ['number_drivers_id' => 'id']],
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
        $temp_osago->number_drivers_id = $this->number_drivers_id;

        $temp_osago->region_id = Osago::REGION_ANOTHER_ID;
        if (in_array(substr($this->autonumber, 0, 2), Osago::AUTONUMBER_TASHKENT_CODES))
            $temp_osago->region_id = Osago::REGION_TASHKENT_ID;

        $temp_osago->is_juridic = $temp_osago->getIsJuridic();

        if ($temp_osago->is_juridic and $temp_osago->region_id == Osago::REGION_TASHKENT_ID and $temp_osago->number_drivers_id == Osago::TILL_5_NUMBER_DRIVERS_ID)
            return [];

        if ($temp_osago->region_id == Osago::REGION_TASHKENT_ID and $temp_osago->number_drivers_id == Osago::TILL_5_NUMBER_DRIVERS_ID)
            return $this->getPeriodsByIds([Osago::DEFAULT_PERIOD_ID]);

        return  $this->getPeriodsByIds([Osago::DEFAULT_PERIOD_ID, Osago::PERIOD_6_ID]);
    }

    public function getPeriodsByIds($ids)
    {
        $lang = GeneralHelper::lang_of_local();
        return Period::find()->select(['id', "name" => "name_$lang"])->where(['id' => $ids])->asArray()->all();
    }
}