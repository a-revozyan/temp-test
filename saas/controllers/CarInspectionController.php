<?php

namespace saas\controllers;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\CarInspection;
use common\models\CarInspectionPartnerRequest;
use common\services\TelegramService;
use saas\models\CarInspectionForms\BeginProcessingForm;
use saas\models\CarInspectionForms\CreateCarInspectionForm;
use saas\models\CarInspectionForms\CreateFileLinkForm;
use saas\models\CarInspectionForms\DeleteFileForm;
use saas\models\CarInspectionForms\FinishedUploadingForm;
use saas\models\CarInspectionForms\SendReadyMessageForm;
use saas\models\CarInspectionForms\SendVerificationSmsForm;
use saas\models\CarInspectionForms\UploadedForm;
use saas\models\CarInspectionForms\VerifyForm;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class CarInspectionController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['POST'],
                'create-file-link' => ['POST'],
                'uploaded' => ['POST'],
                'begin-processing' => ['PUT'],
                'finished-uploading' => ['PUT'],
                'send-verification-sms' => ['POST'],
                'verify' => ['POST'],
                'delete-file' => ['DELETE'],
            ],
        ];

        $behaviors['basicAuth']['only'] = ['create'];
        $behaviors['authenticator']['except'] = ["*"];
        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/car-inspection/create",
     *     summary="create new car inspection",
     *     tags={"CarInspectionController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "phone", "autonumber", "vin", "auto_model", "tex_pass_series", "tex_pass_number"},
     *                 @OA\Property (property="name", type="string", example="new brand"),
     *                 @OA\Property (property="phone", type="string", example="998946464400"),
     *                 @OA\Property (property="autonumber", type="string", example="80U950JA"),
     *                 @OA\Property (property="tex_pass_series", type="string", example="XA"),
     *                 @OA\Property (property="tex_pass_number", type="string", example="5486256"),
     *                 @OA\Property (property="vin", type="iteger", example="12341541"),
     *                 @OA\Property (property="auto_model", type="string", example="matiz"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created car inspection",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/car_inspection")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        $car_inspection_partner_request = new CarInspectionPartnerRequest();
        $car_inspection_partner_request->url = "car-inspection/create";
        $car_inspection_partner_request->request_body = json_encode($this->post);
        $car_inspection_partner_request->partner_id = \Yii::$app->getUser()->identity->partner->id ?? null;
        $car_inspection_partner_request->send_date = date('Y-m-d H:i:s');
        $car_inspection_partner_request->save();
        $start_time = microtime(true);

        $model = new CreateCarInspectionForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            $response = $model->save()->getFullArr();
        else
            $response = $this->sendFailedResponse($model->getErrors(), 422);

        $car_inspection_partner_request->response_body = json_encode($response);
        $car_inspection_partner_request->taken_time = floor(microtime(true) * 1000) - floor($start_time * 1000);
        $car_inspection_partner_request->save();

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/car-inspection/get-by-id",
     *     summary="get car inspection by uuid",
     *     tags={"CarInspectionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="uuid", in="query", @OA\Schema (type="string"), example="sdfgh-dfghjkl-45678ig"),
     *     @OA\Response(
     *         response="200", description="car inspection object",
     *         @OA\JsonContent(type="object", ref="#/components/schemas/car_inspection")
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionGetById(string $uuid)
    {
        if (!$car_inspection = CarInspection::findOne(['uuid' => $uuid]))
            throw new NotFoundHttpException(Yii::t('app', 'uuid not found'));

        return $car_inspection->getFullArr();
    }

    /**
     * @OA\Get(
     *     path="/car-inspection/get-by-qrcode-uuid",
     *     summary="get car inspection by qrcode uuid",
     *     tags={"CarInspectionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="uuid", in="query", @OA\Schema (type="string"), example="sdfgh-dfghjkl-45678ig"),
     *     @OA\Response(
     *         response="200", description="car inspection object",
     *         @OA\JsonContent(type="object",
     *               @OA\Property(property="send_verification_sms_date", type="string", example="12.12.2023 13:23:13"),
     *               @OA\Property(property="verification_code", type="integer", example=12345),
     *               @OA\Property(property="verified_date", type="integer", example="12.12.2023 13:23:13"),
     *          )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionGetByQrcodeUuid(string $uuid): array
    {
        if (!$car_inspection = CarInspection::findOne(['uuid' => $uuid]))
            throw new NotFoundHttpException(Yii::t('app', 'uuid not found'));

        return [
            'send_verification_sms_date' => !empty($car_inspection->send_verification_sms_date) ? DateHelper::date_format($car_inspection->send_verification_sms_date,  'Y-m-d H:i:s', 'd.m.Y H:i:s') : null,
            'verification_code' => $car_inspection->verification_code,
            'verified_date' => !empty($car_inspection->verified_date) ? DateHelper::date_format($car_inspection->verified_date,  'Y-m-d H:i:s', 'd.m.Y H:i:s') : null,
        ];
    }

    /**
     * @OA\Post(
     *     path="/car-inspection/create-file-link",
     *     summary="create link for store file in azure",
     *     tags={"CarInspectionController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"uuid"},
     *                 @OA\Property (property="uuid", type="string", example="8a53dccf-d8f2-44d4-ba78-30e8fcf03dc6"),
     *                 @OA\Property (property="type", type="integer", example="0", description="0 => video, 1 => position1, 2 => position2"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="links for upload file",
     *          @OA\JsonContent( type="object", ref="#components/schemas/car_inspection_file")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreateFileLink()
    {
        $model = new CreateFileLinkForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/car-inspection/uploaded",
     *     summary="send request after uploaded file",
     *     tags={"CarInspectionController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"uuid", "car_inspection_file_id"},
     *                 @OA\Property (property="uuid", type="string", example="8a53dccf-d8f2-44d4-ba78-30e8fcf03dc6"),
     *                 @OA\Property (property="car_inspection_file_id", type="id", example="80"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="true or false",
     *          @OA\JsonContent( type="boolean", example=true)
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUploaded()
    {
        $model = new UploadedForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Delete(
     *     path="/car-inspection/delete-file",
     *     summary="delete file",
     *     tags={"CarInspectionController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"uuid", "car_inspection_file_id"},
     *                 @OA\Property (property="uuid", type="string", example="8a53dccf-d8f2-44d4-ba78-30e8fcf03dc6"),
     *                 @OA\Property (property="car_inspection_file_id", type="id", example="80"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="true or false",
     *          @OA\JsonContent( type="boolean", example=true)
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionDeleteFile()
    {
        $model = new DeleteFileForm();
        $model->setAttributes($this->put_or_post_or_get);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/car-inspection/finished-uploading",
     *     summary="change status to uploaded by client",
     *     tags={"CarInspectionController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"uuid"},
     *                 @OA\Property (property="uuid", type="string", example="8a53dccf-d8f2-44d4-ba78-30e8fcf03dc6"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated car inspection",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/car_inspection")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionFinishedUploading()
    {
        $model = new FinishedUploadingForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }


    /**
     * @OA\Put(
     *     path="/car-inspection/begin-processing",
     *     summary="change status to processing by client",
     *     tags={"CarInspectionController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"uuid", "push_token"},
     *                 @OA\Property (property="uuid", type="string", example="8a53dccf-d8f2-44d4-ba78-30e8fcf03dc6"),
     *                 @OA\Property (property="push_token", type="string", example="asdfasfadffdsa"),
     *                 @OA\Property (property="longitude", type="string", example="asdfasfadffdsa"),
     *                 @OA\Property (property="latitude", type="string", example="asdfasfadffdsa"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated car inspection",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/car_inspection")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionBeginProcessing()
    {
        $model = new BeginProcessingForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionSendReadyMessage()
    {
//         TelegramService::sendMessage(
//            GeneralHelper::env('admin_telegram_bot_token'),
//            TelegramService::$chat_id_by_partner_id[-1],
//            $this->put
//        );

        $model = new SendReadyMessageForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/car-inspection/send-verification-sms",
     *     summary="send verification sms after see pdf doc",
     *     tags={"CarInspectionController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"uuid"},
     *                 @OA\Property (property="uuid", type="string", example="sdfgh-dfghjkl-45678ig"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="car inspection",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/car_inspection")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSendVerificationSms()
    {
        $model = new SendVerificationSmsForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/car-inspection/verify",
     *     summary="verify by entering code",
     *     tags={"CarInspectionController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"uuid", "code"},
     *                 @OA\Property (property="uuid", type="string", example="sdfgh-dfghjkl-45678ig"),
     *                 @OA\Property (property="code", type="string", example="22222"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="car inspection",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/car_inspection")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionVerify()
    {
        $model = new VerifyForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}