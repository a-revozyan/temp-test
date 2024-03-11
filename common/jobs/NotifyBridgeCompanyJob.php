<?php
namespace common\jobs;

use common\models\BridgeCompanyRequest;
use common\helpers\GeneralHelper;
use common\models\Accident;
use common\models\BridgeCompany;
use common\models\Osago;
use common\models\Product;
use common\services\TelegramService;
use yii\queue\RetryableJobInterface;

class NotifyBridgeCompanyJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $osago_id;
    public $accident_id;
    protected $attempt_times = 60;


    public function getData()
    {
        if (!empty($this->osago_id)){
            $order = Osago::findOne($this->osago_id);
            $autonumber = $order->autonumber;
            $bridge_company = BridgeCompany::findOne($order->bridge_company_id);
            $product = Product::products['osago'];
            $received_policy = ($order->status == Osago::STATUS['received_policy']);
        }
        else
        {
            $order = Accident::findOne($this->accident_id);
            $autonumber = $order->osago->autonumber;
            $bridge_company = BridgeCompany::findOne($order->osago->bridge_company_id);
            $product = Product::products['accident'];
            $received_policy = ($order->status == Accident::STATUS['received_policy']);
        }

        return [$order, $bridge_company, $product, $received_policy, $autonumber];
    }
    public function execute($queue)
    {
        [$order, $bridge_company, $product, $received_policy, $autonumber] = $this->getData();
        if ($received_policy) {
            BridgeCompanyRequest::sendRequest($bridge_company->success_webhook_url, $bridge_company, $order, [
                'product' => $product,
                'autonumber' => $autonumber,
                'uuid' => $order->uuid,
                'policy_pdf_url' => $order->policy_pdf_url,
                'policy_number' => $order->policy_number,
            ]);
        }else{
            BridgeCompanyRequest::sendRequest($bridge_company->error_webhook_url, $bridge_company, $order, [
                'product' => $product,
                'autonumber' => $autonumber,
                'uuid' => $order->uuid
            ]);
        }
    }

    public function getTtr()
    {
        return 120;
    }

    public function canRetry($attempt, $error)
    {

        if ($attempt == $this->attempt_times)
        {
            [$order, $bridge_company, $product, $received_policy, $autonumber] = $this->getData();

            $txt = TelegramService::chatText($order, false, $received_policy);
            $txt = <<<HTML
policy: <b>$order->policy_pdf_url</b>

HTML . $txt;

            TelegramService::sendMessage(
                GeneralHelper::env('admin_telegram_bot_token'),
                BridgeCompany::TelegramChatId[$bridge_company->id],
                $txt
            );
        }

        return  $attempt < $this->attempt_times;
    }
}