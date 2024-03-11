<?php
namespace saas\models\CarInspectionForms;

use common\jobs\PingPartnerForCarInspectionPdfJob;
use common\models\CarInspection;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\base\Model;
use yii\web\BadRequestHttpException;

class VerifyForm extends Model
{
    public $uuid;
    public $code;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', "code"], 'required'],
            [['uuid'], UuidValidator::className()],
            [['uuid'], 'exist', 'skipOnError' => true, 'targetClass' => CarInspection::className(), 'targetAttribute' => ['uuid' => 'uuid'], 'filter' => function($query){
                return $query->andWhere(['in', 'status', [CarInspection::STATUS['sent_verification_sms']]]);
            }],
        ];
    }

    public function save()
    {
       $car_inspection = CarInspection::findOne(['uuid' => $this->uuid]);

       if ($car_inspection->verification_code != $this->code)
           throw new BadRequestHttpException(Yii::t('app', "Noto'g'ri kod"));

       $car_inspection->status = CarInspection::STATUS['verified_by_client'];
       $car_inspection->verified_date = date('Y-m-d H:i:s');
       $car_inspection->save();

        $pdf_url = CarInspection::SVAT_CONTAINER_PATH . "/$car_inspection->uuid.pdf";
        Yii::$app->queue1->push(new PingPartnerForCarInspectionPdfJob([
            'pdf_url' => $pdf_url,
            'car_inspection_id' => $car_inspection->id,
        ]));

       return $car_inspection;
    }

}