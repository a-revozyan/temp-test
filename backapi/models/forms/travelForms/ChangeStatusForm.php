<?php
namespace backapi\models\forms\travelForms;

use common\models\Travel;
use common\services\PaymentService;
use yii\base\Model;


class ChangeStatusForm extends Model
{
    public $id;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'id'], 'required'],
            [['status', 'id'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Travel::className(), 'targetAttribute' => ['id' => 'id']],
            [['status'], 'in', 'range' => [Travel::STATUSES['canceled']]],
        ];
    }

    public function save()
    {
        $travel = Travel::findOne($this->id);
        $old_status = $travel->status;
        $travel->status = $this->status;
        $travel->save();

        if ($travel->status == Travel::STATUSES['canceled'] and in_array($old_status, [Travel::STATUSES['payed'], Travel::STATUSES['waiting_for_policy'], Travel::STATUSES['received_policy']]))
        {
            PaymentService::cancel(Travel::className(), $travel->id);
        }
        return $travel;
    }

}