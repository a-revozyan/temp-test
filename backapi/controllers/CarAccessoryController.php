<?php
namespace backapi\controllers;

use backapi\models\forms\carAccessoryForms\CreateCarAccessoryForm;
use backapi\models\forms\carAccessoryForms\UpdateCarAccessoryForm;
use backapi\models\searchs\CarAccessorySearch;
use common\helpers\GeneralHelper;
use common\models\CarAccessory;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class CarAccessoryController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['POST'],
                'update' => ['PUT'],
                'delete' => ['DELETE'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/car-accessory/all",
     *     summary="Method to get all car accessories with or without pagination ",
     *     tags={"CarAccessoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from name uz en ru"),
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from name uz en ru, description uz en ru, id"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name_uz, name_ru, name_en, description_uz, description_ru, description_en. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="car accessories with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/car_accessory")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="car accessories without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/car_accessory")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new CarAccessorySearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => $dataProvider->getModels(),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/car-accessory/export",
     *     summary="Method to get all car accessories excel",
     *     tags={"CarAccessoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from name uz en ru"),
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from name uz en ru, description uz en ru, id"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name_uz, name_ru, name_en, description_uz, description_ru, description_en. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="excel string",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionExport()
    {
        GeneralHelper::checkPermission();

        return GeneralHelper::export(CarAccessory::className(), CarAccessorySearch::className(), [
            'id' => 'integer',
            'name_ru' => 'string',
            'name_en' => 'string',
            'name_uz' => 'string',
            'description_uz' => 'string',
            'description_en' => 'string',
            'description_ru' => 'string',
            'amount_min' => 'string',
            'amount_max' => 'string',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/car-accessory/create",
     *     summary="create new car accessory",
     *     tags={"CarAccessoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                required={"name_ru", "name_uz", "name_en", "amount_min", "amount_max"},
     *                 @OA\Property (property="name_ru", type="string", example="new accessory"),
     *                 @OA\Property (property="name_en", type="string", example="new accessory"),
     *                 @OA\Property (property="name_uz", type="string", example="new accessory"),
     *                 @OA\Property (property="description_uz", type="string", example="new accessory description"),
     *                 @OA\Property (property="description_ru", type="string", example="new accessory description"),
     *                 @OA\Property (property="description_en", type="string", example="new accessory description"),
     *                 @OA\Property (property="amount_min", type="float", example=2.2),
     *                 @OA\Property (property="amount_max", type="float", example=5.2),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created accessory",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/car_accessory")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateCarAccessoryForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/car-accessory/update",
     *     summary="update car accessory",
     *     tags={"CarAccessoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                required={"id", "name_ru", "name_uz", "name_en", "amount_min", "amount_max"},
     *                 @OA\Property (property="id", type="integer", example="12"),
     *                 @OA\Property (property="name_ru", type="string", example="new accessory"),
     *                 @OA\Property (property="name_en", type="string", example="new accessory"),
     *                 @OA\Property (property="name_uz", type="string", example="new accessory"),
     *                 @OA\Property (property="description_uz", type="string", example="new accessory description"),
     *                 @OA\Property (property="description_ru", type="string", example="new accessory description"),
     *                 @OA\Property (property="description_en", type="string", example="new accessory description"),
     *                 @OA\Property (property="amount_min", type="float", example=2.2),
     *                 @OA\Property (property="amount_max", type="float", example=5.2),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated accessory",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/car_accessory")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateCarAccessoryForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/car-accessory/get-by-id",
     *     summary="get car accessory by id",
     *     tags={"CarAccessoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="automodel",
     *         @OA\JsonContent(type="object", ref="#components/schemas/automodel")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id)
            return CarAccessory::findOne($id);

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/car-accessories/delete",
     *     summary="Method to delete car accessory by id",
     *     tags={"CarAccessoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="return 1 if successfully deleted",
     *         @OA\JsonContent(type="integer", example=1)
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionDelete($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $car_accessory = CarAccessory::findOne($id))
        {
            \Yii::$app->db->createCommand()->delete('tariff_car_accessory_coeff', ['car_accessory_id' => $id])->execute();
            return $car_accessory->delete();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }

}