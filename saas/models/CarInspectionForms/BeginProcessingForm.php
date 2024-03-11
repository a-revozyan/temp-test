<?php
namespace saas\models\CarInspectionForms;

use common\models\CarInspection;
use common\services\TelegramService;
use thamtech\uuid\validators\UuidValidator;
use yii\base\Model;

class BeginProcessingForm extends Model
{
    public $uuid;
    public $push_token;
    public $longitude;
    public $latitude;
    public $address;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid'], 'required'],
            [['push_token'], 'safe'],
            [['uuid'], UuidValidator::className()],
            [['uuid'], 'exist', 'skipOnError' => true, 'targetClass' => CarInspection::className(), 'targetAttribute' => ['uuid' => 'uuid'], 'filter' => function($query){
                return $query->andWhere(['in', 'status', [CarInspection::STATUS['created'], CarInspection::STATUS['processing'], CarInspection::STATUS['rejected'], CarInspection::STATUS['problematic']]]);
            }],
            [['longitude', 'latitude'], 'double'],
            [['address'], 'string', 'max' => 500],
        ];
    }


    public function save()
    {
        $car_inspection = CarInspection::findOne(['uuid' => $this->uuid]);
        $car_inspection->status = CarInspection::STATUS['processing'];
        $car_inspection->push_token = $this->push_token;
        $car_inspection->longitude = $this->longitude;
        $car_inspection->latitude = $this->latitude;
        $car_inspection->address = $this->address;

        $car_inspection->save();

        return $car_inspection;
    }

}