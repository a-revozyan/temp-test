<?php
namespace frontend\controllers;

use common\models\Story;
use frontend\models\Searchs\StorySearch;
use yii\filters\VerbFilter;

class StoryController extends BaseController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
            ]
        ];

        $behaviors['authenticator']['only'] = ["all"];
//        $behaviors['authenticator']['only'] = [];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/story/all",
     *     summary="stories",
     *     tags={"StoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="filter[type]", in="query", @OA\Schema (type="integer"), description="1 => reel, 0 => story"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *          response="200_1", description="stories with pagination",
     *          @OA\JsonContent(
     *               @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/story")),
     *               @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *         )
     *      ),
     *      @OA\Response(
     *          response="200_2", description="stories without pagination",
     *          @OA\JsonContent(
     *               @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/story")),
     *               @OA\Property (property="pages", type="boolean", example=false)
     *         )
     *      ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionAll()
    {
        $searchModel = new StorySearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Story::getFullAdminArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }
}