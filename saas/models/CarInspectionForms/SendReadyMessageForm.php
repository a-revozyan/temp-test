<?php
namespace saas\models\CarInspectionForms;

use common\models\CarInspection;
use Yii;
use yii\base\Model;
use yii\httpclient\Client;

class SendReadyMessageForm extends Model
{
    public $event;
    public $job;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['event', 'job'], 'required'],
        ];
    }

    public function save()
    {
        if ($this->event != "update:job" or $this->job->state != "completed")
            return 0;


        /** @var CarInspection $car_inspection */
        $car_inspection = CarInspection::find()->where(['task_id' => $this->job->task_id])->one();
        if (empty($car_inspection))
            return 0;

        $car_inspection->status = CarInspection::STATUS['completed'];
        $car_inspection->save();

        $car_inspection->getActInspection();
    }

}