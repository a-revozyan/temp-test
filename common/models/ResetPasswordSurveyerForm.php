<?php
namespace common\models;

use common\services\SMSService;
use yii\base\Model;


class ResetPasswordSurveyerForm extends Model
{
    public $phone_number;
    public $code;
    public $password;
    public $repeat_password;

    public $surveyer;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone_number', 'code', 'password', 'repeat_password'], 'required'],
            [['phone_number'], 'exist', 'skipOnError' => true, 'targetClass' => Surveyer::className(),
                'targetAttribute' => ['phone_number' => 'phone_number'], 'filter' => function($query){
                    return $query->leftJoin('auth_assignment', '"user"."id" = CAST("auth_assignment"."user_id" AS INTEGER)')
                            ->andWhere(['auth_assignment.item_name' => Surveyer::SURVEYER_ROLE_NAME]);
            }],
            [['code'], 'exist', 'skipOnError' => true, 'targetClass' => Surveyer::className(),
                'targetAttribute' => ['code' => 'verification_token'], 'filter' => function($query){
                return $query->leftJoin('auth_assignment', '"user"."id" = CAST("auth_assignment"."user_id" AS INTEGER)')
                    ->andWhere([
                        'auth_assignment.item_name' => Surveyer::SURVEYER_ROLE_NAME,
                        'phone_number' => $this->phone_number
                    ]);
            }],
            ['password', 'compare', 'compareAttribute' => 'repeat_password']
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function send()
    {
        /** @var Surveyer $surveyer */
        $surveyer = $this->getSurveyer();
        if ($surveyer->verification_token == $this->code)
        {
            $surveyer->setPassword($this->password);
            if ($surveyer->save())
            {
                $surveyer->verification_token = null;
                $surveyer->save();
            }
        }

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
