<?php
namespace saas\models\CarInspectionForms;

use common\helpers\GeneralHelper;
use common\models\CarInspection;
use common\services\SMSService;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\base\Model;
use yii\web\BadRequestHttpException;

class SendVerificationSmsForm extends Model
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
                return $query->andWhere(['in', 'status', [CarInspection::STATUS['completed'], CarInspection::STATUS['sent_verification_sms']]]);
            }],
        ];
    }

    public function save()
    {
        $car_inspection = CarInspection::findOne(['uuid' => $this->uuid]);

        $big_time_of_sending_sms_in_seconds = is_null($car_inspection->big_time_of_sending_sms) ? 0 : strtotime($car_inspection->big_time_of_sending_sms);
        $now_in_seconds = strtotime(\date('Y-m-d H:i:s'));

        $seconds_till_next_verification_sms = $car_inspection->seconds_till_next_verification_sms();
        if ($seconds_till_next_verification_sms > 0)
            throw new BadRequestHttpException(Yii::t('app', "{minut_raqamda} minut o'tguncha qayta yuborish mumkin emas", ['minut_raqamda' => ceil($seconds_till_next_verification_sms/60)], GeneralHelper::lang_of_local()));

        $car_inspection->send_verification_sms_date = date('Y-m-d H:i:s');
        if ($now_in_seconds - $big_time_of_sending_sms_in_seconds > GeneralHelper::env('big_time_intervel_sending_sms_in_seconds'))
        {
            $car_inspection->big_time_of_sending_sms = date('Y-m-d H:i:s');
            $car_inspection->sent_sms_count = 0;
        }
        $car_inspection->sent_sms_count = $car_inspection->sent_sms_count+1;
        
       $car_inspection->verification_code = rand(10000, 99999);
       $car_inspection->status = CarInspection::STATUS['sent_verification_sms'];
       $car_inspection->save();

       SMSService::sendMessageAll($car_inspection->client->phone, "Tasdiqlash kodingiz: " . $car_inspection->verification_code);

       return $car_inspection;
    }

}