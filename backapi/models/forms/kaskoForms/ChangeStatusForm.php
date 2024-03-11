<?php
namespace backapi\models\forms\kaskoForms;

use common\models\Kasko;
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
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Kasko::className(), 'targetAttribute' => ['id' => 'id']],
            [['status'], 'in', 'range' => [Kasko::STATUS['canceled']]],
        ];
    }

    public function save()
    {
        $kasko = Kasko::findOne($this->id);
        $old_status = $kasko->status;
        $kasko->status = $this->status;
        $kasko->save();

        if ($kasko->status == Kasko::STATUS['canceled'] and in_array($old_status, [Kasko::STATUS['payed'], Kasko::STATUS['attached'], Kasko::STATUS['processed'], Kasko::STATUS['policy_generated']]))
        {
            PaymentService::cancel(Kasko::className(), $kasko->id);
            if ($kasko->trans->payment_type == "click")
                $kasko->statusToBackBeforePayment();
        }

        return $kasko;
    }

}