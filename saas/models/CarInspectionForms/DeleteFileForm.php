<?php
namespace saas\models\CarInspectionForms;

use common\models\CarInspection;
use common\models\CarInspectionFile;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use thamtech\uuid\validators\UuidValidator;
use yii\base\Model;

class DeleteFileForm extends Model
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
                return $query->andWhere(['in', 'status', [CarInspection::STATUS['processing'], CarInspection::STATUS['rejected']]]);
            }],
        ];
    }


    public function save()
    {
        $car_inspection = CarInspection::findOne(['uuid' => $this->uuid]);
        $car_inspection_file = CarInspectionFile::find()->where(['id' => $this->car_inspection_file_id, 'car_inspection_id' => $car_inspection->id])->one();

        if (!empty($car_inspection_file))
        {
            CarInspection::deleteBlob($car_inspection_file->url);
            $car_inspection_file->delete();
        }

        return true;
    }

}