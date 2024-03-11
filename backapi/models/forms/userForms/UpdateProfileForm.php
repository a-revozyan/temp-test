<?php
namespace backapi\models\forms\userForms;

use backapi\models\User;
use Yii;
use yii\base\Model;


class UpdateProfileForm extends Model
{
    public $first_name;
    public $last_name;
    public $email;
    public $phone_number;
    public $address;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'phone_number', 'address'], 'string'],
            ['email', 'email']
        ];
    }

    public function save()
    {
       $user = Yii::$app->user->identity;
       $user->first_name = $this->first_name;
       $user->last_name = $this->last_name;
       $user->address = $this->address;
       $user->phone_number = $this->phone_number;
       $user->email = $this->email;
       $user->save();

       return [
           'id' => $user->id,
           'first_name' => $user->first_name,
           'last_name' => $user->last_name,
           'address' => $user->address,
           'phone_number' => $user->phone_number,
           'email' => $user->email,
       ];
    }

}