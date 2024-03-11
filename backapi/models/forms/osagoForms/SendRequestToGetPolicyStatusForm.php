<?php
namespace backapi\models\forms\osagoForms;

use common\models\Osago;
use common\models\OsagoRequest;
use yii\base\Model;


class SendRequestToGetPolicyStatusForm extends Model
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
        return OsagoRequest::sendRequest(OsagoRequest::URLS['get_policy_data'], $osago, ['order_id' => $osago->order_id_in_gross], false);
    }

}