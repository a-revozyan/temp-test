<?php

namespace frontend\controllers;

use frontend\models\UserapiForms\CheckSMSCodeForm;
use frontend\models\UserapiForms\GetSecondsTillNextSmsForm;
use frontend\models\UserapiForms\SendSMSCodeForm;
use frontend\models\UserapiForms\CheckForm;
use frontend\models\UserapiForms\ConfirmForm;
use frontend\models\UserapiForms\LoginForm;
use frontend\models\UserapiForms\RegisterForm;
use frontend\models\UserapiForms\SendSMSCodeToChangePhoneForm;
use frontend\models\UserapiForms\TokenViaTelegramForm;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class UserapiController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'register' => ['POST'],
                'confirm' => ['POST'],
                'send-sms-code' => ['POST'],
                'check-sms-code' => ['POST'],
            ],
        ];
        $behaviors['authenticator']['only'] = ["send-sms-code-to-change-phone", "change-phone"];
        return $behaviors;
    }

    //eski login register methodlari
    public function actionCheck()
    {
        $model = new CheckForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->check();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionRegister()
    {
        $model = new RegisterForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->send();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionConfirm()
    {
        $model = new ConfirmForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->send()->getWithToken();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->send()->getWithUser();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
    //eski login register methodlari

    //yangi login register methodlari

    /**
     * @OA\Post(
     *     path="/userapi/send-sms-code",
     *     summary="send sms code for phone confirmation",
     *     tags={"UserapiController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"phone"},
     *                 @OA\Property (property="phone", type="integer", example="998946464400", description="sms will be sent to this phone"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="returned true if successfully sent",
     *          @OA\JsonContent( type="boolean", example=true)
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionSendSmsCode()
    {
        $model = new SendSMSCodeForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->send();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/userapi/check-sms-code",
     *     summary="check sent sms code",
     *     tags={"UserapiController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"phone", "verifycode"},
     *                 @OA\Property (property="phone", type="integer", example="998946464400", description="phone which sms was be sent to"),
     *                 @OA\Property (property="verifycode", type="integer", example="12345", description="code which sent via sms"),
     *                 @OA\Property (property="data_check_string", type="string", description="agar sms orqali borgan kodni user telegramdan kiritayotgan bolsa yuborilsin"),
     *                 @OA\Property (property="telegram_chat_id", type="integer", description="agar sms orqali borgan kodni user telegramdan kiritayotgan bolsa yuborilsin"),
     *                 @OA\Property (property="car_price_telegram_chat_id", type="integer", description="agar sms orqali borgan kodni user car price telegramdan kiritayotgan bolsa yuborilsin"),
     *                 @OA\Property (property="bcrypted_token", type="string", description="car price bot ning token ini bcrypt qilib yuborish kerak"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="user with token",
     *          @OA\JsonContent( type="object", ref="#components/schemas/user_with_token")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionCheckSmsCode()
    {
        $model = new CheckSMSCodeForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->send()->getWithUser();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/userapi/get-seconds-till-next-sms",
     *     summary="get second which user should wait to send next sms",
     *     tags={"UserapiController"},
     *     @OA\Parameter (name="phone", in="query", required=true, @OA\Schema (type="integer")),
     *     @OA\Response(
     *         response="200", description="seconds",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="seconds", type="integer", example=13),
     *         )
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     * )
     */
    public function actionGetSecondsTillNextSms()
    {
        $model = new GetSecondsTillNextSmsForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->send();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/userapi/token-via-telegram",
     *     summary="get token if exist",
     *     tags={"UserapiController"},
     *     @OA\Parameter (name="data_check_string", in="query", required=false, @OA\Schema (type="string")),
     *     @OA\Parameter (name="telegram_chat_id", in="query", required=false, @OA\Schema (type="integer")),
     *     @OA\Parameter (name="car_price_telegram_chat_id", in="query", required=false, @OA\Schema (type="integer")),
     *     @OA\Parameter (name="bcrypted_token", in="query", required=false, @OA\Schema (type="integer")),
     *     @OA\Response(
     *         response="200", description="token",
     *         @OA\JsonContent(type="string", example="sdfghjkl765432fghjk")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     * @throws BadRequestHttpException
     */
    public function actionTokenViaTelegram(): array
    {
        $model = new TokenViaTelegramForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->send()->getWithUser();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    //telefon raqamini o'zgartirish
    /**
     * @OA\Post(
     *     path="/userapi/send-sms-code-to-change-phone",
     *     summary="send sms code for phone confirmation(to change phone)",
     *     tags={"UserapiController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"phone"},
     *                 @OA\Property (property="phone", type="integer", example="998946464400", description="sms will be sent to this phone"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="returned true if successfully sent",
     *          @OA\JsonContent( type="boolean", example=true)
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionSendSmsCodeToChangePhone()
    {
        $model = new SendSMSCodeToChangePhoneForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->send();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/userapi/change-phone",
     *     summary="send sms code for phone confirmation(to change phone)",
     *     tags={"UserapiController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"phone", "verifycode"},
     *                 @OA\Property (property="verifycode", type="integer", example="123456", description="verification code"),
     *                 @OA\Property (property="phone", type="string", example="998946464400", description="current phone"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="user with token",
     *           @OA\JsonContent( type="object", ref="#components/schemas/user_with_token")
     *      ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionChangePhone()
    {
        $model = new CheckSMSCodeForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->send()->getWithUser();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}
