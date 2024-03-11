<?php

namespace frontend\controllers;

use frontend\models\AgentOsagoForms\LoginForm;
use yii\filters\VerbFilter;

class AgentOsagoController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['POST'],
            ]
        ];

        $behaviors['authenticator']['except'] = ["*"];

        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/agent-osago/login",
     *     summary="login as simpel user",
     *     tags={"AgentOsagoController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"phone_number", "key"},
     *                 @OA\Property (property="phone", type="string", example="998946464400", description="phone number which sent by agent"),
     *                 @OA\Property (property="key", type="string", example="qwerty", description="secret key by agent"),
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
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->send()->getWithUser();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}