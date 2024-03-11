<?php

namespace frontend\models\UserapiForms;

use common\helpers\GeneralHelper;
use common\models\User;
use Yii;
use yii\web\BadRequestHttpException;

class GetSecondsTillNextSmsForm extends \yii\base\Model
{
    public ?string $phone = null;

    public function rules(): array
    {
        return [
            [['phone'], 'required'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'phone' => Yii::t('app', 'phone'),
        ];
    }

    /**
     * @throws BadRequestHttpException
     */
    public function send()
    {
        $user = User::findByPhone($this->phone);
        if (is_null($user) or $user->sent_sms_count == 0)
            return 0;

        $time_of_sending_sms_in_seconds = is_null($user->time_of_sending_sms) ? 0 : strtotime($user->time_of_sending_sms);
        $big_time_of_sending_sms_in_seconds = is_null($user->time_of_sending_sms) ? 0 : strtotime($user->time_of_sending_sms);
        $now_in_seconds = strtotime(\date('Y-m-d H:i:s'));

        $seconds = GeneralHelper::env('time_intervel_sending_sms_in_seconds') + $time_of_sending_sms_in_seconds - $now_in_seconds;
        if ($user->sent_sms_count % GeneralHelper::env('sms_count_for_big_interval') == 0)
            $seconds = GeneralHelper::env('big_time_intervel_sending_sms_in_seconds') + $big_time_of_sending_sms_in_seconds - $now_in_seconds;

        if ($seconds < 0) $seconds = 0;

        return [
            'seconds' => $seconds
        ];
    }
}