<?php
namespace backapi\controllers;

use backapi\models\forms\autoRiskTypeForms\CreateAutoRiskTypeForm;
use backapi\models\forms\autoRiskTypeForms\UpdateAutoRiskTypeForm;
use backapi\models\searchs\AutoRiskTypeSearch;
use common\helpers\GeneralHelper;
use common\models\AutoRiskType;
use XLSXWriter;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class AutoRiskTypeController extends BaseController
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
     *     path="/auto-risk-type/all",
     *     summary="Method to get all auto risk types with or without pagination ",
     *     tags={"AutoRiskTypeController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from name and id"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name, status. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="auto risk types with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/auto_risk_type")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="auto risk types without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/auto_risk_type")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new AutoRiskTypeSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => AutoRiskType::getFullArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/auto-risk-type/export",
     *     summary="Method to get all auto risk types excel",
     *     tags={"AutoRiskTypeController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from name and id"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name, status. use '-' for descending"),
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

        return GeneralHelper::export(AutoRiskType::className(), AutoRiskTypeSearch::className(), [
            'id' => 'integer',
            'name' => 'string',
            'status' => 'string',
        ], [
            'id',
            'name',
            'status' => function($model){
                return array_flip(AutoRiskType::STATUS)[$model->status] ?? "";
            },
        ]);
    }

    /**
     * @OA\Post(
     *     path="/auto-risk-type/create",
     *     summary="create new auto risk type",
     *     tags={"AutoRiskTypeController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "status"},
     *                 @OA\Property (property="name", type="string", example="eletro car"),
     *                 @OA\Property (property="status", type="iteger", example=1),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created auto risk type",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/auto_risk_type")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateAutoRiskTypeForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/auto-risk-type/update",
     *     summary="create new auto risk type",
     *     tags={"AutoRiskTypeController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "name", "status"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="name", type="string", example="eletro car"),
     *                 @OA\Property (property="status", type="iteger", example=1),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated autorisktype",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/auto_risk_type")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateAutoRiskTypeForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/auto-risk-type/get-by-id",
     *     summary="get auto risk type by id",
     *     tags={"AutoRiskTypeController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="auto risk type",
     *         @OA\JsonContent(type="object", ref="#components/schemas/auto_risk_type")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id)
            return AutoRiskType::findOne($id)->getFullArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/auto-risk-type/delete",
     *     summary="Method to delete auto risk type by id",
     *     tags={"AutoRiskTypeController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $auto_risk_type = AutoRiskType::findOne($id))
            return $auto_risk_type->delete();

        throw new BadRequestHttpException("ID is incorrect");
    }
}
