<?php

namespace frontend\controllers;

use common\helpers\GeneralHelper;
use common\models\ZoodpayRequest;
use frontend\models\ZoodPayForms\GetConfigurationForm;
use frontend\models\ZoodPayForms\IpnForm;
use frontend\models\ZoodPayForms\RefundForm;
use Yii;
use yii\filters\VerbFilter;

class ZoodPayController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'get-configuration' => ['POST'],
                'transaction-delivery' => ['POST'],
//                'ipn' => ['POST'],
                'refund' => ['POST'],
            ]
        ];

        $behaviors['authenticator']['only'] = ['get-configuration'];
        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/zood-pay/get-configuration",
     *     summary="get configuration of zoodpay for check amount_uzs is suitable or not",
     *     tags={"ZoodpayController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"model_id", "model_class"},
     *                 @OA\Property (property="model_class", type="string", example="Kasko", description="Hozircha faqat Kasko yuboriladi"),
     *                 @OA\Property (property="model_id", type="integer", example=693, description="Kasko ni uuid si"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="configuration",
     *          @OA\JsonContent( type="object", ref="#components/schemas/zoodpay_configuration")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetConfiguration()
    {
        $model = new GetConfigurationForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionIpn()
    {
        $request_body = $this->put_or_post_or_get;
        $zoodpay_request = ZoodpayRequest::create(GeneralHelper::env('front_website_send_request_url') . '/zood-pay/ipn', $request_body, null, null, null, null);

        $model = new IpnForm();
        $model->setAttributes($request_body);
        if ($model->validate())
            $response = $model->save();
        else
            $response = $this->sendFailedResponse($model->getErrors(), 422);

        $zoodpay_request->response_body = json_encode($response);
        $zoodpay_request->save();

        return $response;
    }

    public function actionRefund()
    {
        $request_body = $this->put_or_post_or_get;
        $zoodpay_request = ZoodpayRequest::create(GeneralHelper::env('front_website_send_request_url') . '/zood-pay/refund', $request_body, null, null, null, null);

        $model = new RefundForm();
        $model->setAttributes($request_body);

        if ($model->validate())
            $response = $model->save();
        else
            $response = $this->sendFailedResponse($model->getErrors(), 422);

        $zoodpay_request->response_body = json_encode($response);
        $zoodpay_request->save();

        return $response;
    }
}