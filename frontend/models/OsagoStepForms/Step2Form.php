<?php

namespace frontend\models\OsagoStepForms;

use common\helpers\DateHelper;
use common\models\NumberDrivers;
use common\models\Osago;
use common\models\Partner;
use common\models\Period;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class Step2Form extends \yii\base\Model
{
    public $osago_uuid;
    public $number_drivers_id;
    public $period_id;
    public $change_status;
    public $begin_date;


    public function rules()
    {
        return [
            [['osago_uuid', 'number_drivers_id', 'period_id'], 'required'],
            [['number_drivers_id', 'period_id'], 'integer'],
            [['osago_uuid'], 'string', 'max' => 255],
            [['osago_uuid'], UuidValidator::className()],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['not', ['insurer_passport_series' => null]])
                    ->andWhere(['not', ['insurer_passport_number' => null]])
                    ->andWhere(['not', ['insurer_birthday' => null]])
                    ->andWhere(['not in', 'status', [
                        Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']
                    ]]);
            }],
            [['number_drivers_id'], 'exist', 'skipOnError' => true, 'targetClass' => NumberDrivers::className(), 'targetAttribute' => ['number_drivers_id' => 'id']],
            [['period_id'], 'exist', 'skipOnError' => true, 'targetClass' => Period::className(), 'targetAttribute' => ['period_id' => 'id']],
            [['change_status'], 'integer'],
            [['change_status'], 'default', 'value' => 1],
            [['begin_date'], 'date', 'format' => 'php:d.m.Y', "min" => date('d.m.Y')],
        ];
    }

    public function attributeLabels()
    {
        return [
            'osago_uuid' => Yii::t('app', 'osago_uuid'),
            'number_drivers_id' => Yii::t('app', 'number_drivers_id'),
            'period_id' => Yii::t('app', 'period_id'),
        ];
    }

    public function save()
    {
        $osago = Osago::findOne(['uuid' => $this->osago_uuid]);
        $osago->setAttributes($this->attributes);

        if (!empty($this->begin_date))
            $osago->begin_date = DateHelper::date_format($this->begin_date, 'd.m.Y', 'Y-m-d');

        if ($osago->is_juridic and $osago->number_drivers_id == Osago::TILL_5_NUMBER_DRIVERS_ID)
            throw new BadRequestHttpException('Juridic cars can not buy limited osago');

        if ($this->change_status)
            $osago->status = Osago::STATUS['step2'];


        if ($osago->region_id == Osago::REGION_TASHKENT_ID and empty($osago->insurer_passport_number))
            $osago->number_drivers_id = Osago::NO_LIMIT_NUMBER_DRIVERS_ID;

        $osago->applicant_is_driver = false;
        if ($osago->number_drivers_id == Osago::TILL_5_NUMBER_DRIVERS_ID)
            $osago->applicant_is_driver = true;

        if ($osago->region_id == Osago::REGION_TASHKENT_ID and $osago->number_drivers_id == Osago::TILL_5_NUMBER_DRIVERS_ID)
        {
            $osago->partner_id = Partner::PARTNER['kapital'];
            $osago->period_id = Osago::DEFAULT_PERIOD_ID;
        }else{
            $osago->partner_id = Partner::PARTNER['gross'];
        }
        $osago->save();

        $amount_uzs = $osago->getAmountUzs(false);
        $osago->amount_uzs = $amount_uzs;
        $osago->save();

        return $osago;
    }
}