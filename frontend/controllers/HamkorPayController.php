<?php

namespace frontend\controllers;

use frontend\models\ZoodPayForms\GetConfigurationForm;
use frontend\models\ZoodPayForms\IpnForm;
use frontend\models\ZoodPayForms\RefundForm;
use yii\filters\VerbFilter;

class HamkorPayController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create-receipt' => ['POST'],
                'pay-receipt' => ['POST'],
                'get-receipt' => ['GET'],
            ]
        ];

        $behaviors['authenticator']['expect'] = [];
        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/hamkorpay/create-receipti",
     *     summary="yangi transaksiya yaratish",
     *     tags={"HamkorpayController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"model_id", "model_class", "card_number", "card_expiry"},
     *                 @OA\Property (property="model_class", type="string", example="Kasko", description="Hozircha faqat Kasko yuboriladi"),
     *                 @OA\Property (property="model_id", type="integer", example=693, description="Kasko ni id si"),
     *                 @OA\Property (property="card_number", type="string", example="8600120412345678", description="Karta raqami"),
     *                 @OA\Property (property="card_expiry", type="string", example="0123", description="Karta amal qilish muddati"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="yaratilgan transaction",
     *          @OA\JsonContent( type="object", ref="#components/schemas/hamkorpay_transaction")
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
        $model = new IpnForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionRefund()
    {
        $model = new RefundForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}