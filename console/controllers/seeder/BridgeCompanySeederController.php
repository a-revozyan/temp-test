<?php

namespace console\controllers\seeder;

class BridgeCompanySeederController extends BaseSeederController
{
    public function actionRun()
    {
        $data = [
            [
                "id" => 17,
                "name" => "Paynet",
                "code" => "paynet_sugurtabozor_123",
                "created_at" => 1703581869,
                "updated_at" => 1703581869,
                "status" => 10,
                "user_id" => 117,
                "success_webhook_url" => "https://paynet.test",
                "error_webhook_url" => "https://paynet.test",
                "authorization" => null
            ]
        ];

        $this->insertData('bridge_company', ["id","name","code","created_at","updated_at","status","user_id","success_webhook_url","error_webhook_url","authorization"], $data);
    }
}