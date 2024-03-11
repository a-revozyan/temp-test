<?php

namespace common\models;

use common\helpers\GeneralHelper;
use common\services\SMSService;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "token".
 *
 * @property int $id
 * @property int|null $f_user_id
 * @property int|null $user_id
 * @property string|null $verification_token
 * @property string|null $access_token
 * @property int|null $telegram_chat_id
 * @property int|null $car_price_telegram_chat_id
 * @property string|null $telegram_lang
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $verified_at
 * @property string|null $sent_sms_promo_at
 * @property string|null $new_phone_number
 *
 * @property User|null $fUser
 */
class Token extends \yii\db\ActiveRecord
{
    public const STATUS = [
        'created' => 0,
        'sent' => 1,
        'verified' => 2,
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['f_user_id', 'telegram_chat_id', 'status', 'car_price_telegram_chat_id'], 'default', 'value' => null],
            [['f_user_id', 'user_id', 'telegram_chat_id', 'status', 'car_price_telegram_chat_id'], 'integer'],
            [['created_at', 'updated_at', 'verified_at', 'sent_sms_promo_at'], 'safe'],
            [[ 'verification_token', 'access_token', 'telegram_lang', 'new_phone_number'], 'string', 'max' => 255],
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
            'verification_token' => 'Verification Token',
            'access_token' => 'Access Token',
            'telegram_chat_id' => 'Telegram Chat ID',
            'telegram_lang' => 'Telegram Lang',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function generateVerificationToken()
    {
        $this->verification_token = (string)mt_rand(10000, 99999);
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    public function getFUser()
    {
        return $this->hasOne(User::className(), ['id' => 'f_user_id']);
    }

    public function getUser()
    {
        return $this->hasOne(\backapi\models\User::className(), ['id' => 'user_id']);
    }

    public static function createNewTokenToChangePhone($phone)
    {
        $f_user = User::find()->where(['phone' => $phone])->one();
        $current_user = Yii::$app->getUser()->identity;

        if (!is_null($f_user))
            throw new BadRequestHttpException("Phone number already exist");

        $token = new Token();
        $token->f_user_id = $current_user->id;
        $token->status = Token::STATUS['created'];
        $token->created_at = date('Y-m-d H:i:s');
        $token->updated_at = date('Y-m-d H:i:s');
        $token->new_phone_number = $phone;
        $token->generateVerificationToken();
        $token->save();

        return $token;
    }

    public static function createNewToken($phone)
    {
        $f_user = User::find()->where(['phone' => $phone])->one();

        if (is_null($f_user))
        {
            $f_user = new User();
            $f_user->phone = $phone;
            $f_user->created_at = time();
            $f_user->updated_at = time();
            $f_user->status = User::STATUS_INACTIVE;
            $f_user->save();
        }

        $token = new Token();
        $token->f_user_id = $f_user->id;
        $token->status = Token::STATUS['created'];
        $token->created_at = date('Y-m-d H:i:s');
        $token->updated_at = date('Y-m-d H:i:s');
        $token->generateVerificationToken();
        $token->save();

        return $token;
    }

    public static function createNewTokenForAdmin($user_id)
    {
        $token = new Token();
        $token->user_id = $user_id;
        $token->status = Token::STATUS['created'];
        $token->created_at = date('Y-m-d H:i:s');
        $token->updated_at = date('Y-m-d H:i:s');
        $token->access_token = Yii::$app->security->generateRandomString();
        $token->save();

        return $token;
    }

    public function sendPhoneVerificationMessage($lang = null)
    {
        $user = $this->fUser;
        $big_time_of_sending_sms_in_seconds = is_null($user->big_time_of_sending_sms) ? 0 : strtotime($user->big_time_of_sending_sms);
        $now_in_seconds = strtotime(\date('Y-m-d H:i:s'));
        if (is_null($lang))
        {
            $lang = GeneralHelper::lang_of_local();

            $time_of_sending_sms_in_seconds = is_null($user->time_of_sending_sms) ? 0 : strtotime($user->time_of_sending_sms);
            if (
                $user
                and $user->sent_sms_count != 0
                and $user->sent_sms_count % GeneralHelper::env('sms_count_for_big_interval') == 0
                and $now_in_seconds - $big_time_of_sending_sms_in_seconds < GeneralHelper::env('big_time_intervel_sending_sms_in_seconds')
            )
                throw new BadRequestHttpException(Yii::t('app', "{minut_raqamda} minut o'tguncha qayta yuborish mumkin emas", ['minut_raqamda' => ceil((GeneralHelper::env('big_time_intervel_sending_sms_in_seconds') + $big_time_of_sending_sms_in_seconds - $now_in_seconds)/60)], GeneralHelper::lang_of_local()));

            if ($user and $now_in_seconds - $time_of_sending_sms_in_seconds < GeneralHelper::env('time_intervel_sending_sms_in_seconds'))
                throw new BadRequestHttpException(Yii::t('app', "SMS yuborilganidan {minut_raqamda} minut o'tguncha qayta yuborish mumkin emas", ['minut_raqamda' => 1], GeneralHelper::lang_of_local()));

        }

        $text = Yii::t('app', "Kod podtverjdeniya Sug'urta Bozor:", [], $lang) . $this->verification_token;

        $phone_number = $user->phone;

        $this->status = self::STATUS['sent'];
        $this->save();

        $user->time_of_sending_sms = date('Y-m-d H:i:s');
        if ($now_in_seconds - $big_time_of_sending_sms_in_seconds > GeneralHelper::env('big_time_intervel_sending_sms_in_seconds'))
        {
            $user->big_time_of_sending_sms = date('Y-m-d H:i:s');
            $user->sent_sms_count = 0;
        }
        $user->sent_sms_count = $user->sent_sms_count+1;

        if (!SMSService::sendMessage($phone_number, $text))
            return false;

        return $user->save();
    }

    public function getWithUser()
    {
        return array_merge($this->fUser->getShortArr(), [
            'access_token' => $this->access_token
        ]);
    }
}
