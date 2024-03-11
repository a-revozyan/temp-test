<?php
namespace frontend\models;

use common\helpers\GeneralHelper;
use Yii;
use yii\base\Model;
use common\models\User;
use common\services\SMSService;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $phone;
    public $password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['phone', 'trim'],
            ['phone', 'required'],
            ['phone', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This phone has already been taken.'],
            ['phone', 'string', 'min' => 2, 'max' => 255],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->phone = $this->phone;
//        $user->email = $this->email;
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->created_at = time();
        $user->updated_at = time();
        $user->status = User::STATUS_INACTIVE;

        return $user->save() && $this->sendPhoneMessage($user);

    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    public function sendPhoneMessage($user)
    {
        $time_of_sending_sms_in_seconds = is_null($user->time_of_sending_sms) ? 0 : strtotime($user->time_of_sending_sms);
        $now_in_seconds = strtotime(\date('Y-m-d H:i:s'));
        if (
            $user
            and $user->sent_sms_count != 0
            and $user->sent_sms_count % GeneralHelper::env('sms_count_for_big_interval') == 0
            and $now_in_seconds - $time_of_sending_sms_in_seconds < GeneralHelper::env('big_time_intervel_sending_sms_in_seconds')
        )
            throw new BadRequestHttpException(Yii::t('app', "SMS yuborilganidan {minut_raqamda} minut o'tguncha qayta yuborish mumkin emas", ['minut_raqamda' => GeneralHelper::env('big_time_intervel_sending_sms_in_seconds')/60], GeneralHelper::lang_of_local()));

        if ($user and $now_in_seconds - $time_of_sending_sms_in_seconds < GeneralHelper::env('time_intervel_sending_sms_in_seconds'))
            throw new BadRequestHttpException(Yii::t('app', "SMS yuborilganidan {minut_raqamda} minut o'tguncha qayta yuborish mumkin emas", ['minut_raqamda' => 1], GeneralHelper::lang_of_local()));

        $text = Yii::t('app', "Kod podtverjdeniya Sug'urta Bozor:", [], GeneralHelper::lang_of_local()) . $user->verification_token;
        // Заполняем массив данных любым удобным способом:
        $phone_number = $user->phone;
        $user->time_of_sending_sms = date('m.d.Y H:i:s');
        $user->sent_sms_count = $user->sent_sms_count+1;

        /**
         * В виду ограничений на максимальное количество получателей в одном пакете,
         * делим массив данных на небольшие пакеты по 50 элементов и формируем POST
         * HTTP запрос с помощью модуля cURL:
         */

        if (!SMSService::sendMessage($phone_number, $text))
            return false;

        return $user->save();
    }

}
