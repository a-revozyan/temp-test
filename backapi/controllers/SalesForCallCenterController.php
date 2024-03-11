<?php
namespace backapi\controllers;

use backapi\models\forms\salesForCallCenterForms\CallForm;
use backapi\models\forms\salesForCallCenterForms\ReasonStatisticsForm;
use backapi\models\forms\salesForCallCenterForms\UpdateProductForm;
use backapi\models\searchs\CallCenterProductsSearch;
use backapi\models\searchs\SaleProductsSearch;
use common\helpers\GeneralHelper;
use common\models\Accident;
use common\models\Kasko;
use common\models\KaskoBySubscriptionPolicy;
use common\models\Osago;
use common\models\Product;
use common\models\Travel;
use XLSXWriter;
use yii\filters\VerbFilter;

class SalesForCallCenterController extends BaseController
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

        $behaviors['authenticator']['except'] = [""];

        return $behaviors;
    }

    public function beforeAction($action)
    {
        $result = parent::beforeAction($action);

        if ($action->id == "export")
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

        return $result;
    }

    /**
     * @OA\Get(
     *     path="/sales-for-call-center/statistics",
     *     summary="Method to get all statistic info for 'sales for call center'  page",
     *     tags={"SalesForCallCenterController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[from_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[till_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Response(
     *         response="200", description="all statistic info",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="reason_percents", type="array", @OA\Items(type="object",
     *                  @OA\Property(property="reason_id", type="integer", example=1),
     *                  @OA\Property(property="percent", type="integer", example=51),
     *              )),
     *              @OA\Property(property="reasons", type="array", @OA\Items(type="object", ref="#/components/schemas/id_name_status")),
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStatistics()
    {
        $model = new ReasonStatisticsForm();
        $model->setAttributes($this->get['filter'] ?? []);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/sales-for-call-center/products",
     *     summary="Method to get all products for sales page",
     *     tags={"SalesForCallCenterController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search by autonumber, f_user_phone"),
     *     @OA\Parameter (name="filter[product]", in="query", @OA\Schema (type="integer"), description="osago => 1, kasko => 2, travel => 3"),
     *     @OA\Parameter (name="filter[region]", in="query", @OA\Schema (type="string"), description="01 => toshkent, 80 => buxoro, va hokazo"),
     *     @OA\Parameter (name="filter[policy_generated_date][gte]", in="query", @OA\Schema (type="integer"), description="policy_generated_date yuborilgandan katta yoki tenglari"),
     *     @OA\Parameter (name="filter[policy_generated_date][lte]", in="query", @OA\Schema (type="integer"), description="policy_generated_date yuborilgandan kichik yoki tenglari"),
     *     @OA\Parameter (name="filter[partner_id]", in="query", @OA\Schema (type="integer")),
     *     @OA\Parameter (name="filter[payment_type]", in="query", @OA\Schema (type="string"), description="click, payme, payze"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="1, 2, 3 ... "),
     *     @OA\Parameter (name="filter[agent_id]", in="query", @OA\Schema (type="integer"), description="get by id of agent"),
     *     @OA\Parameter (name="filter[f_user_id]", in="query", @OA\Schema (type="integer"), description="get by id of user which is create order"),
     *     @OA\Parameter (name="filter[reason_id]", in="query", @OA\Schema (type="integer"), example="12"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: policy_generated_date, amount_uzs. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="products with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#components/schemas/product_for_call_center")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionProducts()
    {
        GeneralHelper::checkPermission();

        $searchModel = new CallCenterProductsSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Product::getShortArrCollectionForCallCenter($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination(),
        ];
    }

    /**
     * @OA\Get(
     *     path="/sales-for-call-center/export",
     *     summary="Method to get excel with all products in sales page",
     *     tags={"SalesForCallCenterController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search by autonumber, f_user_phone"),
     *     @OA\Parameter (name="filter[product_id]", in="query", @OA\Schema (type="integer"), description="osago => 1, kasko => 2, travel => 3"),
     *     @OA\Parameter (name="filter[region]", in="query", @OA\Schema (type="string"), description="01 => toshkent, 80 => buxoro, va hokazo"),
     *     @OA\Parameter (name="filter[policy_generated_date][gte]", in="query", @OA\Schema (type="integer"), description="policy_generated_date yuborilgandan katta yoki tenglari"),
     *     @OA\Parameter (name="filter[policy_generated_date][lte]", in="query", @OA\Schema (type="integer"), description="policy_generated_date yuborilgandan kichik yoki tenglari"),
     *     @OA\Parameter (name="filter[partner_id]", in="query", @OA\Schema (type="integer")),
     *     @OA\Parameter (name="filter[payment_type]", in="query", @OA\Schema (type="string"), description="click, payme, payze"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="1, 2, 3 ... "),
     *     @OA\Parameter (name="filter[agent.id]", in="query", @OA\Schema (type="integer"), description="get by id of agent"),
     *     @OA\Parameter (name="filter[products.f_user_id]", in="query", @OA\Schema (type="integer"), description="get by id of user which is create order"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: policy_generated_date, amount_uzs. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="excel containing string",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionExport()
    {
        GeneralHelper::checkPermission();

        $searchModel = new SaleProductsSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);
        $products = $dataProvider->getModels();

        $_products = [];
        foreach ($products as $product)
        {
            $_product = [];
            $_product[] = array_flip(Product::products)[$product['product']] ?? '';
            $_product[] = empty($product['policy_generated_date']) ? '' : date('Y-m-d H:i:s', $product['policy_generated_date']);
            $_product[] = (string) $product['f_user_phone'];
            if ($product['product'] == Product::products['kasko'])
                $_product[] = array_flip(Kasko::STATUS)[$product['status']] ?? '';
            elseif ($product['product'] == Product::products['travel'])
                $_product[] = array_flip(Travel::STATUSES)[$product['status']] ?? '';
            elseif ($product['product'] == Product::products['osago'])
                $_product[] = array_flip(Osago::STATUS)[$product['status']] ?? '';
            elseif ($product['product'] == Product::products['kasko-by-subscription'])
                $_product[] = array_flip(KaskoBySubscriptionPolicy::STATUS)[$product['status']] ?? '';
            elseif ($product['product'] == Product::products['accident'])
                $_product[] = array_flip(Accident::STATUS)[$product['status']] ?? '';

            $_product[] = $product['reason'];
            $_product[] = $product['comment'];

            $_products[] = $_product;
        }

        $writer = new XLSXWriter();
        $writer->writeSheet($_products,'products',
            [
                'product' => 'string',
                'policy_generated_date' => 'string',
                'f_user_phone' => 'string',
                'status' => 'string',
                'reason' => 'string',
                'comment' => 'string',
            ]);
        $model_name = "products";

        return GeneralHelper::writeToString($writer, $model_name);
    }

    /**
     * @OA\Put(
     *     path="/sales-for-call-center/update",
     *     summary="update product reason or comment",
     *     tags={"SalesForCallCenterController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "name"},
     *                 @OA\Property (property="product", type="integer", example=1),
     *                 @OA\Property (property="product_id", type="integer", example="1"),
     *                 @OA\Property (property="reason_id", type="integer", example="1", description="reason/all api sidan page ga hech nima yubormasdan status=1 yuborib olinadi"),
     *                 @OA\Property (property="comment", type="string", example="taksis ekan"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated reason",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/product_for_call_center")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateProductForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/sales-for-call-center/call",
     *     summary="make call",
     *     tags={"SalesForCallCenterController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                required={"f_user_id"},
     *                 @OA\Property (property="f_user_id"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="make call successfully",
     *          @OA\JsonContent( type="boolean", example=true)
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCall()
    {
        GeneralHelper::checkPermission();

        $model = new CallForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

}