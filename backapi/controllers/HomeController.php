<?php
namespace backapi\controllers;

use backapi\models\forms\homeForms\salesGraphForm;
use backapi\models\searchs\HomeStatisticsSearch;
use common\helpers\GeneralHelper;
use OpenApi\Generator;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;

class HomeController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
//                'login' => ['POST'],
            ],
        ];

        $behaviors['authenticator']['except'] = ["generate-doc"];

        return $behaviors;
    }
    /**
     * @OA\Get(
     *     path="/home/statistics",
     *     summary="Method to get all statistic info which in home page",
     *     tags={"HomeController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[begin_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[end_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[partner_id]", in="query", @OA\Schema (type="integer"), description="ro'yxatini partner/all API sidan oling"),
     *     @OA\Response(
     *         response="200", description="all statistic info",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="policies_of_process", type="object",
     *                  @OA\Property(property="total", type="integer", example=6),
     *                  @OA\Property(property="casco_in_progress", type="integer", example=4),
     *                  @OA\Property(property="osago_in_progress", type="integer", example=2),
     *              ),
     *              @OA\Property(property="users", type="integer", example=142),
     *              @OA\Property(property="weekly_increase_percent", type="integer", example=12),
     *              @OA\Property(property="monthly_product_count", type="object",
     *                  @OA\Property(property="total", type="integer", example=6),
     *                  @OA\Property(property="osago", type="integer", example=4),
     *                  @OA\Property(property="kasko", type="integer", example=2),
     *              ),
     *              @OA\Property(property="monthly_amount_sum", type="object",
     *                  @OA\Property(property="total", type="integer", example=6),
     *                  @OA\Property(property="osago", type="integer", example=4),
     *                  @OA\Property(property="kasko", type="integer", example=2),
     *              ),
     *              @OA\Property(property="average_check", type="integer", example=12),
     *              @OA\Property(property="top_partners", type="array", @OA\Items(type="object",
     *                      @OA\Property(property="name", type="string", example="ALFA LIFE"),
     *                      @OA\Property(property="product_count", type="integer", example=4),
     *                  )
     *              ),
     *              @OA\Property(property="top_auto", type="array", @OA\Items(type="object",
     *                      @OA\Property(property="autobrand", type="string", example="Chevrolet"),
     *                      @OA\Property(property="automodel", type="integer", example="Matiz"),
     *                      @OA\Property(property="count", type="integer", example=44),
     *                  )
     *              ),
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStatistics()
    {
        GeneralHelper::checkPermission();

        $filter = $this->get['filter'] ?? [];
        if (!array_key_exists('begin_date', $filter))
            $filter['begin_date'] = date('Y-m-01');

       $search_model = new HomeStatisticsSearch();
       $result = $search_model->search($filter);

       $result = array_merge($result, [
            'monthly_product_count' => $result['product_count'],
            'monthly_amount_sum' => $result['amount_sum'],
       ]);
       unset($result['product_count']);
       unset($result['amount_sum']);

       return $result;
    }

    public function actionGenerateDoc()
    {
        $openapi = Generator::scan([\Yii::getAlias('@backapi') . "/controllers"]);
        header('Content-Type: application/json');
        return json_decode($openapi->toJson(), JSON_FORCE_OBJECT);
    }

    /**
     * @OA\Get(
     *     path="/home/sales-graph",
     *     summary="Method to get graph coordinates which in home page",
     *     tags={"HomeController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="from_date", in="query", @OA\Schema (type="string"), description="shu chislodan boshlab koordinata nuqtalari beriladi, day bo'lsa 2022-12-30, month bo'lsa 2022-12"),
     *     @OA\Parameter (name="till_date", in="query", @OA\Schema (type="string"), description="shu chislogacha sotilganlar koordinata sifatida beriladi, day bo'lsa 2022-12-30, month bo'lsa 2022-12"),
     *     @OA\Parameter (name="interval", in="query", @OA\Schema (type="string"), description="day yomi month"),
     *     @OA\Parameter (name="type", in="query", @OA\Schema (type="integer"), description="0 => soni, 1 => puli"),
     *     @OA\Parameter (name="partner_id", in="query", @OA\Schema (type="integer"), description="ro'yxatini partner/all API sidan oling"),
     *     @OA\Response(
     *         response="200", description="graph coordinates",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="graph", type="array", @OA\Items(type="object",
     *                      @OA\Property(property="osago", type="array", @OA\Items(type="object",
     *                          @OA\Property(property="interval", type="string", example="2022-12-30|2022-12"),
     *                          @OA\Property(property="count", type="integer", example="25")
     *                      )),
     *                      @OA\Property(property="kasko", type="array", @OA\Items(type="object",
     *                          @OA\Property(property="interval", type="string", example="2022-12-30|2022-12"),
     *                          @OA\Property(property="count", type="integer", example="25")
     *                      )),
     *                  )
     *              ),
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSalesGraph()
    {
        GeneralHelper::checkPermission();

        $model = new salesGraphForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}