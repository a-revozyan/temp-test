<?php
namespace backapi\controllers;

use backapi\models\searchs\KaskoRiskCategorySearch;
use common\helpers\GeneralHelper;
use yii\filters\VerbFilter;

class KaskoRiskCategoryController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
//                'create' => ['POST'],
//                'update' => ['POST'],
//                'delete' => ['DELETE'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/kasko-risk-category/all",
     *     summary="Method to get all kasko-risk-categories with or without pagination ",
     *     tags={"KaskoRiskCategoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="kasko-risk-categories with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/id_name")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="kasko-risk-categories without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/id_name")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new KaskoRiskCategorySearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => $dataProvider->getModels(),
            'pages' => $dataProvider->getPagination()
        ];
    }
}