<?php
namespace common\models;

use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;


class LoginSurveyerForm extends Model
{
    public $phone_number;
    public $password;

    private $surveyer;
    private $access_token;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['phone_number', 'password'], 'required']
            // password is validated by validatePassword()
//            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */

    public function validatePassword($attribute, $params)
    {

        if (!$this->hasErrors()) {
            $this->surveyer = $this->getSurveyer();

            if (!$this->surveyer || !(Yii::$app->security->validatePassword($this->password, $this->surveyer->password_hash))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {

            if (!$this->hasErrors()) {
                $this->surveyer = $this->getSurveyer();

                if (!$this->surveyer || !(Yii::$app->security->validatePassword($this->password, $this->surveyer->password_hash))) {
                    throw new NotFoundHttpException(Yii::t("app", "User not found"));
                }
            }

            $this->surveyer->access_token = Yii::$app->security->generateRandomString();
            $this->surveyer->save();
            $this->access_token = $this->surveyer->access_token;
            return $this->access_token;
        }
        
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User
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
