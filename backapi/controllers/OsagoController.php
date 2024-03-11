<?php
namespace backapi\controllers;

use backapi\models\forms\osagoForms\ChangeStatusForm;
use backapi\models\forms\osagoForms\SendRequestToGetAccidentPolicyForm;
use backapi\models\forms\osagoForms\SendRequestToGetPolicyForm;
use backapi\models\forms\osagoForms\SendRequestToGetPolicyStatusForm;
use backapi\models\forms\osagoForms\SummaryForm;
use backapi\models\forms\osagoForms\UpdateForm;
use common\helpers\GeneralHelper;
use yii\filters\VerbFilter;

class OsagoController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'change-status' => ['PUT'],
                'send-request-to-get-policy' => ['POST'],
                'update' => ['PUT'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Put(
     *     path="/osago/change-status",
     *     summary="change status osago",
     *     tags={"OsagoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "status"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="status", type="integer", example="9", description="o'zgartirilishi kerak bo'lgan status. hozircha faqat 9"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated osago",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/osago")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionChangeStatus()
    {
        GeneralHelper::checkPermission();

        $model = new ChangeStatusForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/osago/send-request-to-get-policy",
     *     summary="bu api ga so'rov yuborilganda grossdan haqiqiy polis rasmiylashtiriladi. Iltimos, testlaganda umuman yo'q mashina raqamini kiritib testlang",
     *     tags={"OsagoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="grossdan kelgan javob",
     *          @OA\JsonContent( type="object")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSendRequestToGetPolicy()
    {
        GeneralHelper::checkPermission();

        $model = new SendRequestToGetPolicyForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/osago/send-request-to-get-accident-policy",
     *     summary="bu api ga so'rov yuborilganda grossdan haqiqiy polis rasmiylashtiriladi. Iltimos, testlaganda umuman yo'q mashina raqamini kiritib testlang",
     *     tags={"OsagoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="grossdan kelgan javob",
     *          @OA\JsonContent( type="object")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSendRequestToGetAccidentPolicy()
    {
        GeneralHelper::checkPermission();

        $model = new SendRequestToGetAccidentPolicyForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/osago/send-request-to-get-policy-status",
     *     summary="bu api grossda bu osagoni statusini aniqlash uchun kerak, response sifatida kelgan jsonni ekranga ko'rsatish kerak",
     *     tags={"OsagoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="grossdan kelgan javob",
     *          @OA\JsonContent( type="object")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSendRequestToGetPolicyStatus()
    {
        GeneralHelper::checkPermission();

        $model = new SendRequestToGetPolicyStatusForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/osago/update",
     *     summary="update osago",
     *     tags={"OsagoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="insurer_passport_series", type="string", example="AA", description="owner yoki applicant passport seriasi"),
     *                 @OA\Property (property="insurer_passport_number", type="string", example="1234567", description="owner yoki applicant passport nomeri"),
     *                 @OA\Property (property="insurer_birthday", type="string", example="25.12.1991", description="owner yoki applicant tug'ilgan sanasi, formt: dd.mm.YY"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated osago",
     *          @OA\JsonContent( type="object")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }


    /**
     * @OA\Get(
     *     path="/osago/summary",
     *     summary="Method to get summary by partner and product and number_drivers",
     *     tags={"OsagoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[begin_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[end_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[bridge_company_id]", in="query", @OA\Schema (type="integer"), description="16"),
     *     @OA\Response(
     *         response="200", description="summary",
     *         @OA\JsonContent(type="array", @OA\Items(type="object",
     *              @OA\Property(property="partner", ref="#components/schemas/id_name"),
     *              @OA\Property(property="product", ref="#components/schemas/id_name"),
     *              @OA\Property(property="number_drivers", ref="#components/schemas/id_name"),
     *              @OA\Property(property="success_count", type="integer", example="14"),
     *              @OA\Property(property="cancel_count", type="integer", example="14"),
     *              @OA\Property(property="success_amount", type="integer", example="60000000"),
     *              @OA\Property(property="cancel_amount", type="integer", example="500000"),
     *              @OA\Property(property="success_amount_without_discounts", type="integer", example="60000000"),
     *              @OA\Property(property="cancel_amount_without_discounts", type="integer", example="500000"),
     *              )
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSummary()
    {
        GeneralHelper::checkPermission();

        $model = new SummaryForm();
        $model->setAttributes($this->get['filter'] ?? []);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}