<?php
namespace backapi\controllers;

use backapi\models\searchs\OsagoSearch;
use backapi\models\searchs\SaleProductsSearch;
use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\Accident;
use common\models\Kasko;
use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\models\Osago;
use common\models\Product;
use common\models\Travel;
use XLSXWriter;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;

class SaleController extends BaseController
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
     *     path="/sale/statistics",
     *     summary="Method to get all statistic info for sales page",
     *     tags={"SaleController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Response(
     *         response="200", description="all statistic info",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="dayly_product_count", type="integer", example=540),
     *              @OA\Property(property="dayly_amount_sum", type="integer", example=142),
     *              @OA\Property(property="average_check", type="integer", example=12),
     *              @OA\Property(property="given_polices", type="integer", example=12),
     *              @OA\Property(property="dayly_in_progress", type="integer", example=12),
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStatistics()
    {
        GeneralHelper::checkPermission();

        //dayly_product_count
        $beginning_of_day = date_create_from_format('Y-m-d', date('Y-m-d'))->setTime(0, 0, 0)->getTimestamp();
        $dayly_casco_condition = [
            'and',
            ['in', 'status', [Kasko::STATUS['payed'], Kasko::STATUS['attached'], Kasko::STATUS['processed'], Kasko::STATUS['policy_generated']]],
            ['between', 'payed_date', $beginning_of_day, time()]
        ];
        $dayly_travel_condition = [
            'and',
            ['in', 'status', [Travel::STATUSES['payed'], Travel::STATUSES['waiting_for_policy'], Travel::STATUSES['received_policy']]],
            ['between', 'payed_date', $beginning_of_day, time()]
        ];
        $dayly_osago_condition = [
            'and',
            ['in', 'status', [Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']]],
            ['between', 'created_at', $beginning_of_day, time()]
        ];
        $dayly_accident_condition = [
            'and',
            ['in', 'status', [Accident::STATUS['payed'], Accident::STATUS['waiting_for_policy'], Accident::STATUS['received_policy']]],
            ['between', 'created_at', $beginning_of_day, time()]
        ];
        $dayly_kbsp_condition = [
            'and',
            ['in', 'status', [KaskoBySubscriptionPolicy::STATUS['payed'], KaskoBySubscriptionPolicy::STATUS['waiting_for_policy'], KaskoBySubscriptionPolicy::STATUS['received_policy']]],
            ['between', 'created_at', date('Y-m-d H:i:s', $beginning_of_day), date('Y-m-d H:i:s')]
        ];

        $dayly_casco = Kasko::find()->where($dayly_casco_condition)->count();
        $dayly_travel = Travel::find()->where($dayly_travel_condition)->count();
        $dayly_osago = Osago::find()->where($dayly_osago_condition)->count();
        $dayly_accident = Accident::find()->where($dayly_accident_condition)->count();
        $dayly_kbsp = KaskoBySubscriptionPolicy::find()->where($dayly_kbsp_condition)->count();
        $dayly_product_count = $dayly_casco + $dayly_travel + $dayly_osago + $dayly_accident + $dayly_kbsp;
        //dayly_product_count

        //dayly_amount_sum
        $dayly_casco_sum = Kasko::find()->where($dayly_casco_condition)->sum('amount_uzs');
        $dayly_travel_sum = Travel::find()->where($dayly_travel_condition)->sum('amount_uzs');
        $dayly_osago_sum = Osago::find()->where($dayly_osago_condition)->sum('amount_uzs');
        $dayly_accident_sum = Accident::find()->where($dayly_accident_condition)->sum('amount_uzs');
        $dayly_kbsp_sum = KaskoBySubscriptionPolicy::find()->where($dayly_kbsp_condition)->sum('amount_uzs');
        $dayly_amount_sum = $dayly_casco_sum + $dayly_travel_sum + $dayly_osago_sum + $dayly_accident_sum + $dayly_kbsp_sum;
        //dayly_amount_sum

        //average_check
        $average_check = 0;
        if ($dayly_product_count != 0)
            $average_check = round($dayly_amount_sum / $dayly_product_count, -3);
        //average_check

        //given_polices
        $dayly_given_policies_casco = Kasko::find()->where($dayly_casco_condition)->andWhere(['in', 'status', [Kasko::STATUS['processed'], Kasko::STATUS['policy_generated']]])->count();
        $dayly_given_policies_osago = Osago::find()->where($dayly_osago_condition)->andWhere(['in', 'status', [Osago::STATUS['received_policy']]])->count();
        $dayly_given_policies_travel = Travel::find()->where($dayly_travel_condition)->andWhere(['in', 'status', [Travel::STATUSES['received_policy']]])->count();
        $dayly_given_policies_accident = Accident::find()->where($dayly_accident_condition)->andWhere(['in', 'status', [Accident::STATUS['received_policy']]])->count();
        $dayly_given_policies_kbsp = KaskoBySubscriptionPolicy::find()->where($dayly_kbsp_condition)->andWhere(['in', 'status', [KaskoBySubscriptionPolicy::STATUS['received_policy']]])->count();
        $given_polices = $dayly_given_policies_travel + $dayly_given_policies_osago + $dayly_given_policies_casco + $dayly_given_policies_accident + $dayly_given_policies_kbsp;
        //given_polices

        //dayly_in_progress
        $osago_in_progress_count = Osago::find()->where([
            'and',
            ['in', 'status', [
                Osago::STATUS['step1'],
                Osago::STATUS['step2'],
                Osago::STATUS['step3'],
                Osago::STATUS['step4'],
            ]],
            ['between', 'created_at', $beginning_of_day, time()]
        ])->count();
        $casco_in_progress_count = Kasko::find()->where([
            'and',
            ['in', 'status', [
                Kasko::STATUS['step1'],
                Kasko::STATUS['step2'],
                Kasko::STATUS['step3'],
                Kasko::STATUS['step4'],
            ]],
            ['between', 'created_at', $beginning_of_day, time()]
        ])->count();
        $travel_in_progress_count = Travel::find()->where([
            'and',
            ['in', 'status', [
                Travel::STATUSES['step1'],
                Travel::STATUSES['step2'],
                Travel::STATUSES['step3'],
            ]],
            ['between', 'created_at', $beginning_of_day, time()]
        ])->count();
        $accident_in_progress_count = Osago::find()->where([
            'and',
            ['in', 'status', [
                Osago::STATUS['step1'],
                Osago::STATUS['step2'],
                Osago::STATUS['step3'],
                Osago::STATUS['step4'],
            ]],
            ['between', 'created_at', $beginning_of_day, time()],
            ['not', ['accident_amount' => null]]
        ])->count();
        $kbsp_in_progress_count = KaskoBySubscription::find()->where([
            'and',
            ['in', 'status', [
                KaskoBySubscription::STATUS['step1'],
                KaskoBySubscription::STATUS['step2'],
                KaskoBySubscription::STATUS['step3'],
                KaskoBySubscription::STATUS['step4'],
                KaskoBySubscription::STATUS['step5'],
                KaskoBySubscription::STATUS['step6'],
            ]],
            ['between', 'created_at', date('Y-m-d H:i:s', $beginning_of_day), date('Y-m-d H:i:s')]
        ])->count();
        $dayly_in_progress = $casco_in_progress_count + $travel_in_progress_count + $osago_in_progress_count + $accident_in_progress_count + $kbsp_in_progress_count;
        //dayly_in_progress

        return [
            'dayly_product_count' => $dayly_product_count,
            'dayly_amount_sum' => $dayly_amount_sum,
            'average_check' => $average_check,
            'given_polices' => $given_polices,
            'dayly_in_progress' => $dayly_in_progress,
        ];
    }

    /**
     * @OA\Get(
     *     path="/sale/products",
     *     summary="Method to get all products for sales page",
     *     tags={"SaleController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[product_id]", in="query", @OA\Schema (type="integer"), description="osago => 1, kasko => 2, travel => 3"),
     *     @OA\Parameter (name="filter[region]", in="query", @OA\Schema (type="string"), description="01 => toshkent, 80 => buxoro, va hokazo"),
     *     @OA\Parameter (name="filter[policy_generated_date][gte]", in="query", @OA\Schema (type="integer"), description="policy_generated_date yuborilgandan katta yoki tenglari"),
     *     @OA\Parameter (name="filter[policy_generated_date][lte]", in="query", @OA\Schema (type="integer"), description="policy_generated_date yuborilgandan kichik yoki tenglari"),
     *     @OA\Parameter (name="filter[partner_id]", in="query", @OA\Schema (type="integer")),
     *     @OA\Parameter (name="filter[payment_type]", in="query", @OA\Schema (type="string"), description="click, payme, payze"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="1, 2, 3 ... "),
     *     @OA\Parameter (name="filter[agent_id]", in="query", @OA\Schema (type="integer"), description="get by id of agent"),
     *     @OA\Parameter (name="filter[f_user_id]", in="query", @OA\Schema (type="integer"), description="get by id of user which is create order"),
     *     @OA\Parameter (name="filter[promo_id]", in="query", @OA\Schema (type="integer"), description="get by id of promocode"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: policy_generated_date, amount_uzs. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="products with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#components/schemas/product")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionProducts()
    {
        GeneralHelper::checkPermission();

        $searchModel = new SaleProductsSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => $dataProvider->getModels(),
            'pages' => $dataProvider->getPagination(),
        ];
    }

    /**
     * @OA\Get(
     *     path="/sale/export",
     *     summary="Method to get excel with all products in sales page",
     *     tags={"SaleController"},
     *     security={ {"bearerAuth": {} } },
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
            $_product[] = (string) $product['product_id'];
            $_product[] = empty($product['policy_generated_date']) ? '' : date('Y-m-d', $product['policy_generated_date']);
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

            $_product[] = array_flip(Product::products)[$product['product']] ?? '';
            $_product[] = (string) $product['policy_number'];
            $_product[] = (string) $product['amount_uzs'];
            $_product[] = (string) $product['payment_type'];
            $_product[] = (string) $product['partner_name'];
            $_product[] = (string) $product['f_user_name'];
            $_product[] = (string) $product['f_user_phone'];
            $_product[] = (string) $product['insurer_name'];
            $_product[] = (string) $product['region'];

            $_products[] = $_product;
        }

        $writer = new XLSXWriter();
        $writer->writeSheet($_products,'products',
            [
                'product_id' => 'integer',
                'policy_generated_date' => 'string',
                'status' => 'string',
                'product' => 'string',
                'policy_number' => 'string',
                'amount_uzs' => 'integer',
                'payment_type' => 'string',
                'partner_name' => 'string',
                'f_user_name' => 'string',
                'f_user_phone' => 'string',
                'insurer_name' => 'string',
                'region' => 'string'
            ]);
        $model_name = "products";

        return GeneralHelper::writeToString($writer, $model_name);
    }

}