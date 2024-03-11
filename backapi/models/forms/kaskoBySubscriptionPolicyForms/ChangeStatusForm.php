<?php
namespace backapi\models\forms\kaskoBySubscriptionPolicyForms;

use common\models\KaskoBySubscriptionPolicy;
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
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoBySubscriptionPolicy::className(), 'targetAttribute' => ['id' => 'id']],
            [['status'], 'in', 'range' => [KaskoBySubscriptionPolicy::STATUS['canceled']]],
        ];
    }

    public function save()
    {
        $kbsp = KaskoBySubscriptionPolicy::findOne($this->id);
        $old_status = $kbsp->status;
        $kbsp->status = $this->status;
        $kbsp->save();

        if ($kbsp->status == KaskoBySubscriptionPolicy::STATUS['canceled'] and in_array($old_status, [KaskoBySubscriptionPolicy::STATUS['payed'], KaskoBySubscriptionPolicy::STATUS['waiting_for_policy'], KaskoBySubscriptionPolicy::STATUS['received_policy']]))
        {
            PaymentService::cancel(KaskoBySubscriptionPolicy::className(), $kbsp->id);
        }
        return $kbsp;
    }

}