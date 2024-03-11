<?php

namespace common\services;

use common\helpers\GeneralHelper;
use common\models\SmsHistory;
use common\models\SmsTemplate;
use common\models\User;
use Yii;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\Response;
use yii\web\BadRequestHttpException;

class SMSService
{
    public const SMS_PROVIDERS = [
        'play_mobile' => 0,
        'opersms' => 1,
    ];

    public static function get_base_url($sms_provider)
    {
        $base_url = "";
        switch ($sms_provider) {
            case self::SMS_PROVIDERS['play_mobile']:
                $base_url = "https://send.smsxabar.uz/broker-api/send";
                break;
            case self::SMS_PROVIDERS['opersms']:
                $base_url = "http://83.69.139.182:8083/";
                break;
        }

        return $base_url;
    }

    public static function getAuthorization($sms_provider)
    {
        $auth = "";
        switch ($sms_provider) {
            case self::SMS_PROVIDERS['play_mobile']:
                $auth = 'Basic ' . base64_encode(GeneralHelper::env('playmobile_login') . ":" . GeneralHelper::env('playmobile_password'));
                break;
            case self::SMS_PROVIDERS['opersms']:
                $auth = "login=" . GeneralHelper::env('opersms_login') . "&password=" . GeneralHelper::env('opersms_password');
                break;
        }

        return $auth;
    }

    /**
     * @param $phone_number
     * @param $text
     * @param $telegram_chat_ids
     * @param $sms_template
     * @return bool
     * @throws BadRequestHttpException
     *
     * this function will send message to all telegram accounts of client. then if the client has not telegram account
     * it will send message via sms.
     */
    public static function sendMessageAll($phone_number, $text, $telegram_chat_ids = [], $sms_template = null)
    {
        $sent = false;
        foreach ($telegram_chat_ids as $telegram_chat_id) {
            if (self::sendMessage($phone_number, $text, $telegram_chat_id, $sms_template, true))
                $sent = true;
        }

        if (!$sent)
            $sent = self::sendMessage($phone_number, $text, null, $sms_template);
        return $sent;
    }

    public static function sendMessage($phone_number, $text, $telegram_chat_id = null, $sms_template = null, $do_not_send_via_sms = false)
    {
        $status = SmsHistory::STATUS['created'];
        $response_of_sms_service = null;
        $to_telegram = false;
        if (!is_null($sms_template) and $sms_template->type == SmsTemplate::TYPE['all_users_via_sms'])
            $telegram_chat_id = null;

        if (!is_null($telegram_chat_id)) {
            /** @var Response $response */
            /** @var SmsTemplate $sms_template */
            if (is_null($sms_template))
                $response = TelegramService::sendMessage(GeneralHelper::env('web_app_telegram_bot_token'), $telegram_chat_id, $text);
            else
                $response = TelegramService::sendMessage(GeneralHelper::env('web_app_telegram_bot_token'), $telegram_chat_id, $text, $sms_template->method, $sms_template->getFileUrl());
            $response_of_sms_service = $response->getData();
            if ($response->getData()['ok'] ?? false) {
                $status = SmsHistory::STATUS['sent_to_user'];
                $to_telegram = true;
            }
        }

        if (!is_null($sms_template) and $sms_template->type == SmsTemplate::TYPE['users_which_have_telegram_via_telegram']) {
            self::writeHistory($phone_number, $telegram_chat_id, $text, $to_telegram, $status, $response_of_sms_service, $sms_template);
            return ($status != SmsHistory::STATUS['created']) ? true : false;
        }

        if ($status == SmsHistory::STATUS['created'] and !$do_not_send_via_sms) {
            if (is_null($sms_template))
                $sms_provider = self::SMS_PROVIDERS['play_mobile'];
            else
                $sms_provider = self::SMS_PROVIDERS['play_mobile'];

            [$response_of_sms_service, $status_of_sms] = self::SEND_SMS($sms_provider, $phone_number, $text);
            if (!is_null($status_of_sms))
                $status = $status_of_sms;
        }

        self::writeHistory($phone_number, $telegram_chat_id, $text, $to_telegram, $status, $response_of_sms_service, $sms_template);

        if (isset($sms_provider) and $status != SmsHistory::STATUS['sent_to_external_service']) {
            [$response_of_sms_service, $status_of_sms] = self::SEND_SMS(!$sms_provider, $phone_number, $text);
            self::writeHistory($phone_number, $telegram_chat_id, $text, $to_telegram, $status, $response_of_sms_service, $sms_template);
            if ($status_of_sms != SmsHistory::STATUS['sent_to_external_service'])
                throw new BadRequestHttpException(\Yii::t('app', 'sms service is not working, please try again'));
        }

        return ($status != SmsHistory::STATUS['created']) ? true : false;
    }

    private static function SEND_SMS($sms_provider, $phone_number, $text)
    {
        $status_of_sms = null;
        $response_of_sms_service = null;
        if ($sms_provider == self::SMS_PROVIDERS['opersms']) {
            $data = array(
                array('phone' => $phone_number, 'text' => mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1')),
            );

            $array = array_chunk($data, 50, true);

            foreach ($array as $chunk) {

                // Инициализация соединения с сервером OperSMS:
                $ch = curl_init(self::get_base_url($sms_provider));
                // Включаем опцию возврата ответа:
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // Устанавливаем ограничение на выполнение запроса 30 секунд:
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                // Вариант 2. Если предыдущий вариант не работает,
                // используйте следующую конструкцию:
                curl_setopt($ch, CURLOPT_POSTFIELDS, self::getAuthorization($sms_provider) . "&data=" . json_encode($chunk));

                // Записываем результат выполнения запроса в переменную:
                $result = curl_exec($ch);
                // Закрываем соединение с сервером OperSMS:
                curl_close($ch);

                /**
                 * Далее приведен пример получения статуса отправленного сообщения.
                 * Возвращается строка в формате JSON, которая записывается в переменную
                 * для дальнейшей обработки программным обеспечением клиента:
                 */
                $response_of_sms_service = $result;
                // Преобразуем предыдущий ответ сервера из формата JSON в обычный массив:
                $array = json_decode($result, true);

                // Извлекаем из полученного массива request_id:
                $request_id = $array[0]['request_id'] ?? null;

                if ($request_id)
                    $status_of_sms = SmsHistory::STATUS['sent_to_external_service'];
            }
        } elseif ($sms_provider == self::SMS_PROVIDERS['play_mobile']) {
            $client = new Client();
            $request_body = json_encode([
                'messages' => [
                    [
                        'recipient' => $phone_number,
                        'message-id' => 'sugurtabozor_' . time() . "_" . rand(1, 99999),
                        'sms' => [
                            'originator' => "SBozor",
                            'content' => [
                                'text' => $text
                            ]
                        ],
                    ]
                ]
            ]);
            $response = $client->post(self::get_base_url($sms_provider), $request_body, ['Authorization' => self::getAuthorization($sms_provider), 'Content-Type' => 'application/json'])->send();
            $response_of_sms_service = $response->getContent();
            if ($response->getStatusCode() == 200)
                $status_of_sms = SmsHistory::STATUS['sent_to_external_service'];
        }

        return [
            $response_of_sms_service,
            $status_of_sms,
        ];
    }

    private static function writeHistory($phone, $telegram_chat_id, $message, $to_telegram, $status, $response_of_sms_service, $sms_template = null)
    {
        $fuser = User::findOne(['phone' => $phone]);

        $user = Yii::$app->user ?? null;

        $sms_history = new SmsHistory();
        $sms_history->phone = $phone;
        $sms_history->telegram_chat_id = $telegram_chat_id;
        $sms_history->message = $message;
        $sms_history->to_telegram = $to_telegram;
        $sms_history->status = $status;
        $sms_history->response_of_sms_service = json_encode($response_of_sms_service);
        $sms_history->sent_at = date('Y.m.d H:i:s');
        $sms_history->sent_by = is_null($user) ? null : $user->id;
        if (!is_null($sms_template))
            $sms_history->sms_template_id = $sms_template->id;

        if (!is_null($fuser))
            $sms_history->f_user_id = $fuser->id;
        $sms_history->save();
    }
}
