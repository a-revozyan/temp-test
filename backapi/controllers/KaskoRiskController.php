<?php
namespace backapi\controllers;

use backapi\models\forms\kaskorisk\CreateKaskoRiskForm;
use backapi\models\forms\kaskorisk\UpdateKaskoRiskForm;
use backapi\models\searchs\KaskoRiskSearch;
use common\helpers\GeneralHelper;
use common\models\Autocomp;
use common\models\KaskoRisk;
use XLSXWriter;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class KaskoRiskController extends BaseController
{
    public $row_respons_action_ids = ["kasko-risk-export"];
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
     *     path="/kasko-risk/all",
     *     summary="Method to get all kasko risks with or without pagination ",
     *     tags={"KaskoRiskController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from id, name_uz, name_ru, name_en, description_en, description_ru, description_uz, name of kasko_risk_category, amount"),
     *     @OA\Parameter (name="filter[category_id]", in="query", @OA\Schema (type="integer"), description="get those ids from kasko-risk-category/all"),
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="integer"), description="name_uz, name_ru, name_en"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name_uz, name_ru, name_en, amount. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="kasko risk with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/kasko_risk")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="kasko risk without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/kasko_risk")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new KaskoRiskSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => KaskoRisk::getFullArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/kasko-risk/kasko-risk-export",
     *     summary="Method to get all kasko risks excel",
     *     tags={"KaskoRiskController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from id, name_uz, name_ru, name_en, description_en, description_ru, description_uz, name of kasko_risk_category, amount"),
     *     @OA\Parameter (name="filter[category_id]", in="query", @OA\Schema (type="integer"), description="get those ids from kasko-risk-category/all"),
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="integer"), description="name_uz, name_ru, name_en"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name_uz, name_ru, name_en, amount. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="excel string",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionKaskoRiskExport()
    {
        GeneralHelper::checkPermission();

        return GeneralHelper::export(KaskoRisk::className(), KaskoRiskSearch::className(), [
            'id' => 'integer',
            'name_ru' => 'string',
            'name_uz' => 'string',
            'name_en' => 'string',
            'category_name' => 'string',
            'amount' => 'integer',
        ], [
            'id',
            'name_ru', 'name_uz', 'name_en',
            'category_name' => function($model){
                return $model->category->name ?? "";
            },
            'amount'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/kasko-risk/create",
     *     summary="create new kasko risk",
     *     tags={"KaskoRiskController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name_ru", "name_uz", "name_en", "amount", "show_desc"},
     *                 @OA\Property (property="name_ru", type="string", example="new risk"),
     *                 @OA\Property (property="name_en", type="string", example="new risk"),
     *                 @OA\Property (property="name_uz", type="string", example="new risk"),
     *                 @OA\Property (property="description_uz", type="string", example="new risk description"),
     *                 @OA\Property (property="description_ru", type="string", example="new risk description"),
     *                 @OA\Property (property="description_en", type="string", example="new risk description"),
     *                 @OA\Property (property="amount", type="float", example=2.2),
     *                 @OA\Property (property="category_id", type="iteger|null", example=1, description="get those ids from kasko-risk-category/all"),
     *                 @OA\Property (property="show_desc", type="iteger", example=1, description="0 or 1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created kasko risk",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/kasko_risk")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateKaskoRiskForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/kasko-risk/update",
     *     summary="udpate kasko risk",
     *     tags={"KaskoRiskController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                required={"name_ru", "name_uz", "name_en", "amount", "show_desc", "id"},
     *                 @OA\Property (property="id", type="integer", example=12),
     *                 @OA\Property (property="name_ru", type="string", example="new risk"),
     *                 @OA\Property (property="name_en", type="string", example="new risk"),
     *                 @OA\Property (property="name_uz", type="string", example="new risk"),
     *                 @OA\Property (property="description_uz", type="string", example="new risk description"),
     *                 @OA\Property (property="description_ru", type="string", example="new risk description"),
     *                 @OA\Property (property="description_en", type="string", example="new risk description"),
     *                 @OA\Property (property="amount", type="float", example=2.2),
     *                 @OA\Property (property="category_id", type="iteger|null", example=1, description="get those ids from kasko-risk-category/all"),
     *                 @OA\Property (property="show_desc", type="iteger", example=1, description="0 or 1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated kasko risk",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/kasko_risk")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateKaskoRiskForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/kasko-risk/get-by-id",
     *     summary="get kasko risk by id",
     *     tags={"KaskoRiskController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="automodel",
     *         @OA\JsonContent(type="object", ref="#components/schemas/kasko_risk")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id)
            return KaskoRisk::findOne($id);

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/kasko-risk/delete",
     *     summary="Method to delete kasko risk by id",
     *     tags={"KaskoRiskController"},
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

        if (!(!is_null($id) and is_numeric($id) and (int)$id == $id and $kaskoRisk = KaskoRisk::findOne($id)))
            throw new BadRequestHttpException("ID is incorrect");

        return $kaskoRisk->delete();
    }
}