<?php
namespace common\jobs;

use common\helpers\GeneralHelper;
use common\models\CarInspection;
use common\models\CarInspectionPartnerRequest;
use common\models\OsagoRequest;
use common\models\Partner;
use common\services\TelegramService;
use Exception;
use Yii;
use yii\httpclient\Client;
use yii\queue\RetryableJobInterface;
use yii\web\BadRequestHttpException;

class PingPartnerForCarInspectionPdfJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $pdf_url;
    public $car_inspection_id;

    protected $attempt_times = 5;

    public function execute($queue)
    {
        $car_inspection = CarInspection::find()->where(['id' => $this->car_inspection_id])->one();
        $client = new Client();

        $request_body = json_encode([
            'uuid' => $car_inspection->uuid,
            'pdf_url' => $this->pdf_url,
        ]);

        $car_inspection_partner_request = new CarInspectionPartnerRequest();
        $car_inspection_partner_request->url = $car_inspection->partner->hook_url;
        $car_inspection_partner_request->request_body = $request_body;
        $car_inspection_partner_request->partner_id = $car_inspection->partner->id;
        $car_inspection_partner_request->send_date = date('Y-m-d H:i:s');
        $car_inspection_partner_request->save();

        $basic_auth = '';
        switch ($car_inspection->partner->id)
        {
            case Partner::PARTNER['gross'] :
                $basic_auth = 'Basic ' . base64_encode(GeneralHelper::env('gross_login') . ":" . GeneralHelper::env('gross_password'));
                break;
            case Partner::PARTNER['kapital'] :
                $basic_auth = 'Basic ' . base64_encode(GeneralHelper::env('kapital_sugurta_login') . ":" . GeneralHelper::env('kapital_sugurta_password'));
                break;
            case Partner::PARTNER['insonline'] :
                $basic_auth = 'Basic ' . base64_encode(GeneralHelper::env('inson_login') . ":" . GeneralHelper::env('inson_password'));
                break;
        }

        $start_time = microtime(true);
        try {
            $response = $client->post(
                $car_inspection->partner->hook_url,
                $request_body,
                ['Authorization' => $basic_auth, 'Content-Type' => 'application/json']
            )->send();
            $status_code = $response->getStatusCode();
            $response = $response->getContent();
        }catch (Exception $exception){
            $status_code = 0;
            $response = $exception->getMessage();
        }


        $car_inspection_partner_request->response_body = $response;
        $car_inspection_partner_request->taken_time = floor(microtime(true) * 1000) - floor($start_time * 1000);
        $car_inspection_partner_request->save();

        if ($status_code != 200)
            throw new BadRequestHttpException($response);
    }

    public function getTtr()
    {
        return 120;
    }

    public function canRetry($attempt, $error)
    {
        if ($attempt == $this->attempt_times)
        {
            $car_inspection = CarInspection::findOne($this->car_inspection_id);
            $partner_id = $car_inspection->partner_id;
            $car_inspection = $car_inspection->getFullArr();
            $uuid = $car_inspection['uuid'];
            $pdf_url = $car_inspection['pdf_url'];
            $autonumber = $car_inspection['autonumber'];
            $name = $car_inspection['client']['name'];
            $phone_number = $car_inspection['client']['phone'];
            $text = <<<HTML
Car Inspection UUID: <b>$uuid</b> 
PDF URL: <b>$pdf_url</b> 
Autonumber: <b>$autonumber</b>
Name: <b>$name</b>
Phone: <b>$phone_number</b>
HTML;


            if (array_key_exists($partner_id, TelegramService::$chat_id_by_partner_id_for_car_inspection))
                TelegramService::sendMessage(
                    GeneralHelper::env('admin_telegram_bot_token'),
                    TelegramService::$chat_id_by_partner_id_for_car_inspection[$partner_id],
                    $text
                );
        }

        return  $attempt < $this->attempt_times;
    }
}