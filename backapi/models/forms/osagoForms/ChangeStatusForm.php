<?php
namespace backapi\models\forms\osagoForms;

use common\models\Accident;
use common\models\Osago;
use common\services\PaymentService;
use yii\base\Exception;
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
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['id' => 'id']],
            [['status'], 'in', 'range' => [Osago::STATUS['canceled']]],
        ];
    }

    public function save()
    {
        $osago = Osago::findOne($this->id);
        $old_status = $osago->status;
        $osago->status = $this->status;
        $osago->save();

        Accident::updateAll(['status' => Accident::STATUS['canceled']], ['osago_id' => $osago->id]);

        if ($osago->status == Osago::STATUS['canceled'] and in_array($old_status, [Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']]))
        {
            PaymentService::cancel(Osago::className(), $osago->id);
        }
        return $osago;
    }

}