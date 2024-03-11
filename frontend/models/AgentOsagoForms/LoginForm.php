<?php

namespace frontend\models\AgentOsagoForms;

use common\models\BridgeCompany;
use common\models\Token;
use common\models\User;
use yii\web\BadRequestHttpException;

class LoginForm extends \yii\base\Model
{
    public $phone;
    public $key;

    public function rules(): array
    {
        return [
            [['phone', 'key'], 'required'],
            [['phone'], 'trim'],
            [['key'], 'exist', 'skipOnError' => true, 'targetClass' => BridgeCompany::className(), 'targetAttribute' => ['key' => 'code']],
        ];
    }

    public function attributeLabels(): array
    {
        return [];
    }

    /**
     * @throws BadRequestHttpException
     */
    public function send()
    {
        $bridge_company = BridgeCompany::findOne(['code' => $this->key]);
        $user = $bridge_company->user;
        if (empty($user) or $user->status != \backapi\models\User::STATUS_ACTIVE)
            return false;

        $this->phone = str_replace('+', '', $this->phone);
        $token = Token::createNewToken($this->phone);

        $token->generateAccessToken();
        $token->status = Token::STATUS['verified'];
        $token->verified_at = date('Y-m-d H:i:s');
        $token->save();

        $user = User::findByPhone($this->phone);
        if ($user->status != User::STATUS_ACTIVE)
            $user->bridge_company_id = BridgeCompany::findOne(['code' => $this->key])->id;
        $user->status = User::STATUS_ACTIVE;
        $user->save();

        return $token;
    }
}