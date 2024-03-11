<?php
namespace backapi\controllers;

use backapi\models\forms\carPriceForm\changePaidForm;
use backapi\models\forms\carPriceForm\partnerMonthlyPaidForm;
use backapi\models\forms\carPriceForm\requestGraphForm;
use common\helpers\GeneralHelper;
use yii\filters\VerbFilter;

class CarPriceController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'change-paid' => ['PUT'],
            ],
        ];

//        $behaviors['authenticator']['except'] = ['import-file'];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/car-price/statistics",
     *     summary="Method to get statistics and graph",
     *     tags={"CarPriceController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="from_date", in="query", @OA\Schema (type="string"), description="2023-03"),
     *     @OA\Parameter (name="till_date", in="query", @OA\Schema (type="string"), description="2023-03"),
     *     @OA\Parameter (name="partner_id", in="query", @OA\Schema (type="integer"), description="ex:1. this field is required"),
     *     @OA\Response(
     *         response="200", description="graph coordinates",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="graph", type="object",
     *                      @OA\Property(property="gross", type="array", @OA\Items(type="object",
     *                          @OA\Property(property="interval", type="string", example="2022-12-30|2022-12"),
     *                          @OA\Property(property="count", type="integer", example="25")
     *                      )),
     *                      @OA\Property(property="apex", type="array", @OA\Items(type="object",
     *                          @OA\Property(property="interval", type="string", example="2022-12-30|2022-12"),
     *                          @OA\Property(property="count", type="integer", example="25")
     *                      )),
     *              ),
     *              @OA\Property(property="statistics", type="object",
     *                      @OA\Property(property="current_year", type="array", @OA\Items(type="object",
     *                          @OA\Property(property="partner_name", type="string", example="2022-12-30|2022-12"),
     *                          @OA\Property(property="count", type="integer", example="25")
     *                      )),
     *                      @OA\Property(property="current_month", type="array", @OA\Items(type="object",
     *                          @OA\Property(property="partner_name", type="string", example="2022-12-30|2022-12"),
     *                          @OA\Property(property="count", type="integer", example="25")
     *                      )),
     *                      @OA\Property(property="period", type="array", @OA\Items(type="object",
     *                          @OA\Property(property="partner_name", type="string", example="2022-12-30|2022-12"),
     *                          @OA\Property(property="count", type="integer", example="25")
     *                      )),
     *                      @OA\Property(property="top_auto_models", type="array", @OA\Items(type="object",
     *                            @OA\Property(property="auto_model_name", type="integer", example="nexia"),
     *                            @OA\Property(property="count", type="integer", example="38"),
     *                      )),
     *              ),
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStatistics()
    {
        GeneralHelper::checkPermission();

        $model = new requestGraphForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }


    /**
     * @OA\Get(
     *     path="/car-price/partner-monthly-paid",
     *     summary="Method to get partner months request couns with paid or not paid",
     *     tags={"CarPriceController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="from_date", in="query", @OA\Schema (type="string"), description="2023-03"),
     *     @OA\Parameter (name="till_date", in="query", @OA\Schema (type="string"), description="2023-03"),
     *     @OA\Parameter (name="partner_id", in="query", @OA\Schema (type="integer"), description="ex:1. this field is required"),
     *      @OA\Response(
     *         response="200", description="",
     *         @OA\JsonContent(type="array", @OA\Items(type="object",
     *                     @OA\Property(property="month", type="integer", example="2023-08"),
     *                     @OA\Property(property="count", type="integer", example="38"),
     *                     @OA\Property(property="is_paid", type="integer", example="true"),
     *              )
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionPartnerMonthlyPaid()
    {
        GeneralHelper::checkPermission();

        $model = new partnerMonthlyPaidForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/car-price/change-paid",
     *     summary="Method to change paid or not",
     *     tags={"CarPriceController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"partner_id", "month"},
     *                 @OA\Property (property="partner_id", type="integer", example=18),
     *                 @OA\Property (property="month", type="string", description="2023-12"),
     *             )
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"partner_id", "month"},
     *                 @OA\Property (property="partner_id", type="integer", example=18),
     *                 @OA\Property (property="month", type="array", @OA\Items(type="integer", example=2), description="osago => 1, kasko => 2, travel => 3"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="is paid",
     *          @OA\JsonContent( type="boolean", example="true")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionChangePaid()
    {
        GeneralHelper::checkPermission();

        $model = new changePaidForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

}