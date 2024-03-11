<?php
namespace saas\models\CarInspectionForms;

use common\models\CarInspection;
use common\models\CarInspectionFile;
use thamtech\uuid\validators\UuidValidator;
use yii\base\Model;

class UploadFileForm extends Model
{
    public $uuid;
    public $video;
    public $images;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', 'images', 'video'], 'required'],
            [['uuid', 'video'], 'string'],
            [['images'], 'each', 'rule' => ['string']],
            [['uuid'], UuidValidator::className()],
            [['uuid'], 'exist', 'skipOnError' => true, 'targetClass' => CarInspection::className(), 'targetAttribute' => ['uuid' => 'uuid'], 'filter' => function($query){
                return $query->andWhere(['in', 'status', [CarInspection::STATUS['processing'], CarInspection::STATUS['rejected']]]);
            }],
        ];
    }


    public function save()
    {
        $car_inspection = CarInspection::findOne(['uuid' => $this->uuid]);

        CarInspectionFile::deleteAll(['car_inspection_id' => $car_inspection->id]);
        foreach ($this->images as $image) {
            $car_inspection_file = new CarInspectionFile();
            $car_inspection_file->url = $image;
            $car_inspection_file->type = CarInspectionFile::TYPE['image'];
            $car_inspection_file->car_inspection_id = $car_inspection->id;
            $car_inspection_file->save();
        }

        $car_inspection_file = new CarInspectionFile();
        $car_inspection_file->url = $this->video;
        $car_inspection_file->type = CarInspectionFile::TYPE['video'];
        $car_inspection_file->car_inspection_id = $car_inspection->id;
        $car_inspection_file->save();

        $car_inspection->status = CarInspection::STATUS['uploaded'];
        $car_inspection->save();

        return $car_inspection;
    }

}