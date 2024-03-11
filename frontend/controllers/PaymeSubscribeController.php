<?php

namespace frontend\controllers;

use common\models\SavedCard;
use frontend\models\PaymeSubscribeForms\CheckSMSCodeForm;
use frontend\models\UserapiForms\GetSecondsTillNextSmsForm;
use frontend\models\UserapiForms\TokenViaTelegramForm;
use yii\filters\VerbFilter;

class PaymeSubscribeController extends BaseController
{
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'pay' => ['POST'],
            ],
        ];
        $behaviors['authenticator']['except'] = ["*"];
        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/payme-subscribe/pay",
     *     summary="check sent sms code and pay",
     *     tags={"PaymeSubscribeController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"model_class", "model_id", "saved_card_id"},
     *                 @OA\Property (property="model_class", type="string", example="Osago", description="Qaysi mahsulotimiz sotilayotgan bo'lsa o'shani nomi, mavjud variantlar hozircha faqat Kasko"),
     *                 @OA\Property (property="model_id", type="integer", example="15", description="osago id yoki kasko id"),
     *                 @OA\Property (property="saved_card_id", type="integer", example="15"),
     *                 @OA\Property (property="verifycode", type="integer", example="666666", description="code which sent via sms"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="true",
     *          @OA\JsonContent( type="boolean", example="true")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionPay()
    {
        $model = new CheckSMSCodeForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->send();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionCards()
    {
        return SavedCard::find()->select(['id', 'card_mask'])
            ->where([
                'f_user_id' => \Yii::$app->user->id,
                'status' => SavedCard::STATUS['saved']
            ])->all();
    }
}
