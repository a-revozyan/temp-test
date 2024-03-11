<?php

namespace frontend\controllers;

use common\helpers\GeneralHelper;
use common\helpers\PdfHelper;
use common\jobs\NotifyBridgeCompanyJob;
use common\models\CarInspection;
use common\models\CarInspectionPartnerRequest;
use common\models\Osago;
use common\models\OsagoDriver;
use common\models\OsagoRequest;
use common\models\Period;
use common\models\UniqueCode;
use frontend\models\OsagoStepForms\CalculateAccidentAmountForm;
use frontend\models\OsagoStepForms\CheckApplicantLicenseIsExistForm;
use frontend\models\OsagoStepForms\CheckLicenseIsExistForm;
use frontend\models\OsagoStepForms\CheckOsagoFromKapitalForm;
use frontend\models\OsagoStepForms\CloneForm;
use frontend\models\OsagoStepForms\PayAccidentWithoutQueueForm;
use frontend\models\OsagoStepForms\PayForm;
use frontend\models\OsagoStepForms\PayWithoutQueueForm;
use frontend\models\OsagoStepForms\SaveForm;
use frontend\models\OsagoStepForms\SetPromoForm;
use frontend\models\OsagoStepForms\Step11Form;
use frontend\models\OsagoStepForms\Step1Form;
use frontend\models\OsagoStepForms\Step2Form;
use frontend\models\OsagoStepForms\Step3Form;
use frontend\models\OsagoStepForms\Step4Form;
use frontend\models\OsagoStepForms\VerifyForm;
use frontend\models\Searchs\OsagoSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class OsagoStepController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'step1' => ['POST'],
                'step2' => ['PUT'],
                'check-license-is-exist' => ['POST'],
                'check-applicant-license-is-exist' => ['POST'],
                'step3' => ['PUT'],
                'step5' => ['POST'],
                'pay' => ['POST'],
                'pay-without-queue' => ['POST'],
                'pay-accident-without-queue' => ['POST'],
                'delete-osago-driver' => ['DELETE'],
                'save' => ['POST'],
            ]
        ];

        $behaviors['authenticator']['only'] = ['osagos-of-user', 'step3', 'step4', 'clone', 'verify', 'save'];
        $behaviors['basicAuth']['only'] = ['pay', 'pay-without-queue', 'pay-accident-without-queue'];

        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/osago-step/step1",
     *     summary="create new Osago or update osago which sended by key osago_id",
     *     tags={"OsagoStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"insurer_tech_pass_series", "insurer_tech_pass_number", "autonumber"},
     *                 @OA\Property (property="insurer_tech_pass_series", type="string", example="AAF", description="tex passport seria"),
     *                 @OA\Property (property="insurer_tech_pass_number", type="string", example="0390422", description="tex passport raqam"),
     *                 @OA\Property (property="autonumber", type="string", example="01Y195DA", description="vehicle registration number(davlat raqami)"),
     *                 @OA\Property (property="osago_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of osago which is created by current user"),
     *                 @OA\Property (property="data_check_string", type="string", description="agar step1 ga zapros telegramdan yuborilayotgan bo'lsa yuborish kerak"),
     *                 @OA\Property (property="insurer_passport_series", type="string", example="AA", description="passport seria"),
     *                 @OA\Property (property="insurer_passport_number", type="string", example="0390422", description="passport raqam"),
     *                 @OA\Property (property="insurer_birthday", type="string", example="25.12.1991", description="owner yoki applicant tug'ilgan sanasi, formt: dd.mm.YY"),
     *                 @OA\Property (property="insurer_pinfl", type="string", example="3412341234123", description="yuridict bo'lganda kerak"),
     *                 @OA\Property (property="promo_code", type="string", example="qwertyuiop", description="unique promo code"),
     *                 @OA\Property (property="super_agent_key", type="string", example="qwertyuiop", description="super agent ekanligini bildiradigan key"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created or updated osago",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionStep1()
    {
        $model = new Step1Form();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/osago-step/step2",
     *     summary="step2",
     *     tags={"OsagoStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"osago_uuid", "number_drivers_id", "period_id"},
     *                 @OA\Property (property="osago_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of osago which is created by current user"),
     *                 @OA\Property (property="number_drivers_id", type="integer", example=1, description="1 => cheklanmagan, 2 => cheklangan"),
     *                 @OA\Property (property="change_status", type="integer", example=1, description="narxni bilish uchun 0, keyingi stepga o'tish uchun 1"),
     *                 @OA\Property (property="period_id", type="integer", example=1, description="1 => bir yillik, 2 => 6 oylik"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated osago",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionStep2()
    {
        $model = new Step2Form();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/osago-step/calculate-accident-amount",
     *     summary="calculate-accident-amount",
     *     tags={"OsagoStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="owner_with_accident", in="query", @OA\Schema (type="integer"), description="Mashina egasiga accident polis berish bermaslikni bildiradi. 0 yoki 1"),
     *     @OA\Parameter (name="accident_insurer_count", in="query", @OA\Schema (type="integer"), description="nechta haydovchida accident checkbox yoqilganligi"),
     *     @OA\Parameter (name="osago_uuid", in="query", @OA\Schema (type="string"), description="uuid of current osago"),
     *
     *     @OA\Response(response="200", description="accident_amount",
     *          @OA\JsonContent( type="object",
     *              @OA\Property(property="accident_amount", type="integer", example=10700),
     *          )
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionCalculateAccidentAmount()
    {
        $model = new CalculateAccidentAmountForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/osago-step/step11",
     *     summary="step11",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"osago_uuid"},
     *                 @OA\Property (property="osago_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of osago which is created by current user"),
     *                 @OA\Property (property="partner_id", type="integer", example=1, description="gross => 1, neo => 22"),
     *                 @OA\Property (property="owner_with_accident", type="integer", example=1, description="0, 1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated osago",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStep11()
    {
        $model = new Step11Form();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/osago-step/step3",
     *     summary="step3",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"osago_uuid"},
     *                 @OA\Property (property="osago_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of osago which is created by current user"),
     *                 @OA\Property (property="applicant_is_driver", type="integer", example=1, description="Ariza beruvchi haydovchilar ro'yxatiga qo'shilsinmi? 0 yoki 1"),
     *                 @OA\Property (property="owner_with_accident", type="integer", example=1, description="Mashina egasiga accident polis berish bermaslikni bildiradi. 0 yoki 1"),
     *                 @OA\Property (property="insurer_license_series", type="string|null", example=null, description="agar applicant pravasi fonddan topilmasa yuboriladi"),
     *                 @OA\Property (property="insurer_license_number", type="string|null", example=null, description="agar applicant pravasi fonddan topilmasa yuboriladi"),
     *                 @OA\Property (property="insurer_passport_series", type="string|null", example=null, description="agar 9 code li error kelsa yuborish kerak"),
     *                 @OA\Property (property="insurer_passport_number", type="string|null", example=null, description="agar 9 code li error kelsa yuborish kerak"),
     *                 @OA\Property (property="insurer_birthday", type="string|null", example=null, description="agar 9 code li error kelsa yuborish kerak"),
     *                 @OA\Property (property="drivers", type="array", description="data of relationship drivers for add insurance policy",
     *                      @OA\Items(type="object",
     *                          @OA\Property (property="relationship_id", type="integer", example=7, description="relationship of driver with owner, 1=>ota, 2=>aka, 3=>uka, 4=>xotin, 5=>ona, 6=>er, 7=>o'g'il, 8=>qiz, 9=>opa, 10=>singil"),
     *                          @OA\Property (property="birthday", type="string", example="23.07.1999", description="birthday of driver"),
     *                          @OA\Property (property="pinfl", type="string", example="123412341", description="pinfl of driver"),
     *                          @OA\Property (property="passport_series", type="string", example="KA", description="passport seria of driver"),
     *                          @OA\Property (property="passport_number", type="string", example="0829728", description="passport number of driver"),
     *                          @OA\Property (property="with_accident", type="integer", example=1, description="haydovchiga accident polis berish bermaslikni bildiradi. 0 yoki 1"),
     *                      )
     *                 ),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated osago",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400_with_driver_key"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStep3()
    {
        $model = new Step3Form();
        $model->setAttributes($this->put);
        if ($model->validate())
        {
            $response = $model->save();
            if (!is_array($response))
                $response = $response->getFullClientArr();
            return $response;
        }

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/osago-step/check-license-is-exist",
     *     summary="check is exist or not license data in fond database",
     *     tags={"OsagoStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"passport_series", "passport_number", "birthday"},
     *                 @OA\Property (property="osago_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of osago which is created by current user"),
     *                 @OA\Property (property="passport_series", type="string", example="KA", description="driver passport series"),
     *                 @OA\Property (property="passport_number", type="string", example="0829728", description="driver passport number"),
     *                 @OA\Property (property="license_series", type="string", example="AA", description="driver license series"),
     *                 @OA\Property (property="license_number", type="string", example="12345678", description="driver license number"),
     *                 @OA\Property (property="license_given_date", type="string", example="2023-01-30", description="driver license number"),
     *                 @OA\Property (property="birthday", type="string", example="1999-09-29", description="birthday of driver"),
     *                 @OA\Property (property="pinfl", type="string", example="23452345235", description="pinfl of driver"),
     *                 @OA\Property (property="relationship_id", type="integer", example=7, description="relationship of driver with owner, 1=>ota, 2=>aka, 3=>uka, 4=>xotin, 5=>ona, 6=>er, 7=>o'g'il, 8=>qiz, 9=>opa, 10=>singil"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="if exist license data api will return true",
     *          @OA\JsonContent( type="boolean", example=true)
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionCheckLicenseIsExist()
    {
        $model = new CheckLicenseIsExistForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->check();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/osago-step/step4",
     *     summary="step4",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"osago_uuid", "payment_variant"},
     *                 @OA\Property (property="osago_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of osago which is created by current user"),
     *                 @OA\Property (property="payment_variant", type="integer", example="2", description="payment service ['PAYME' => 0,'CLICK' => 1,'PAYZE' => 2, 'HAMKOR_PAY' => 3]"),
     *                 @OA\Property (property="card_id", type="integer", example="23", description="send while selecting Payze, get ids from /payze/cards API"),
     *                 @OA\Property (property="card_number", type="string", example="12345678", description="card number"),
     *                 @OA\Property (property="card_expiry", type="string", example="0223", description="car expire date monthYear format"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="checkout url",
     *          @OA\JsonContent( type="string", example="https://payze.uz/api/redirect/transaction/255A7168FAE64961A7EBD3EC98E1DF25")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStep4()
    {
        $model = new Step4Form();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/osago-step/verify",
     *     summary="verify",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"osago_uuid", "verifycode"},
     *                 @OA\Property (property="osago_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of osago which is created by current user"),
     *                 @OA\Property (property="verifycode", type="string", example="123321", description="telefonga yuborilgan sms kod"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="checkout url",
     *          @OA\JsonContent( type="string", example="https://payze.uz/api/redirect/transaction/255A7168FAE64961A7EBD3EC98E1DF25")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionVerify()
    {
        $model = new VerifyForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->send();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/osago-step/get-by-id",
     *     summary="get osago which is created current user by id",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="osago_uuid", in="query", @OA\Schema (type="string"), example="sdfgh-dfghjkl-45678ig"),
     *     @OA\Response(
     *         response="200", description="osago object",
     *         @OA\JsonContent(type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionGetById(string $osago_uuid)
    {
        return $this->getByID($osago_uuid)->getFullClientArr();
    }

    /**
     * @OA\Delete(
     *     path="/osago-step/delete-osago-driver",
     *     summary="delete driver of osago which is created current user",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="driver_id", in="query", @OA\Schema (type="integer"), example=124),
     *     @OA\Parameter (name="osago_uuid", in="query", @OA\Schema (type="string"), example="12345678-sdfghjk-5jhhg"),
     *     @OA\Response(
     *         response="200", description="if successfully deleted API return true",
     *         @OA\JsonContent(type="boolean", example=true)
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionDeleteOsagoDriver(int $driver_id, string $osago_uuid)
    {
        if (!$driver = OsagoDriver::findOne(['id' => $driver_id]))
            throw new NotFoundHttpException('driver_id not found');

        if (!$osago = Osago::findOne(['uuid' => $osago_uuid]))
            throw new NotFoundHttpException('driver_id not found');

        $driver->delete();
        return true;
    }

    /**
     * @OA\Post(
     *     path="/osago-step/check-applicant-license-is-exist",
     *     summary="check is exist or not license data of applicant in fond database",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"osago_uuid"},
     *                 @OA\Property (property="osago_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of osago which is created by current user"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="if exist license data of applicant api will return true",
     *          @OA\JsonContent( type="boolean", example=true)
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCheckApplicantLicenseIsExist()
    {
        $model = new CheckApplicantLicenseIsExistForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->check();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/osago-step/osagos-of-user",
     *     summary="get kaskos which is created current user and payed, waiting policy, received policy status",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Response(
     *         response="200", description="osagos",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/short_osago")),
     *             @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionOsagosOfUser()
    {
        $searchModel = new OsagoSearch();
        $dataProvider = $searchModel->search([
            'status' => [Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']],
            'f_user_id' => Yii::$app->user->id,
        ]);
        return [
            'models' => Osago::getShortClientArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Post(
     *     path="/osago-step/clone",
     *     summary="clone",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"code"},
     *                 @OA\Property (property="code", type="string", example="qwert", description="url dagi unique code"),
     *                 @OA\Property (property="insurer_passport_series", type="string", example="AA", description="passport seria"),
     *                 @OA\Property (property="insurer_passport_number", type="string", example="0390422", description="passport raqam"),
     *                 @OA\Property (property="insurer_birthday", type="string", example="25.12.1991", description="owner yoki applicant tug'ilgan sanasi, formt: dd.mm.YY"),
     *                 @OA\Property (property="insurer_pinfl", type="string", example="32510985350014", description="owner pinfl"),
     *                 @OA\Property (property="drivers", type="array", description="data of relationship drivers for add insurance policy",
     *                       @OA\Items(type="object",
     *                           @OA\Property (property="id", type="integer", example=7, description="id of existing driver"),
     *                           @OA\Property (property="pinfl", type="string", example="32510985350014", description="pinfl of existing driver"),
     *                       )
     *                  ),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="new osago",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *      @OA\Response(response="400", ref="#/components/responses/error_400_with_driver_key"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionClone()
    {
        $model = new CloneForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/osago-step/get-by-unique-code",
     *     summary="get osago by unique code",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="code", in="query", @OA\Schema (type="string"), example="qwert"),
     *     @OA\Response(
     *         response="200", description="osago object",
     *         @OA\JsonContent(type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionGetByUniqueCode($code)
    {
        $unique_code = UniqueCode::findOne(['code' => $code]);
        if (is_null($unique_code))
            throw new NotFoundHttpException(Yii::t('app', 'unique code not found'));

        if (Osago::find()->where(['unique_code_id' => $unique_code->id])->andWhere(['in', 'status', [
            Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy'],
        ]])->exists())
            throw new BadRequestHttpException(Yii::t('app', "Kechirasiz, bu kod link orqali allaqachon chegirma olingan"));

        $transaction = \Yii::$app->db->beginTransaction();

        $osago = Osago::findOne($unique_code->clonable_id);
        $osago->unique_code_id = $unique_code->id;
        $osago->policy_pdf_url = null;
        $osago->policy_number = null;
        $osago->begin_date = date('Y-m-d');
        $osago->end_date = date('Y-m-d', strtotime(Period::PERIOD_STRING[$osago->period_id]));
        $osago->getAutoAndOwnerInfo(true);
        $osago->amount_uzs = $osago->getAmountUzs();
        $osago->setAccidentAmount(false);
        $osago_arr = $osago->getFullClientArr();
        $osago_arr['id'] = null;

        $transaction->rollBack();

        return $osago_arr;
    }

    /**
     * @OA\Put(
     *     path="/osago-step/set-promo",
     *     summary="promo code ni ishlatish",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"osago_uuid"},
     *                 @OA\Property (property="osago_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of osago which is created by current user"),
     *                 @OA\Property (property="promo_code", type="string", example="trikchilik", description="promo code"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated osago",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSetPromo()
    {
        $model = new SetPromoForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/osago-step/check-osago-from-kapital",
     *     summary="This api will check status of osago from kapital",
     *     tags={"OsagoStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"osago_uuid"},
     *                 @OA\Property (property="osago_uuid", type="string", example="123412sdf-2341234-12341"),
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
    public function actionCheckOsagoFromKapital()
    {
        $model = new CheckOsagoFromKapitalForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/osago-step/save",
     *     summary="create order",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  required={""},
     *                  @OA\Property (property="begin_date", type="string", example="d.m.Y", description="policy begin date"),
     *                  @OA\Property (property="insurer_tech_pass_series", type="string", example="AAF", description="tex passport seria"),
     *                  @OA\Property (property="insurer_tech_pass_number", type="string", example="0390422", description="tex passport raqam"),
     *                  @OA\Property (property="autonumber", type="string", example="01Y195DA", description="vehicle registration number(davlat raqami)"),
     *                  @OA\Property (property="insurer_passport_series", type="string", example="AA", description="passport seria"),
     *                  @OA\Property (property="insurer_passport_number", type="string", example="0390422", description="passport raqam"),
     *                  @OA\Property (property="insurer_birthday", type="string", example="25.12.1991", description="owner yoki applicant tug'ilgan sanasi, formt: dd.mm.YY"),
     *                  @OA\Property (property="super_agent_key", type="string", example="qwertyuiop", description="super agent ekanligini bildiradigan key"),
     *                  @OA\Property (property="number_drivers_id", type="integer", example=1, description="1 => cheklanmagan, 2 => cheklangan"),
     *                  @OA\Property (property="period_id", type="integer", example=1, description="1 => bir yillik, 2 => 6 oylik"),
     *                  @OA\Property (property="partner_id", type="integer", example=1, description="gross => 1, neo => 22"),
     *                  @OA\Property (property="applicant_is_driver", type="integer", example=1, description="Ariza beruvchi haydovchilar ro'yxatiga qo'shilsinmi? 0 yoki 1"),
     *                  @OA\Property (property="insurer_license_series", type="string|null", example=null, description="agar applicant pravasi fonddan topilmasa yuboriladi"),
     *                  @OA\Property (property="insurer_license_number", type="string|null", example=null, description="agar applicant pravasi fonddan topilmasa yuboriladi"),
     *                  @OA\Property (property="drivers", type="array", description="data of relationship drivers for add insurance policy",
     *                       @OA\Items(type="object",
     *                           @OA\Property (property="relationship_id", type="integer", example=7, description="relationship of driver with owner, 1=>ota, 2=>aka, 3=>uka, 4=>xotin, 5=>ona, 6=>er, 7=>o'g'il, 8=>qiz, 9=>opa, 10=>singil"),
     *                           @OA\Property (property="birthday", type="string", example="23.07.1999", description="birthday of driver"),
     *                           @OA\Property (property="pinfl", type="string", example="32510985350014", description="pinfl of driver"),
     *                           @OA\Property (property="passport_series", type="string", example="KA", description="passport seria of driver"),
     *                           @OA\Property (property="passport_number", type="string", example="0829728", description="passport number of driver"),
     *                           @OA\Property (property="with_accident", type="integer", example=1, description="haydovchiga accident polis berish bermaslikni bildiradi. 0 yoki 1"),
     *                       )
     *                  ),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="osago",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSave()
    {
        $model = new SaveForm();

        $model->setAttributes($this->put);
        if ($model->validate())
        {
            $response = $model->save();
            if (!is_array($response))
                $response = $response->getFullClientArr();
            return $response;
        }

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/osago-step/pay",
     *     summary="agentga pul o'tganligini bildirish",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"osago_uuid"},
     *                 @OA\Property (property="osago_uuid", type="string", example="qwert-45235-451dfgdf", description="puli to'langan osago uuid si"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="osago",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionPay()
    {
        $model = new PayForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }


    /**
     * @OA\Post(
     *     path="/osago-step/pay-without-queue",
     *     summary="polisga pul to'langandan so'ng agent osago polisni olishi",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"osago_uuid"},
     *                 @OA\Property (property="osago_uuid", type="string", example="qwert-45235-451dfgdf", description="puli to'langan osago uuid si"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="osago",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionPayWithoutQueue()
    {
        $model = new PayWithoutQueueForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/osago-step/pay-accident-without-queue",
     *     summary="polisga pul to'langandan so'ng agent accident polisni olishi",
     *     tags={"OsagoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"osago_uuid"},
     *                 @OA\Property (property="osago_uuid", type="string", example="qwert-45235-451dfgdf", description="puli to'langan osago uuid si"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="osago",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionPayAccidentWithoutQueue()
    {
        $model = new PayAccidentWithoutQueueForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    private function getById($osago_uuid)
    {
        if (!$osago = Osago::findOne(['uuid' => $osago_uuid]))
            throw new NotFoundHttpException(Yii::t('app', 'osago_id not found'));

        return $osago;
    }

    public function actionTest()
    {

        VarDumper::dump(GeneralHelper::env('web_app_telegram_bot_username'));die();
//        $client = new Client();
//
//        $annotationResponse = $client->createRequest()
//            ->setMethod('GET')
//            ->setUrl("http://cvat.sugurtabozor.uz/api/labels?job_id=2&org=&page_size=500&page=1")
//            ->addHeaders([
//                'Authorization' => 'Basic ' . base64_encode("support@sugurtabozor.uz:Sugurta123"),
//                'Content-Type' => 'application/json',
//            ])
//            ->send();
//
//        return json_decode($annotationResponse->content, true);


        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        $id = 34;
//        $car_inspection = CarInspection::findOne($id);
//        $car_inspection->getActInspection();
$view = [
    'Спереди' => [
        'Крыша' => [
            0 => 1,
1 => 1,
],
'Передний бампер' => [
        0 => 4
    ],
'Лобовое стекло' => [
        0 => 8
    ]
],
'Справа' => [
        'Капот' => [
            0 => 3
        ]
    ],
'Сзади' => [
//        'Заднее стекло' => [
//            0 => 7,
//            1 => 8,
//            2 => 7,
//        ]
    ],
'Слева' => [
        'Левая передняя дверь' => [
            0 => 6
        ]
    ]
];
        $cvat_url = GeneralHelper::env('cvat_url');
        $headers = [
            'Authorization' => 'Basic ' . base64_encode(GeneralHelper::env('cvat_username') . ":" . GeneralHelper::env('cvat_password')),
            'Content-Type' => 'application/json',
        ];

        // We get annotation for a job
        $client = new Client();

//        $jobResponse = $client->createRequest()
//            ->setMethod('GET')
//            ->setUrl("$cvat_url/api/jobs")
//            ->addHeaders($headers)
//            ->setData(['task_id' => 8])
//            ->send();
//
//        if (!$jobResponse->isOk or !$data = json_decode($jobResponse->content, true) or empty($data['results']))
//            return 0;
//
//        $job_id = $data['results'][0]['id'];
//        $annotationResponse = $client->createRequest()
//            ->setMethod('GET')
//            ->setUrl("$cvat_url/api/jobs/$job_id/annotations")
//            ->addHeaders($headers)
//            ->send();
//
//        if (!$annotationResponse->isOk)
//            return 0;
//
//        $data = json_decode($annotationResponse->content, true);
//        $shapes = $data['shapes'];
//
//        $shapesByFrame = array();
//        $viewingAngles = array();
//
//        foreach ($shapes as $shape) {
//            $frameId = $shape['frame'];
//            $shapesByFrame[$frameId][] = $shape;
//
//            $attributes = $shape['attributes'];
//            $viewing_angle_index = in_array($attributes[0]['value'], CarInspection::VIEWING_ANGLE) ? 0 : 1;
//            $viewing_angle = $attributes[$viewing_angle_index]['value'];
//            $location = $attributes[!$viewing_angle_index]['value'];
//            $viewingAngles[$viewing_angle][$location][] = $shape['label_id'];
//
//        }
//
//
//        $client = new Client();
//        $cvat_url = GeneralHelper::env('cvat_url');
//        $headers = [
//            'Authorization' => 'Basic ' . base64_encode(GeneralHelper::env('cvat_username') . ":" . GeneralHelper::env('cvat_password')),
//            'Content-Type' => 'application/json',
//        ];
//
//        $frames = range(0, CarInspectionFile::FILES_COUNT-2);
//        foreach ($frames as $frameId) {
//            $frameResponse = $client->createRequest()
//                ->setMethod('GET')
//                ->setUrl("$cvat_url/api/jobs/8/data?org=&quality=compressed&type=frame&number=$frameId")
//                ->addHeaders($headers)
//                ->send();
//
//            $imageData = $frameResponse->content;
//
//
//            // Create image resource from image data
//            $image = imagecreatefromstring($imageData);
//
//            // we have to grab all labels and save it in our db so we don't need to make a request to CVAT server every time
//            // http://cvat.sugurtabozor.uz/api/labels?job_id=2&org=&page_size=500&page=1
//
//            $labels = CvatLabel::find()->asArray()->all();
//            $labels = ArrayHelper::map($labels, 'label_id', 'color');
//            // Loop through shapes and draw polygons on image
//            foreach ($shapesByFrame[$frameId] ?? [] as $shape) {
//                $label_id = $shape['label_id'];
//                $labelColor = $labels[$label_id];
//
//                list($r, $g, $b) = sscanf($labelColor, "#%02x%02x%02x");
//
//                $points = $shape['points'];
//                $color = imagecolorallocate($image, $r, $g, $b);
////                imagesetthickness($image, 5);
//                imagepolygon($image, $points, $color);
//            }
//
//            // Output image
//            header('Content-Type: image/jpeg');
//            // imagepng($image);
//
//            $folder = Yii::$app->basePath . "/../saas/web/assets/cvat/output/" . $id;
//
//            if (!file_exists($folder)) {
//                mkdir($folder, 0777, true);
//            }
//
//            $image_path = "$folder/{$frameId}_" . rand(0,9999) . ".JPEG";
//            $rotate = imagerotate($image, 90, 0);
//            imagejpeg($rotate, $image_path, 100);
//        }
//
//        $car_inspection = CarInspection::find()->where(['id' => $id])->one();
//        $file_paths_in_folder = array_diff(scandir($folder), array('.', '..'));
//
//        $file_paths_in_folder = array_map(function ($file_path) use($folder){
//            return $folder . "/$file_path";
//        }, $file_paths_in_folder);

        $file_paths_in_folder = [
            'E:\projects\sugurta-backend\frontend\web\img\act-inspection\img.png',
            'E:\projects\sugurta-backend\frontend\web\img\act-inspection\img.png',
            'E:\projects\sugurta-backend\frontend\web\img\act-inspection\img.png',
            'E:\projects\sugurta-backend\frontend\web\img\act-inspection\img.png',
            'E:\projects\sugurta-backend\frontend\web\img\act-inspection\img.png',
            'E:\projects\sugurta-backend\frontend\web\img\act-inspection\img.png',
            'E:\projects\sugurta-backend\frontend\web\img\act-inspection\img.png',
            'E:\projects\sugurta-backend\frontend\web\img\act-inspection\img.png',
            'E:\projects\sugurta-backend\frontend\web\img\act-inspection\img.png',
        ];

        return PdfHelper::genAktOsmotr($id, $file_paths_in_folder, $view)->render();

    }

    public function actionTest1()
    {
        $car_inspection = CarInspection::find()->where(['id' => 66])->one();
        $client = new Client();

        $request_body = json_encode([
            'uuid' => $car_inspection->uuid,
            'pdf_url' => 'aa',
        ]);

        $car_inspection_partner_request = new CarInspectionPartnerRequest();
        $car_inspection_partner_request->url = $car_inspection->partner->hook_url;
        $car_inspection_partner_request->request_body = $request_body;
        $car_inspection_partner_request->partner_id = $car_inspection->partner->id;
        $car_inspection_partner_request->send_date = date('Y-m-d H:i:s');
        $car_inspection_partner_request->save();

        $start_time = microtime(true);
        try {
            $response = $client->post(
                $car_inspection->partner->hook_url,
                $request_body,
                ['Authorization' => OsagoRequest::getAuthorization(), 'Content-Type' => 'application/json']
            )->send();
            $status_code = $response->getStatusCode();
            $response = $response->getContent();
        }catch (\Exception $exception){
            $status_code = 0;
            $response = $exception->getMessage();
        }


        $car_inspection_partner_request->response_body = $response;
        $car_inspection_partner_request->taken_time = floor(microtime(true) * 1000) - floor($start_time * 1000);
        $car_inspection_partner_request->save();

        if ($status_code != 200)
            throw new BadRequestHttpException($response);
    }

    public function actionTest2()
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setHeaders([
                'Authorization' => 'Bearer vLGy8Bk}pqI{yLi}lclsu7-rFWiQfe4n',
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept'=> 'application/json'
            ])
            ->setUrl('https://insurance.road24.uz/webhook/sugurta_bozor')
            ->setContent(json_encode( [
                'product' => 1,
                'autonumber' => "80U950JA",
                'uuid' => "52595a39-ae24-45f8-a57d-9fd4dd1f1f16",
                'policy_pdf_url' => "test",
                'policy_number' => "test",
            ]))
            ->send();

        return $response->getStatusCode();
    }

    public function actionTest3()
    {
//        $osago = Osago::findOne(['uuid' => '541cb1ca-8394-405b-becd-6d16ee085a5d']);//neo --
//        $osago = Osago::findOne(['uuid' => '1f2c1334-ceb2-42f4-8a4e-f611d28d1a51']);//inson
//        $osago = Osago::findOne(['uuid' => '1f2c1334-ceb2-42f4-8a4e-f611d28d1a51']);//gross
//        return $osago->create_osago_in_partner_system();

    }

}