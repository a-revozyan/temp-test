<?php
namespace common\models;

use common\services\SMSService;
use yii\base\Model;


class SendVerificationCodeSurveyerForm extends Model
{
    public $phone_number;
    public $surveyer;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone_number'], 'required'],
            [['phone_number'], 'exist', 'skipOnError' => true, 'targetClass' => Surveyer::className(),
                'targetAttribute' => ['phone_number' => 'phone_number'], 'filter' => function($query){
                    return $query->leftJoin('auth_assignment', '"user"."id" = CAST("auth_assignment"."user_id" AS INTEGER)')
                            ->andWhere(['auth_assignment.item_name' => Surveyer::SURVEYER_ROLE_NAME]);
            }],
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function send()
    {
        $surveyer = $this->getSurveyer();
        $code = rand(10000, 99999);
        $surveyer->verification_token = $code;
        if ($surveyer->save())
            SMSService::sendMessage($surveyer->phone_number, "sugurtabozor.uz: sizning tasdiqlash kodingiz $code");

        return true;
    }

    /**
     * Finds user by [[username]]
     *
     * @return array|\yii\db\ActiveRecord
     */
    protected function getSurveyer()
    {
        if ($this->surveyer === null) {
            $this->surveyer = \mdm\admin\models\User::find()
                ->leftJoin('auth_assignment', '"user"."id" = CAST("auth_assignment"."user_id" AS INTEGER)')
                ->where([
                    'auth_assignment.item_name' => Surveyer::SURVEYER_ROLE_NAME,
                    'user.phone_number' => $this->phone_number
                ])->one();
        }

        return $this->surveyer;
    }
}
