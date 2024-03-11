<?php
namespace backapi\models\forms\osagoForms;

use common\models\Accident;
use common\models\Osago;
use common\models\OsagoDriver;
use yii\base\Model;


class SendRequestToGetAccidentPolicyForm extends Model
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
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function save()
    {
        $osago = Osago::findOne($this->id);
        $osago_drivers = OsagoDriver::find()->where(['osago_id' => $this->id, 'with_accident' => true])->all();

        if (count($osago_drivers) > 0 or $osago->owner_with_accident)
        {
            $accident = (new Accident())->save_accident_from_osago($osago, $osago_drivers);
            if ($accident->status == Accident::STATUS['canceled'])
                $accident->status = Accident::STATUS['payed'];
            $begin_date = null;
            if ($accident->begin_date < date('Y-m-d', strtotime("+1 day")))
                $begin_date = date('Y-m-d', strtotime("+1 day"));

            $accident->save();
            return $accident->get_policy_from_partner($accident->osago, true, $begin_date);
        }

        return null;
    }

}