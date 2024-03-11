<?php

namespace frontend\models\OsagoStepForms;

use common\models\Accident;
use common\models\Osago;
use thamtech\uuid\validators\UuidValidator;

class CalculateAccidentAmountForm extends \yii\base\Model
{
    public $owner_with_accident;
    public $accident_insurer_count;
    public $osago_uuid;

    public function rules()
    {
        return [
            [['osago_uuid'], 'required'],
            [['accident_insurer_count', 'owner_with_accident'], 'integer'],
            [['osago_uuid'], 'string', 'max' => 255],
            [['osago_uuid'], UuidValidator::className()],
            [['owner_with_accident'], 'in', 'range' => [0,1]],
            [['accident_insurer_count', 'owner_with_accident'], 'default', 'value' => 0],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['not in', 'status', [
                        Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']
                    ]]);
            }],
        ];
    }

    public function save()
    {
        $osago = Osago::findOne(['uuid' => $this->osago_uuid]);
        return ["accident_amount" => Accident::getAccidentAmount($this->accident_insurer_count + $this->owner_with_accident, $osago->partner_id)];
    }
}
