<?php
namespace backapi\models\forms\userForms;

use backapi\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;


class CreateForm extends Model
{
    public $first_name;
    public $last_name;
    public $username;
    public $email;
    public $status;
    public $password;
    public $password_repeat;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password', 'password_repeat', 'status', 'username'], 'required'],
            [['username'], 'unique', 'targetClass' => User::class, 'targetAttribute' => 'username'],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Password and Repeat password don't match" ],
            ['email', 'email'],
            [['status'], 'in', 'range' => [9,10]]
        ];
    }

    public function save()
    {
       $user = new User();
       $user->username = $this->username;
       $user->first_name = $this->first_name;
       $user->last_name = $this->last_name;
       $user->email = $this->email;
       if (empty($user->email))
           $user->email = "default@gmail.com";
       $user->generateAuthKey();
       $user->setPassword($this->password);
       $user->created_at = time();
       $user->updated_at = time();
       $user->status = $this->status;
       $user->save();

       return [
           'id' => $user->id,
           'username' => $user->username,
           'first_name' => $user->first_name,
           'last_name' => $user->last_name,
           'email' => $user->email,
           'status' => $user->status,
       ];
    }

}