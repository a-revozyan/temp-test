<?php
namespace saas\models\CarInspectionForms;

use common\models\CarInspection;
use common\models\CarInspectionFile;
use thamtech\uuid\validators\UuidValidator;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class UploadedForm extends Model
{
    public $uuid;
    public $car_inspection_file_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', 'car_inspection_file_id'], 'required'],
            [['uuid'], 'string'],
            [['car_inspection_file_id'], 'integer'],
            [['uuid'], UuidValidator::className()],
            [['uuid'], 'exist', 'skipOnError' => true, 'targetClass' => CarInspection::className(), 'targetAttribute' => ['uuid' => 'uuid'], 'filter' => function($query){
                return $query->andWhere(['in', 'status', [CarInspection::STATUS['processing'], CarInspection::STATUS['rejected'], CarInspection::STATUS['problematic']]]);
            }],
        ];
    }


    public function save()
    {
        $car_inspection = CarInspection::findOne(['uuid' => $this->uuid]);
        $car_inspection_file = CarInspectionFile::findOne(['id' => $this->car_inspection_file_id]);
        if (empty($car_inspection_file) or $car_inspection_file->car_inspection_id != $car_inspection->id)
            throw new NotFoundHttpException();

        $headers = get_headers($car_inspection_file->url);
        $exist = stripos($headers[0],"200 OK");

        if ($exist) {
            $car_inspection_file->status = CarInspectionFile::STATUS['uploaded'];
            $car_inspection_file->save();
        }

        if ($exist)
            return $car_inspection_file->url;
        else
            return (boolean)$exist;
    }

}