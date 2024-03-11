<?php
namespace backapi\models\forms\salesForCallCenterForms;

use common\helpers\GeneralHelper;
use common\models\TelephonyRequest;
use common\models\User;
use yii\base\Model;
use yii\helpers\VarDumper;

class CallForm extends Model
{
    public $f_user_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['f_user_id'], 'required'],
            [['f_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['f_user_id' => 'id']],
        ];
    }

    public function save()
    {
        $user = User::findOne(['id' => $this->f_user_id]);
        $response = TelephonyRequest::sendRequest(TelephonyRequest::URLS['call'], $user->phone, [
            "from" => GeneralHelper::env('online_p_b_x_internal_number'),
            "to" => substr($user->phone, 3),
            "gate_from" => GeneralHelper::env('online_p_b_x_external_number'),
        ]);

        return true;
    }

}