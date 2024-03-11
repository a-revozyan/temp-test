<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sms_history".
 *
 * @property int $id
 * @property int|null $f_user_id
 * @property string|null $phone
 * @property integer|null $telegram_chat_id
 * @property string|null $message
 * @property bool|null $to_telegram
 * @property string|null $sent_at
 * @property int|null $sent_by
 * @property integer|null $status
 * @property integer|null $sms_template_id
 * @property string|null $response_of_sms_service
 */
class SmsHistory extends \yii\db\ActiveRecord
{
    public const STATUS = [
        'created' => 0,
        'sent_to_external_service' => 1,
        'sent_to_user' => 2,
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['f_user_id', 'sent_by'], 'default', 'value' => null],
            [['f_user_id', 'sent_by', 'telegram_chat_id', 'sms_template_id'], 'integer'],
            [['to_telegram'], 'boolean'],
            [['sent_at'], 'safe'],
            [['phone', 'message'], 'string', 'max' => 255],
            [['response_of_sms_service'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'f_user_id' => 'F User ID',
            'phone' => 'Phone',
            'telegram_chat_id' => 'Telegram Chat ID',
            'message' => 'Message',
            'to_telegram' => 'To Telegram',
            'sent_at' => 'Sent At',
            'sent_by' => 'Sent By',
            'response_of_sms_service' => 'Response of sms service',
        ];
    }
}
