<?php
namespace backapi\models\forms\fUserForms;

use common\models\Token;
use common\models\User;
use common\services\SMSService;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class SendSmsOrTelegramMessage extends Model
{
    public $user_id;
    public $phone;
    public $message;
    public $only_by_sms;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required', 'when' => function($model) {
                return empty($this->phone);
            }],
            [['phone'], 'required', 'when' => function($model) {
                return empty($this->user_id);
            }],
            [['user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['message'], 'string'],
            [['only_by_sms'], 'default', 'value' => 0],
            [['only_by_sms'], 'in', 'range' => [0,1]],
        ];
    }

    public function save()
    {
        if (!empty($this->user_id))
            $fuser = User::findOne($this->user_id);

        if (!empty($this->phone))
            $fuser = User::findOne(['phone' => $this->phone]);

        $telegram_chat_ids = [];
        if (!is_null($fuser) and !$this->only_by_sms)
        {
            $this->phone = $fuser->phone;
            $telegram_chat_ids = ArrayHelper::getColumn(
                Token::find()->where(['f_user_id' => $fuser->id, 'status' => Token::STATUS['verified']])
                    ->andWhere(['not', ['telegram_chat_id' => null]])
                    ->asArray()->all(),
                'telegram_chat_id'
            );
        }

        return SMSService::sendMessageAll($this->phone, $this->message, $telegram_chat_ids);
    }

}