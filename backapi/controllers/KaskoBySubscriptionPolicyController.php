<?php
namespace backapi\controllers;

use backapi\models\forms\kaskoBySubscriptionPolicyForms\ChangeStatusForm;
use common\helpers\GeneralHelper;
use yii\filters\VerbFilter;

class KaskoBySubscriptionPolicyController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'change-status' => ['PUT']
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Put(
     *     path="/kasko-by-subscription-policy/change-status",
     *     summary="change status osago",
     *     tags={"KaskoBySubscriptionPolicyController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "status"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="status", type="integer", example="5", description="o'zgartirilishi kerak bo'lgan status. hozircha faqat 5 => cancel"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated kbs",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/kasko_by_subscription")
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
}