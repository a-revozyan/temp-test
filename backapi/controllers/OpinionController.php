<?php
namespace backapi\controllers;

use backapi\models\searchs\OpinionSearch;
use common\helpers\GeneralHelper;
use common\models\Opinion;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class OpinionController extends BaseController
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
     *     path="/opinion/all",
     *     summary="Method to get all opinions with or without pagination ",
     *     tags={"OpinionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from name, phone, message"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name, phone. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="opinions with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/opinion")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new OpinionSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Opinion::getShortArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/opinion/export",
     *     summary="Method to get all opinion excel",
     *     tags={"OpinionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from name, phone, message"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name, phone. use '-' for descending"),
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
        return GeneralHelper::export(Opinion::className(), OpinionSearch::className(), [
            'id' => 'integer',
            'name' => 'string',
            'phone' => 'string',
            'message' => 'string',
            'created_at' => 'string',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/opinion/get-by-id",
     *     summary="get opinion by id",
     *     tags={"OpinionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="opinion",
     *         @OA\JsonContent(type="object", ref="#components/schemas/opinion")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $opinion = Opinion::findOne($id))
            return $opinion->getShortArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/opinion/delete",
     *     summary="Method to delete opinion by id",
     *     tags={"OpinionController"},
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
        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $opinion = Opinion::findOne($id))
            return $opinion->delete();

        throw new BadRequestHttpException("ID is incorrect");
    }

}