<?php

namespace backapi\controllers;

use backapi\models\forms\zoodPayForms\TransactionDeliveryForm;
use common\helpers\GeneralHelper;
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
                'transaction-delivery' => ['POST'],
            ]
        ];

        $behaviors['authenticator']['except'] = [];
        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/zood-pay/transaction-delivery",
     *     summary="pulni zoodpay dan bizni shotga ko'chirish",
     *     tags={"ZoodpayController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"model_id", "model_class"},
     *                 @OA\Property (property="model_class", type="string", example="Kasko", description="Hozircha faqat Kasko yuboriladi"),
     *                 @OA\Property (property="model_id", type="integer", example=693, description="Kasko ni id si"),
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
    public function actionTransactionDelivery()
    {
        GeneralHelper::checkPermission();

        $model = new TransactionDeliveryForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

}