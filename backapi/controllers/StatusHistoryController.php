<?php
namespace backapi\controllers;

use backapi\models\searchs\StatusHistorySearch;
use common\models\StatusHistory;
use yii\filters\VerbFilter;

class StatusHistoryController extends BaseController
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
     *     path="/status-history/all",
     *     summary="Method to get all sms histories with or without pagination",
     *     tags={"StatusHistoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[model_class]", in="query", @OA\Schema (type="string"), description="Osago, KaskoBySubscription, Kasko, Travel"),
     *     @OA\Parameter (name="filter[model_id]", in="query", @OA\Schema (type="integer"), description="1234"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="sms histories with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/status_history")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        $searchModel = new StatusHistorySearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => StatusHistory::getFullArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }
}