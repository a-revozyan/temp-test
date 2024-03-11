<?php

namespace frontend\models\CascoStepForms;

use common\helpers\DateHelper;
use common\models\Kasko;
use thamtech\uuid\validators\UuidValidator;
use Yii;

class Step23Form extends \yii\base\Model
{
    public $kasko_uuid;
    public $autonumber;
    public $insurer_tech_pass_series;
    public $insurer_tech_pass_number;

    public $insurer_passport_series;
    public $insurer_passport_number;
    public $insurer_pinfl;
    public $insurer_name;
    public $insurer_phone;
    public $insurer_address;
    public $begin_date;

    public function rules()
    {
        return [
            [['autonumber', 'insurer_tech_pass_series', 'insurer_tech_pass_number', 'kasko_uuid', 'insurer_passport_series', 'insurer_passport_number', 'insurer_phone', 'insurer_address', 'begin_date',], 'required'],
            [['autonumber', 'insurer_tech_pass_series', 'insurer_tech_pass_number', 'insurer_name', 'insurer_pinfl', 'insurer_passport_series', 'insurer_passport_number', 'insurer_phone', 'insurer_address'], 'string', 'max' => 255],
            ['kasko_uuid', UuidValidator::className()],
            [['kasko_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Kasko::className(), 'targetAttribute' => ['kasko_uuid' => 'uuid'], 'filter' => function($query){
                $query->andWhere([
                    'and',
                    ['not in', 'status', [
                            Kasko::STATUS['payed'],
                            Kasko::STATUS['attached'],
                            Kasko::STATUS['processed'],
                            Kasko::STATUS['policy_generated'],
                        ]
                    ]
                ]);
            }],

            ['begin_date', 'date', 'format' => 'php: d.m.Y'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'autonumber' => Yii::t('app', 'autonumber'),
            'kasko_uuid' => Yii::t('app', 'kasko'),
            'insurer_tech_pass_series' => Yii::t('app', 'insurer_tech_pass_series'),
            'insurer_tech_pass_number' => Yii::t('app', 'insurer_tech_pass_number'),
            'insurer_passport_series' => Yii::t('app', 'insurer_passport_series'),
            'insurer_passport_number' => Yii::t('app', 'insurer_passport_number'),
            'insurer_pinfl' => Yii::t('app', 'insurer_pinfl'),
            'insurer_name' => Yii::t('app', 'insurer_name'),
            'insurer_phone' => Yii::t('app', 'insurer_phone'),
            'insurer_address' => Yii::t('app', 'insurer_address'),
            'begin_date' => Yii::t('app', 'begin_date'),
        ];
    }

    public function save()
    {
        $casco = Kasko::findOne(['uuid' => $this->kasko_uuid]);
        $attributes = $this->attributes;

        $attributes['end_date'] = $this->getAfterAYear($attributes['begin_date']);
        $attributes['begin_date'] = DateHelper::date_format($attributes['begin_date'], 'd.m.Y', 'Y-m-d');

        $casco->setAttributes($attributes);
        $casco->status = Kasko::STATUS['step3'];

        $casco->save();
        return $casco;
    }

    public function getAfterAYear($date)
    {
        $date = date_create_from_format('d.m.Y', $date)->getTimestamp();
//        return date('m.d.Y', strtotime("+1 year", $date));
        return date('Y-m-d', strtotime("+364 day", $date));
    }
}