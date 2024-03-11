<?php
namespace saas\models\CarInspectionForms;

use common\models\CarInspection;
use common\models\CarInspectionFile;
use common\services\TelegramService;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class FinishedUploadingForm extends Model
{
    public $uuid;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid'], 'required'],
            [['uuid'], UuidValidator::className()],
            [['uuid'], 'exist', 'skipOnError' => true, 'targetClass' => CarInspection::className(), 'targetAttribute' => ['uuid' => 'uuid'], 'filter' => function($query){
                return $query->andWhere(['in', 'status', [CarInspection::STATUS['processing'], CarInspection::STATUS['rejected'], CarInspection::STATUS['uploaded'], CarInspection::STATUS['problematic']]]);
            }],
        ];
    }


    public function save()
    {
        $car_inspection = CarInspection::findOne(['uuid' => $this->uuid]);

        if (CarInspectionFile::find()->where(['car_inspection_id' => $car_inspection->id, 'status' => CarInspectionFile::STATUS['uploaded']])->count() == CarInspectionFile::FILES_COUNT)
            $car_inspection->status = CarInspection::STATUS['uploaded'];
        else
            throw new BadRequestHttpException(Yii::t('app', 'Your files more or less then {files_count}', ['files_count' => CarInspectionFile::FILES_COUNT]));

        $car_inspection->save();

        TelegramService::sendCarInspectionFinishUploading($car_inspection);

        return $car_inspection;
    }

}