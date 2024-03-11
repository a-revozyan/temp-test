<?php

namespace frontend\models\ProfileForms;

use common\helpers\DateHelper;
use common\models\City;
use common\models\User;
use Yii;
use yii\helpers\VarDumper;

class UpdateProfile extends \yii\base\Model
{
    public $first_name;
    public $last_name;
    public $email;
    public $gender;
    public $birthday;
    public $passport_seria;
    public $passport_number;
    public $city_id;

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'passport_seria', 'passport_number'], 'safe'],
            [['email'], 'email'],
            [['gender', 'city_id'], 'integer'],
            [['gender'], 'in', 'range' => User::GENDER],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id'], 'filter' => function($query){
                return $query->andWhere(['status' => City::STATUS['active']]);
            }],
            [['birthday'], 'date', 'format' => 'php:d.m.Y'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'first_name' => Yii::t('app', 'first_name'),
            'last_name' => Yii::t('app', 'last_name'),
            'email' => Yii::t('app', 'email'),
        ];
    }

    public function update()
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;

        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->email = $this->email;
        $user->gender = $this->gender;
        $user->birthday = !empty($this->birthday) ? DateHelper::date_format($this->birthday, 'd.m.Y', 'Y-m-d') : null;
        $user->passport_seria = $this->passport_seria;
        $user->passport_number = $this->passport_number;
        $user->city_id = $this->city_id;
        $user->save();
        return $user;
    }
}