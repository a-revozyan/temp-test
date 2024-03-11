<?php

namespace frontend\models\UserapiForms;

use common\helpers\GeneralHelper;
use common\models\Token;
use common\models\User;
use frontend\models\SignupForm;
use Yii;
use yii\web\BadRequestHttpException;

class LoginForm extends \yii\base\Model
{
    public ?string $phone = null;
    public ?string $password = null;

    public function rules(): array
    {
        return [
            [['phone', 'password'], 'required'],
            [['phone'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(),
                'targetAttribute' => ['phone' => 'phone'], 'filter' => function($query){
                return $query->andWhere(['status' => User::STATUS_ACTIVE]);
            }],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'phone' => Yii::t('app', 'phone'),
            'password' => Yii::t('app', 'password'),
        ];
    }

    public function send()
    {
        $user = User::find()->where(['phone' => $this->phone, 'status' => User::STATUS_ACTIVE])->one();
        if (empty($user->password_hash) or !$user->validatePassword($this->password))
            throw new BadRequestHttpException(Yii::t('app', "Password is incorrect", [], GeneralHelper::lang_of_local()));

        $token = Token::find()->where(['f_user_id' => $user->id, 'status' => Token::STATUS['verified']])->orderBy(['id' => SORT_DESC])->one();
        if (empty($token))
        {
            $token = Token::createNewToken($this->phone);
            $token->generateAccessToken();
            $token->status = Token::STATUS['verified'];
            $token->save();
        }

        return $token;
    }
}