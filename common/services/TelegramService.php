<?php

namespace common\services;

use common\helpers\GeneralHelper;
use common\models\Accident;
use common\models\CarInspection;
use common\models\KaskoBySubscriptionPolicy;
use common\models\Osago;
use common\models\OsagoRequest;
use common\models\Travel;
use Longman\TelegramBot\Request;
use Yii;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\httpclient\Response;

class TelegramService
{
    public static $chat_id_by_partner_id = [
        -1 => '-1001712509283', //o'zimizning gruppa
        -2 => '-901112109', //car inspection
        1 => '-1001672770109',  //gross
        6 => '-1001764575527',  //EUROASIA INSURANCE
        7 => '-1001682236448',  //ALFA LIFE
        9 => '-1001648440142',  //APEX Insurance
        10 => '-1001536622550', //ALFA Invest
        18 => '',  //kapital sug'urta
        22 => '',  //neo
        23 => '',  //insonline
    ];

    public static $chat_id_by_partner_id_for_car_inspection = [
        1 => '-654988672',  //gross
        23 => '-1002146503625',  //inson
        18 => '-897130921',  //kapital
    ];

    public const METHOD = [
        "sendMessage" => "sendMessage",
        "sendPhoto" => "sendPhoto",
        "sendDocument" => "sendDocument",
        "sendVideo" => "sendVideo",
    ];

    public static function chatText($order, $is_to_our_chat = false, $error = false): string
    {
        $product = str_replace("common\\models\\", "", get_class($order));
        $partner_name = $order->partner->name;

        $bridge_company_name = $order->bridgeCompany->name ?? '';
        $payment_type = $order->trans->payment_type ?? '';
        $phone_number = $order->insurer_phone ?? '';
        $insurer_name = $order->insurer_name ?? '';
        $status = "";
        if ($product == "Osago")
        {
            $phone_number = $order->user->phone ?? '';
            $insurer_name = ($order->user->first_name  ?? '') . ($order->user->last_name ?? '');
            $status = array_flip(Osago::STATUS)[$order->status];
        }elseif ($product == "KaskoBySubscriptionPolicy"){
            $fuser = $order->kaskoBySubscription->fUser;
            $phone_number = $fuser->phone ?? '';
            $insurer_name = $order->kaskoBySubscription->applicant_name;
            $status = array_flip(KaskoBySubscriptionPolicy::STATUS)[$order->status];
        }elseif ($product == "Accident"){
            $status = array_flip(Accident::STATUS)[$order->status];
        }elseif ($product == "Travel"){
            $status = array_flip(Travel::STATUSES)[$order->status];
        }

        $created_in_telegram = $order->created_in_telegram ?? 0;

        $text = <<<HTML
Policy ID: <b>$order->policy_number</b> 
страховая компания: <b>$partner_name</b> 
номер телефона: <b>$phone_number</b>
HTML;


        if ($is_to_our_chat)
            $text = <<<HTML
страховая компания: <b>$partner_name</b>  
продукт: <b>$product</b> 
номер телефона: <b>$phone_number</b> 
id of $product: <b>$order->id</b> 
Payment type: <b>$payment_type</b>
Created in telegram: <b>$created_in_telegram</b>
HTML;

        if ($is_to_our_chat and !empty($bridge_company_name))
            $text .= <<<HTML

Bridge Company: <b>$bridge_company_name</b>
HTML;

        if (!empty($insurer_name))
            $text .= <<<HTML

Insurer Name: <b>$insurer_name</b>   
HTML;
        if (!empty($status))
            $text .= <<<HTML

Status: <b>$status</b>
HTML;

        if ($error)
            $text .= <<<HTML

<b>ERROR!!!</b> <b>ERROR!!!</b> <b>ERROR!!!</b>
HTML;

        return $text;
    }

    public static function send($order, $error = false)
    {
        if (get_class($order) == "common\\models\\CarInspection")
        {
            self::sendCarInspectionNotification($order);
            return 0;
        }

        self::sendMessage(
            GeneralHelper::env('admin_telegram_bot_token'),
            self::$chat_id_by_partner_id[-1],
            self::chatText($order, true, $error)
        );

        if (!$error)
            self::sendMessage(
                GeneralHelper::env('admin_telegram_bot_token'),
                self::$chat_id_by_partner_id[$order->partner_id],
                self::chatText($order, false, $error)
            );
    }

    public static function sendMessage($bot_token, $chat_id, $message,  $method = self::METHOD['sendMessage'], $file_url = null)
    {
        switch (trim($method))
        {
            case self::METHOD['sendVideo'] :
                $request = [
                    'video' => $file_url,
                    "caption" => $message
                ];
                break;
            case self::METHOD['sendPhoto'] :
                $request = [
                    'photo' => $file_url,
                    "caption" => $message
                ];
                break;
            case self::METHOD['sendDocument'] :
                $request = [
                    'document' => $file_url,
                    "caption" => $message
                ];
                break;
            case self::METHOD['sendMessage'] :
            default:
            $request = ["text" => $message];
        }

        $client = new Client();
        return $client->get("https://api.telegram.org/bot$bot_token/$method", json_encode([
            'parse_mode' => 'HTML',
            'chat_id' => $chat_id,
            ...$request
        ]), ['Content-Type' => 'application/json'])->send();
    }

    public static function checkFromTelegram($data_check_string)
    {
        if (is_null($data_check_string))
            return false;

        [$checksum, $sortedInitData] = self::convertInitData($data_check_string);
        $secretKey = hash_hmac('sha256', GeneralHelper::env('web_app_telegram_bot_token'), 'WebAppData', true);
        $hash = bin2hex(hash_hmac('sha256', $sortedInitData, $secretKey, true));

        return 0 === strcmp($hash, $checksum);
    }

    private static function convertInitData(string $initData): array
    {
        $initDataArray = explode('&', rawurldecode($initData));
        $needle        = 'hash=';
        $hash          = '';

        foreach ($initDataArray as &$data) {
            if (substr($data, 0, \strlen($needle)) === $needle) {
                $hash = substr_replace($data, '', 0, \strlen($needle));
                $data = null;
            }
        }
        $initDataArray = array_filter($initDataArray);
        sort($initDataArray);

        return [$hash, implode("\n", $initDataArray)];
    }

    /**
     * @param CarInspection $car_inspection
     * @return Response
     * @throws Exception
     */
    public static function sendCarInspectionNotification($car_inspection)
    {
        $bot_token = GeneralHelper::env('admin_telegram_bot_token');
        $method = self::METHOD['sendMessage'];

        $partner_name = $car_inspection->partner->name;
        $client = $car_inspection->client;
        $phone_number = $client->phone;
        $name = $client->name;
        $model = $car_inspection->autoModel->name;
        $text = <<<HTML
страховая компания: <b>$partner_name</b> 
номер телефона: <b>$phone_number</b> 
имя: <b>$name</b> 
модель: <b>$model</b> 
id: <b>$car_inspection->id</b> 
HTML;


        $client = new Client();
        return $client->get("https://api.telegram.org/bot$bot_token/$method", json_encode([
            'parse_mode' => 'HTML',
            'chat_id' => self::$chat_id_by_partner_id[-2],
            'text' => $text
        ]), ['Content-Type' => 'application/json'])->send();
    }

    public static function sendCarInspectionFinishUploading($car_inspection)
    {
        $bot_token = GeneralHelper::env('admin_telegram_bot_token');
        $method = self::METHOD['sendMessage'];

        $partner_name = $car_inspection->partner->name;
        $client = $car_inspection->client;
        $phone_number = $client->phone;
        $name = $client->name;
        $model = $car_inspection->autoModel->name;
        $text = <<<HTML
<b>FINISH UPLOADING</b>
страховая компания: <b>$partner_name</b> 
номер телефона: <b>$phone_number</b> 
имя: <b>$name</b> 
модель: <b>$model</b> 
id: <b>$car_inspection->id</b> 
HTML;


        $client = new Client();
        return $client->get("https://api.telegram.org/bot$bot_token/$method", json_encode([
            'parse_mode' => 'HTML',
            'chat_id' => GeneralHelper::env('surveyer_process_chat_id'),
            'text' => $text
        ]), ['Content-Type' => 'application/json'])->send();
    }
    
}