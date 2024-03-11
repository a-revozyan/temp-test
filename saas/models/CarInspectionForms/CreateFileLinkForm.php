<?php
namespace saas\models\CarInspectionForms;

use common\models\CarInspection;
use common\models\CarInspectionFile;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use thamtech\uuid\validators\UuidValidator;
use yii\base\Model;
class CreateFileLinkForm extends Model
{
    public $uuid;
    public $type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', 'type'], 'required'],
            [['uuid'], 'string'],
            [['type'], 'integer'],
            [['uuid'], UuidValidator::className()],
            [['uuid'], 'exist', 'skipOnError' => true, 'targetClass' => CarInspection::className(), 'targetAttribute' => ['uuid' => 'uuid'], 'filter' => function($query){
                return $query->andWhere(['in', 'status', [CarInspection::STATUS['processing'], CarInspection::STATUS['rejected'], CarInspection::STATUS['problematic']]]);
            }],
        ];
    }


    public function save()
    {
        $car_inspection = CarInspection::findOne(['uuid' => $this->uuid]);
        $car_inspection_file = CarInspectionFile::find()->where(['car_inspection_id' => $car_inspection->id, 'type' => $this->type])->one();

        if (!empty($car_inspection_file))
        {
            CarInspection::deleteBlob($car_inspection_file->url);
            $car_inspection_file->delete();
        }

        $blob = "car_inspection_" . $car_inspection->id . "_type_" . $this->type . date('_Y_m_d_H_i_s');
        $blob = $blob . ($this->type == CarInspectionFile::TYPE['video'] ? '.mp4' : '.jpg');
        $url = CarInspection::getBlobUrl($blob);

        $car_inspection_file = new CarInspectionFile();
        $car_inspection_file->url = $url;
        $car_inspection_file->type = $this->type;
        $car_inspection_file->car_inspection_id = $car_inspection->id;
        $car_inspection_file->status = CarInspectionFile::STATUS['created'];
        $car_inspection_file->save();

        return  $car_inspection_file->getShortArrForUpload();
    }

}