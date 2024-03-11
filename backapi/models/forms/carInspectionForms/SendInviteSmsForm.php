<?php
namespace backapi\models\forms\carInspectionForms;

use common\models\Autobrand;
use common\models\CarInspection;
use common\services\SMSService;
use yii\base\Model;

class SendInviteSmsForm extends Model
{
    public $id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => CarInspection::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function save()
    {
       $car_inspection = CarInspection::findOne($this->id);
       $car_inspection->sendInviteSms();

       return $car_inspection;
    }

}