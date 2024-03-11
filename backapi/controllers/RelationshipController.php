<?php
namespace backapi\controllers;

use backapi\models\searchs\RelationshipSearch;
use common\helpers\GeneralHelper;
use common\models\Relationship;
use yii\filters\VerbFilter;

class RelationshipController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
//                'create' => ['POST'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/relationship/all",
     *     summary="Method to get all relationships with or without pagination ",
     *     tags={"RelationshipController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from name_uz, name_en, name_ur"),
     *     @OA\Parameter (name="filter[for_select]", in="query", @OA\Schema (type="integer"), description="send 1 to get for select, for table do not send"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name_uz, name_ru, name_en. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="products with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name_ru", type="string|null", example="Младшая сестра"),
     *                  @OA\Property(property="name_uz", type="string|null", example="Singlisi"),
     *                  @OA\Property(property="name_en", type="string|null", example="Sister"),
     *              )),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *      @OA\Response(
     *         response="200_2", description="products with pagination for select",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string|null", example="Младшая сестра"),
     *              )),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new RelationshipSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => $dataProvider->getModels(),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/relationship/export",
     *     summary="Method to get all relationships excel",
     *     tags={"RelationshipController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from name_uz, name_en, name_ur"),
     *     @OA\Parameter (name="filter[for_select]", in="query", @OA\Schema (type="integer"), description="send 1 to get for select, for table do not send"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name_uz, name_ru, name_en. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="products with pagination for table",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionExport()
    {
        GeneralHelper::checkPermission();

        return GeneralHelper::export(Relationship::className(), RelationshipSearch::className(), [
                'id' => 'integer',
                'name_ru' => 'string',
                'name_uz' => 'string',
                'name_en' => 'string',
            ]);
    }
}