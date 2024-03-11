<?php

namespace frontend\models\UserapiForms;

use common\models\User;
use Yii;
use yii\web\BadRequestHttpException;

class ConfirmForm extends \yii\base\Model
{
    public ?string $phone = null;
    public ?string $password = null;
    public ?int $verifycode = null;

    public function rules(): array
    {
        return [
            [['phone', 'password', 'verifycode'], 'required'],
            [['phone'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['phone' => 'phone']],
            [['verifycode'], 'integer'],
            [['verifycode'], 'filter', 'filter' => 'trim'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'phone' => Yii::t('app', 'phone'),
            'password' => Yii::t('app', 'password'),
            'verifycode' => Yii::t('app', 'verifycode'),
        ];
    }

    /**
     * @throws BadRequestHttpException
     */
    public function send()
    {
        $user = User::findByPhone($this->phone);

        if ($user->verification_token != $this->verifycode)
            throw new BadRequestHttpException(Yii::t('app', 'Verify code is incorrect'));

        $user->status = User::STATUS_ACTIVE;
        $user->setPassword($this->password);
        $user->access_token = Yii::$app->security->generateRandomString();
        $user->save();
        return $user;
    }
}