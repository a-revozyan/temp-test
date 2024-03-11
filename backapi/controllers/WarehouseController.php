<?php
namespace backapi\controllers;

use backapi\models\forms\warehouseForms\CreateWarehouseForm;
use backapi\models\forms\warehouseForms\UpdateWarehouseForm;
use backapi\models\searchs\WarehouseSearch;
use common\helpers\GeneralHelper;
use common\models\Warehouse;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class WarehouseController extends BaseController
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
     *     path="/warehouse/all",
     *     summary="Method to get all warehouses with or without pagination ",
     *     tags={"WarehouseController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from searies and number"),
     *     @OA\Parameter (name="filter[partner_id]", in="query", @OA\Schema (type="integer"), description="send id of partner to get warehouses which are realted to the partner"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="new => 0, reserve => 1, paid => 2, cancel => 3"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, status, partner_id. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="warehouses with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/warehouse")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="warehouses without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/warehouse")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new WarehouseSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Warehouse::getFullArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/warehouse/export",
     *     summary="Method to get all warehouses excel",
     *     tags={"WarehouseController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from searies and number"),
     *     @OA\Parameter (name="filter[partner_id]", in="query", @OA\Schema (type="integer"), description="send id of partner to get warehouses which are realted to the partner"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="new => 0, reserve => 1, paid => 2, cancel => 3"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, status, partner_id. use '-' for descending"),
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

        return GeneralHelper::export(Warehouse::className(), WarehouseSearch::className(), [
            'id' => 'integer',
            'series' => 'string',
            'number' => 'string',
            'partner' => 'string',
            'status' => 'string',
        ], [
            'id',
            'series',
            'number',
            'partner' => function($model){
                return $model->partner->name ?? "";
            },
            'status' => function($model){
                return array_flip(Warehouse::STATUS)[$model->status] ?? "";
            },
        ]);
    }

    /**
     * @OA\Post(
     *     path="/warehouse/create",
     *     summary="create new warehouse",
     *     tags={"WarehouseController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"partner_id", "series", "number", "status"},
     *                 @OA\Property (property="partner_id", type="integer", example="1", description="ro'yxatini partner/all api sidan olish mumkin"),
     *                 @OA\Property (property="series", type="string", example="GSS"),
     *                 @OA\Property (property="number", type="string", example="3456234"),
     *                 @OA\Property (property="status", type="iteger", example=1, description="new => 0, reserve => 1, paid => 2, cancel => 3"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created warehouse",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/warehouse")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateWarehouseForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/warehouse/update",
     *     summary="udpate warehouse",
     *     tags={"WarehouseController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"warehouse_id", "partner_id", "series", "number", "status"},
     *                 @OA\Property (property="warehouse_id", type="integer", example="1", description="update qilinayotgan warehouse id si"),
     *                 @OA\Property (property="partner_id", type="integer", example="1", description="ro'yxatini partner/all api sidan olish mumkin"),
     *                 @OA\Property (property="series", type="string", example="GSS"),
     *                 @OA\Property (property="number", type="string", example="3456234"),
     *                 @OA\Property (property="status", type="iteger", example=1, description="new => 0, reserve => 1, paid => 2, cancel => 3"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated warehouse",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/warehouse")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateWarehouseForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/warehouse/get-by-id",
     *     summary="get warehouse by id",
     *     tags={"WarehouseController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="warehouse",
     *         @OA\JsonContent(type="object", ref="#components/schemas/warehouse")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $warehouse = Warehouse::findOne($id))
            return $warehouse->getFullArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/warehouse/delete",
     *     summary="Method to delete warehouse by id",
     *     tags={"WarehouseController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $warehouse = Warehouse::findOne($id))
        {
            return $warehouse->delete();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }
}