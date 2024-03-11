<?php
namespace backapi\controllers;

use backapi\models\forms\travelForms\ChangeStatusForm;
use backapi\models\forms\travelForms\SendRequestToGetPolicyForm;
use backapi\models\forms\osagoForms\SendRequestToGetPolicyStatusForm;
use backapi\models\forms\osagoForms\UpdateForm;
use common\helpers\GeneralHelper;
use mdm\admin\components\Helper;
use Yii;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;

class TravelController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'change-status' => ['PUT'],
                'send-request-to-get-policy' => ['POST'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Put(
     *     path="/travel/change-status",
     *     summary="change status travel",
     *     tags={"TravelController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "status"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="status", type="integer", example="7", description="o'zgartirilishi kerak bo'lgan status. hozircha faqat 7(cancel)"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated travel",
     *          @OA\JsonContent( type="object")
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
     *     path="/travel/send-request-to-get-policy",
     *     summary="grossdan polis olish. confirmation qo'yish kerak",
     *     tags={"TravelController"},
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
     *     path="/travel/send-request-to-get-policy-status",
     *     summary="bu api grossda bu travelni statusini aniqlash uchun kerak, response sifatida kelgan jsonni ekranga ko'rsatish kerak",
     *     tags={"TravelController"},
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
//        GeneralHelper::checkPermission();
//
//        $model = new SendRequestToGetPolicyStatusForm();
//        $model->setAttributes($this->put);
//        if ($model->validate())
//            return $model->save();
//
//        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}