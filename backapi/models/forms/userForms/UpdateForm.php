<?php
namespace backapi\models\forms\userForms;

use backapi\models\User;
use yii\base\Model;

class UpdateForm extends Model
{
    public $id;
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
            [['id', 'status', 'username'], 'required'],
            [['password'], 'string', 'max' => 255],
            [['username'], 'unique', 'targetClass' => User::class, 'targetAttribute' => 'username', 'filter' => function($query){
                $query->andWhere(['not', ['id' => $this->id]]);
            }],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Password and Repeat password don't match" ],
            ['email', 'email'],
            [['status'], 'in', 'range' => [9,10]]
        ];
    }

    public function save()
    {
       $user = User::findOne(['id' => $this->id]);
       $user->username = $this->username;
       $user->first_name = $this->first_name;
       $user->last_name = $this->last_name;
       $user->email = $this->email ?? "default@gmail.com";
       if (!empty($this->password) and !empty($this->password_repeat))
            $user->setPassword($this->password);
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