<?php
namespace backapi\models\forms\userForms;

use backapi\models\User;
use common\models\Token;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;


class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
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
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return array whether the user is logged in successfully
     */
    public function login()
    {
        $user = $this->getUser();
        if ($user and $user->validatePassword($this->password)) {
            $token = Token::createNewTokenForAdmin($user->id);

            $permissions = Yii::$app->authManager->getPermissionsByUser($user->id);
            $routes = [];
            $menus = [];
            foreach ($permissions as $permission) {
                if (strpos($permission->name, '/') === false)
                    $menus[] = $permission->name;
                else
                {
                    $routes[] = $permission->name;
                }
            }
            return [
                'result' => true,
                'access_token' => $token->access_token,
                'response' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone_number' => $user->phone_number,
                    'email' => $user->email,
                    'address' => $user->address,
                    'roles' => array_keys(Yii::$app->authManager->getRolesByUser($user->id)),
                    'routes' => $routes,
                    'menus' => $menus,
                ]
            ];
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['username' => $this->username, 'status' => 10]);
        }

        return $this->_user;
    }
}