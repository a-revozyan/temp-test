<?php

namespace saas\controllers;

use saas\models\CarPrice\CalculateReserveRequestsForm;
use saas\models\CarPrice\CreateCarPriceRequestForm;
use yii\filters\VerbFilter;

class CarPriceController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create-request' => ['POST'],
            ],
        ];

//        $behaviors['basicAuth']['only'] = [""];
        $behaviors['basicAuth']['only'] = ['create-request'];
        $behaviors['authenticator']['only'] = ['create-request-as-simple-user', 'reserve-requests'];
        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/car-price/create-request",
     *     summary="create new car price request",
     *     tags={"CarPriceController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"brand", "model", "type", "engine", "year", "mileage", "capacity", "average_price", "among_cars_count"},
     *                 @OA\Property (property="brand", type="string", example="chevrolet"),
     *                 @OA\Property (property="model", type="string", example="nexia"),
     *                 @OA\Property (property="type", type="string", example="Mexanika"),
     *                 @OA\Property (property="engine", type="string", example="Benzin"),
     *                 @OA\Property (property="year", type="integer", example="2021"),
     *                 @OA\Property (property="mileage", type="iteger", example="10000"),
     *                 @OA\Property (property="capacity", type="float", example="1.5"),
     *                 @OA\Property (property="average_price", type="string", example="matiz"),
     *                 @OA\Property (property="among_cars_count", type="string", example="matiz"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="true",
     *          @OA\JsonContent( type="boolean", example="true")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreateRequest()
    {
        $model = new CreateCarPriceRequestForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/car-price/create-request-as-simple-user",
     *     summary="create new car price request",
     *     tags={"CarPriceController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"brand", "model", "type", "engine", "year", "mileage", "capacity", "average_price", "among_cars_count"},
     *                 @OA\Property (property="brand", type="string", example="chevrolet"),
     *                 @OA\Property (property="model", type="string", example="nexia"),
     *                 @OA\Property (property="type", type="string", example="Mexanika"),
     *                 @OA\Property (property="engine", type="string", example="Benzin"),
     *                 @OA\Property (property="year", type="integer", example="2021"),
     *                 @OA\Property (property="mileage", type="iteger", example="10000"),
     *                 @OA\Property (property="capacity", type="float", example="1.5"),
     *                 @OA\Property (property="average_price", type="string", example="matiz"),
     *                 @OA\Property (property="among_cars_count", type="string", example="matiz"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="true",
     *          @OA\JsonContent( type="boolean", example="true")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreateRequestAsSimpleUser()
    {
        $model = new CreateCarPriceRequestForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/car-price/reserve-requests",
     *     summary="now, how many reserve requests are there",
     *     tags={"CarPriceController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *
     *     @OA\Response(response="200", description="reserve requests",
     *          @OA\JsonContent( type="object",
     *              @OA\Property(property="reserve_requests", type="integer", example=25),
     *          )
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionReserveRequests()
    {
        $model = new CalculateReserveRequestsForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

}