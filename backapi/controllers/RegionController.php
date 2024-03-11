<?php
namespace backapi\controllers;

use backapi\models\searchs\RegionSearch;
use backapi\models\searchs\SurveyerSearch;
use common\helpers\GeneralHelper;
use common\models\Region;
use yii\filters\VerbFilter;

class RegionController extends BaseController
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
     *     path="/region/all",
     *     summary="Method to get all regions with or without pagination ",
     *     tags={"RegionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from name"),
     *     @OA\Parameter (name="filter[for_select]", in="query", @OA\Schema (type="integer"), description="for table do not send, for select send 1"),
     *     @OA\Response(
     *         response="200_1", description="products without pagination for select",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#components/schemas/region_short")),
     *              @OA\Property (property="pages", type="boolean", example="false")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="products with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#components/schemas/region")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new RegionSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => $dataProvider->getModels(),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/region/export",
     *     summary="Method to get excel contain all regions",
     *     tags={"RegionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from name"),
     *     @OA\Response(
     *         response="200", description="string which excel file contain",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionExport()
    {
        GeneralHelper::checkPermission();

        return GeneralHelper::export(Region::className(), RegionSearch::className(), [
            'id' => 'integer',
            'name_ru' => 'string',
            'name_en' => 'string',
            'name_uz' => 'string',
            'coeff' => 'string',
        ]);
    }
}