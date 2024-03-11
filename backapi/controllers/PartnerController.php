<?php

namespace backapi\controllers;

use backapi\models\forms\partnerForms\CreatePartnerForm;
use backapi\models\forms\partnerForms\UpdatePartnerForm;
use backapi\models\searchs\KaskoBySubscriptionSearch;
use backapi\models\searchs\KaskoSearch;
use backapi\models\searchs\OsagoSearch;
use backapi\models\searchs\TravelSearch;
use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\Kasko;
use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\models\Osago;
use common\models\Partner;
use common\models\Product;
use common\models\Travel;
use XLSXWriter;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class PartnerController extends BaseController
{
    public $row_respons_action_ids = ["kaskos-export", "osago-export", "kasko-by-subscription-export", "travel-export"];
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['POST'],
                'update' => ['POST'],
            ],
        ];


        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/partner/all",
     *     summary="Method to get all partners for table in product page as companies",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Response(
     *         response="200", description="partners",
     *         @OA\JsonContent(type="array", @OA\Items(type="object",
     *              @OA\Property(property="id", type="integer", example=9),
     *              @OA\Property(property="name", type="string", example="APEX Insurance"),
     *              @OA\Property(property="image", type="string", example="http://127.0.0.1:20080/uploads/partners/partner1601399768.png"),
     *              )
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $partners =  Partner::find()->where(['status' => 1])->all();
        return ArrayHelper::toArray($partners, [
            Partner::className() => [
                'id',
                'name',
                'image' => function ($partner) {
                    return GeneralHelper::env('frontend_project_website') . '/uploads/partners/' . $partner->image;
                }
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/partner/by-id",
     *     summary="Method to get one partners by id, call when user click to company",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="id", ref="#components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="partner",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="id", type="integer", example=9),
     *              @OA\Property(property="name", type="string", example="APEX Insurance"),
     *              @OA\Property(property="image", type="string", example="http://127.0.0.1:20080/uploads/partners/partner1601399768.png"),
     *              @OA\Property(property="status", type="integer", example=1),
     *              @OA\Property(property="created_at", type="integer", example=1598589214),
     *              @OA\Property(property="updated_at", type="integer", example=1598589214),
     *              @OA\Property(property="travel_currency_id", type="integer", example=1),
     *              @OA\Property(property="contract_number", type="string|null", example=null),
     *              @OA\Property(property="monthly_sales_amount", type="string", example="672000"),
     *              @OA\Property(property="monthly_sales_count", type="integer", example=4),
     *              @OA\Property(property="average_sales_amount", type="integer", example=168000),
     *              @OA\Property(property="commission", type="integer", example=0),
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionById(int $id)
    {
        GeneralHelper::checkPermission();

        if (!$partner = Partner::findOne($id))
            throw new NotFoundHttpException(Yii::t('app', 'Partner not found'));

        $beginning_of_month = date_create_from_format('Y-m-d', date('Y-m-01'))->setTime(0, 0, 0)->getTimestamp();
        $monthly_sales_amount = Product::products()->where(['between', 'policy_generated_date', $beginning_of_month, time()])->andWhere(['partner_id' => $id])->sum('amount_uzs');
        $monthly_sales_count = Product::products()->where(['between', 'policy_generated_date', $beginning_of_month, time()])->andWhere(['partner_id' => $id])->count();
        return array_merge($partner->getShortArr(), [
            'monthly_sales_amount' => $monthly_sales_amount,
            'monthly_sales_count' => $monthly_sales_count,
            'average_sales_amount' => ($monthly_sales_count == 0) ? 0 : $monthly_sales_amount / $monthly_sales_count,
            'commission' => 0,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/partner/top-products",
     *     summary="Method to get top products in one product page(Самые продаваемые продукты)",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="id", ref="#components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="partner",
     *         @OA\JsonContent(type="array", @OA\Items(type="object",
     *              @OA\Property(property="product", type="integer", example=2),
     *              @OA\Property(property="count", type="integer", example=387),
     *              @OA\Property(property="amount_uzs", type="string", example="767507600"),
     *         ))
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionTopProducts($id)
    {
        GeneralHelper::checkPermission();

        if (!$partner = Partner::findOne($id))
            throw new NotFoundHttpException(Yii::t('app', 'Partner not found'));

        return Product::products()
            ->select(["product", "count(product_id) as count",  "sum(amount_uzs) as amount_uzs"])
            ->groupBy('product')
            ->orderBy('amount_uzs desc')
            ->createCommand()->queryAll();
    }

    /**
     * @OA\Get(
     *     path="/partner/products",
     *     summary="Method to get all or one partner products(Продукты)",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="id", ref="#components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="partner",
     *         @OA\JsonContent(type="array", @OA\Items(type="object",
     *              @OA\Property(property="product", type="integer", example=2),
     *              @OA\Property(property="count", type="integer", example=387),
     *              @OA\Property(property="amount_uzs", type="string", example="767507600"),
     *         ))
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionProducts()
    {
        GeneralHelper::checkPermission();

        if (
            array_key_exists('partner_id', $this->get)
            and is_numeric($this->get['partner_id'])
            and $partner = Partner::findOne($this->get['partner_id'])
        )
            return Product::getShortArrCollection($partner->products);

        return Product::getShortArrCollection(Product::find()->all());
    }

    /**
     * @OA\Post(
     *     path="/partner/create",
     *     summary="create new Partner",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "image"},
     *                 @OA\Property (property="name", type="string", example="Qanaqadir insurance"),
     *                 @OA\Property (property="contract_number", type="string", example="as0390dd422"),
     *                 @OA\Property (property="phone", type="string", example="gross_insurance"),
     *                 @OA\Property (property="service_amount", type="integer", example="200000", description="phone yozilsa keyin service_amount inputi ochilsin"),
     *                 @OA\Property (property="hook_url", type="string", example="https://wert.oi", description="akt osmotr tayyor bo'lgandan keyin shu urlga ping qilamiz"),
     *                 @OA\Property (property="password", type="string", example="gross_secret", description="phone yozilsa keyin password inputi ochilsin"),
     *                 @OA\Property (property="password_repeat", type="string", example="gross_secret", description="phone yozilsa keyin password_repeat inputi ochilsin"),
     *                 @OA\Property (property="image", type="file"),
     *                 @OA\Property (property="travel_offer_file", type="file"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created or updated osago",
     *          @OA\JsonContent( type="object", ref="#components/schemas/partner")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreatePartnerForm();
        $model->setAttributes($this->post);
        $model->image = UploadedFile::getInstanceByName('image');
        $model->travel_offer_file = UploadedFile::getInstanceByName('travel_offer_file');
        if ($model->validate())
            return $model->save()->getShortArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/partner/update",
     *     summary="update Partner",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"id", "name", "image", "status"},
     *                 @OA\Property (property="id", type="integer", example="124"),
     *                 @OA\Property (property="name", type="string", example="Qanaqadir insurance"),
     *                 @OA\Property (property="contract_number", type="string", example="as0390dd422"),
     *                 @OA\Property (property="phone", type="string", example="gross_insurance"),
     *                 @OA\Property (property="service_amount", type="integer", example="200000", description="phone yozilsa keyin service_amount inputi ochilsin"),
     *                 @OA\Property (property="hook_url", type="string", example="https://wert.oi", description="akt osmotr tayyor bo'lgandan keyin shu urlga ping qilamiz"),
     *                 @OA\Property (property="password", type="string", example="gross_secret", description="phone yozilgan bo'lsa keyin password inputi ochilsin"),
     *                 @OA\Property (property="password_repeat", type="string", example="gross_secret", description="phone yozilgan bo'lsa keyin password_repeat inputi ochilsin"),
     *                 @OA\Property (property="image", type="file"),
     *                 @OA\Property (property="travel_offer_file", type="file"),
     *                 @OA\Property (property="status", type="integer", description="0 yoki 1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created or updated osago",
     *          @OA\JsonContent( type="object", ref="#components/schemas/partner")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdatePartnerForm();
        $model->setAttributes($this->post);
        $model->image = UploadedFile::getInstanceByName('image');
        $model->travel_offer_file = UploadedFile::getInstanceByName('travel_offer_file');
        if ($model->validate())
            return $model->save()->getShortArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/partner/kaskos",
     *     summary="Method to get all kaskos with pagination ",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[id]", in="query", @OA\Schema (type="integer")),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="step1 => 1, step2 => 2, step3 => 3, step4 => 4, payed => 5, attached => 6, processed => 7, policy_generated => 8"),
     *     @OA\Parameter (name="filter[partner_id]", in="query", @OA\Schema (type="string"), description="get from partner/all"),
     *     @OA\Parameter (name="filter[processed_date_from]", in="query", @OA\Schema (type="string"), description="send in seconds. for example you should send 1653458665. It means Wednesday, 25 May 2022 г., 6:04:25"),
     *     @OA\Parameter (name="filter[policy_number]", in="query", @OA\Schema (type="string"), description="search from policy_number"),
     *     @OA\Parameter (name="filter[processed_date_to]", in="query", @OA\Schema (type="integer"), description="send in seconds. for example you should send 1653458665. It means Wednesday, 25 May 2022 г., 6:04:25"),
     *     @OA\Parameter (name="filter[region_name]", in="query", @OA\Schema (type="string"), description="search from region.name_ru"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: payed_date, year, amount_uzs, insurer_name, 'id', 'partner.name', 'status'. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="products with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="autonumber", type="string", example="50A345TR"),
     *                  @OA\Property(property="payed_date", type="string|null", example="27.09.2022"),
     *                  @OA\Property(property="year", type="integer", example=2022),
     *                  @OA\Property(property="amount_uzs", type="integer|null", example=360000),
     *                  @OA\Property(property="partner_id", type="integer", example=1),
     *                  @OA\Property(property="partner_name", type="string", example="Gross Insurance"),
     *                  @OA\Property(property="insurer_name", type="string|null", example="BEKMULLIN TIGRAN TIMUROVICH"),
     *                  @OA\Property(property="status", type="integer", example=2),
     *                  @OA\Property(property="payment_type", type="string", example="zoodpay", description="zoodpay, click, payme, payze, hamkorpay"),
     *                  @OA\Property(property="created_at", type="string", example="d.m.Y H:i:s", description="Zayavka yaratilish boshlangan vaqt"),
     *                  @OA\Property(property="model", type="string", example="Captiva", description="mashina markasi"),
     *                  @OA\Property(property="insurer_phone", type="string", example="94 646 4400", description="telefon raqam"),
     *              )),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionKaskos()
    {
        GeneralHelper::checkPermission();

        $searchModel = new KaskoSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);
        $models = $dataProvider->getModels();

        $models = array_map(function ($model) {
            /** @var Kasko $model */
            return [
                'id' => $model->id,
                'autonumber' => $model->autonumber,
                'payed_date' => is_null($model->payed_date) ? null :  date('d.m.Y H:i:s', $model->payed_date),
                'created_at' => date('d.m.Y H:i:s', $model->created_at),
                'model' => $model->autocomp->automodel->name ?? "",
                'year' => $model->year,
                'amount_uzs' => $model->amount_uzs,
                'partner_id' => $model->partner_id,
                'partner_name' => $model->partner->name ?? null,
                'insurer_name' => $model->insurer_name,
                'status' => $model->status,
                'payment_type' => empty($model->trans) ? null : $model->trans->payment_type,
                'insurer_phone' => $model->fUser->phone ?? null,
            ];
        }, $models);

        return [
            'models' => $models,
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/partner/kaskos-export",
     *     summary="Method to get all kaskos excel",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[id]", in="query", @OA\Schema (type="integer")),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="step1 => 1, step2 => 2, step3 => 3, step4 => 4, payed => 5, attached => 6, processed => 7, policy_generated => 8"),
     *     @OA\Parameter (name="filter[partner_id]", in="query", @OA\Schema (type="string"), description="get from partner/all"),
     *     @OA\Parameter (name="filter[processed_date_from]", in="query", @OA\Schema (type="string"), description="send in seconds. for example you should send 1653458665. It means Wednesday, 25 May 2022 г., 6:04:25"),
     *     @OA\Parameter (name="filter[policy_number]", in="query", @OA\Schema (type="string"), description="search from policy_number"),
     *     @OA\Parameter (name="filter[processed_date_to]", in="query", @OA\Schema (type="integer"), description="send in seconds. for example you should send 1653458665. It means Wednesday, 25 May 2022 г., 6:04:25"),
     *     @OA\Parameter (name="filter[region_name]", in="query", @OA\Schema (type="string"), description="search from region.name_ru"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: payed_date, year, amount_uzs, insurer_name. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="excel string",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionKaskosExport()
    {
        GeneralHelper::checkPermission();

        $filter = \Yii::$app->request->get()['filter'] ?? [];
        if (!array_key_exists('from_date', $filter) and !array_key_exists('till_date', $filter)) {
            $filter['till_date'] = date('Y-m-d');
            $filter['from_date'] = date('Y-m-d', strtotime("-1 month"));
        }

        if (!array_key_exists('from_date', $filter) and array_key_exists('till_date', $filter))
            throw new BadRequestHttpException("from_date is required");
        if (array_key_exists('from_date', $filter) and !array_key_exists('till_date', $filter))
            throw new BadRequestHttpException("till_date is required");

        $ts1 = strtotime($filter['till_date']);
        $ts2 = strtotime($filter['from_date']);
        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);
        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);
        $diff = abs((($year2 - $year1) * 12) + ($month2 - $month1));
        if ($diff > 1)
            throw new BadRequestHttpException("Интервал времени не должен превышать одного месяца");


        return GeneralHelper::export(Kasko::class, KaskoSearch::class, [
            'id' => 'integer',
            'created_at' => 'string',
            'year' => 'integer',
            'amount_uzs' => 'string',
            'partner' => 'string',
            'insurer_name' => 'string',
            'status' => 'string',
        ], [
            'id',
            'created_at' => function ($model) {
                return is_null($model->created_at) ? null :  date('d.m.Y H:i:s', $model->created_at);
            },
            'year',
            'amount_uzs',
            'partner' => function ($model) {
                return $model->partner->name;
            },
            'insurer_name',
            'status' => function ($model) {
                return is_null($model->status) ? null :  array_flip(Kasko::STATUS)[$model->status];
            },
        ], $filter);
    }

    /**
     * @OA\Get(
     *     path="/partner/osago",
     *     summary="Method to get all osagos with pagination ",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from id, phone_number, policy_number"),
     *     @OA\Parameter (name="filter[from_date]", in="query", @OA\Schema (type="string"), example="2022-12-30", description="payed date bo'yicha"),
     *     @OA\Parameter (name="filter[till_date]", in="query", @OA\Schema (type="string"), example="2022-12-30", description="payed date bo'yicha"),
     *     @OA\Parameter (name="filter[status][]", in="query", @OA\Schema (type="array", @OA\Items(type="integer")), description="step1 => 1, step2 => 2, step3 => 3, step4 => 4, payed => 6, waiting_for_policy => 7, received_policy => 8, 'canceled' => 9,"),
     *     @OA\Parameter (name="filter[payment_type][]", in="query", @OA\Schema (type="array", @OA\Items(type="string")), description="send click, payme, payze"),
     *     @OA\Parameter (name="filter[partner_id][]", in="query",  @OA\Schema (type="array", @OA\Items(type="integer")), description="send partner_id to filter for partner. Get partners list from partner/all to using select options"),
     *     @OA\Parameter (name="filter[autonumber]", in="query", @OA\Schema (type="string")),
     *     @OA\Parameter (name="filter[with_discount]", in="query", @OA\Schema (type="integer"), description="0 yoki 1"),
     *     @OA\Parameter (name="filter[is_juridic]", in="query", @OA\Schema (type="integer"), description="0 yoki 1"),
     *     @OA\Parameter (name="filter[bridge_company_id]", in="query", @OA\Schema (type="integer"), description="bridge company idsini yuborish kerak"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, payed_date, amount_uzs, status, user.phone, trans.payment_type, policy_number, partner.name, autonumber, created_at . use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="products with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="payed_date", type="string|null", example="27.09.2022"),
     *                  @OA\Property(property="amount_uzs", type="integer|null", example=168000),
     *                  @OA\Property(property="accident_amount", type="integer|null", example=21400),
     *                  @OA\Property(property="status", type="integer", example=2),
     *                  @OA\Property(property="phone_number", type="string", example="998946464400"),
     *                  @OA\Property(property="payment_type", type="string|null", example="click"),
     *                  @OA\Property(property="insurer_name", type="string|null", example=null),
     *                  @OA\Property(property="policy_number", type="string|null", example=null),
     *                  @OA\Property(property="policy_pdf_url", type="string|null", example=null),
     *                  @OA\Property(property="autonumber", type="string|null", example="30a288da"),
     *                  @OA\Property(property="partner", ref="#components/schemas/id_name"),
     *                  @OA\Property(property="created_at",  type="string|null", example="27.09.2022"),
     *                  @OA\Property(property="created_in_telegram",  type="boolean", example="1"),
     *                  @OA\Property(property="promo", type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="promo_code", type="string|null", example="salom"),
     *                      @OA\Property(property="promo_percent", type="integer|null", example=-20),
     *                      @OA\Property(property="promo_amount", type="integer|null", example=-30000),
     *                  ),
     *                  @OA\Property(property="used_unique_code", type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="code", type="string|null", example="salom"),
     *                      @OA\Property(property="discount_percent", type="integer|null", example=-5),
     *                  ),
     *                  @OA\Property(property="bridge_company", type="object",
     *                       @OA\Property(property="id", type="integer", example=1),
     *                       @OA\Property(property="name", type="string|null", example="road24"),
     *                   ),
     *              )),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionOsago()
    {
        GeneralHelper::checkPermission();

        $searchModel = new OsagoSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);
        $models = $dataProvider->getModels();

        $models = array_map(function ($model) {
            return [
                'id' => $model->id,
                'payed_date' => is_null($model->payed_date) ? null :  date('d.m.Y H:i:s', $model->payed_date),
                'amount_uzs' => $model->amount_uzs,
                'accident_amount' => $model->accident_amount,
                'status' => $model->status,
                'phone_number' => $model->user->phone ?? null,
                'payment_type' => $model->trans->payment_type ?? null,
                'policy_number' => $model->policy_number,
                'policy_pdf_url' => $model->policy_pdf_url,
                'autonumber' => $model->autonumber,
                'partner' => !is_null($model->partner) ? $model->partner->getForIdNameArr() : null,
                'created_at' => is_null($model->created_at) ? null :  date('d.m.Y H:i:s', $model->created_at),
                'created_in_telegram' => $model->created_in_telegram,
                "promo" => [
                    "id" => $model->promo_id,
                    "promo_code" => is_null($model->promo) ? null : $model->promo->code,
                    "promo_percent" => $model->promo_percent,
                    "promo_amount" => $model->promo_amount,
                ],
                "used_unique_code" => !is_null($model->usedUniqueCode) ? $model->usedUniqueCode->getShortArr() : null,
                "bridge_company" => !is_null($model->bridgeCompany) ? $model->bridgeCompany->getIdNameArr() : null,
            ];
        }, $models);

        return [
            'models' => $models,
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/partner/osago-export",
     *     summary="Method to get excel which contain all osagos",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from id, phone_number, policy_number"),
     *     @OA\Parameter (name="filter[status][]", in="query", @OA\Schema (type="array", @OA\Items(type="integer")), description="step1 => 1, step2 => 2, step3 => 3, step4 => 4, payed => 6, waiting_for_policy => 7, received_policy => 8, 'canceled' => 9,"),
     *     @OA\Parameter (name="filter[payment_type][]", in="query", @OA\Schema (type="array", @OA\Items(type="string")), description="send click, payme, payze"),
     *     @OA\Parameter (name="filter[from_date]", in="query", @OA\Schema (type="string"), example="2022-12-30", description="payed date bo'yicha"),
     *     @OA\Parameter (name="filter[till_date]", in="query", @OA\Schema (type="string"), example="2022-12-30", description="payed date bo'yicha"),
     *     @OA\Parameter (name="filter[with_discount]", in="query", @OA\Schema (type="integer"), description="0 yoki 1"),
     *     @OA\Parameter (name="filter[is_juridic]", in="query", @OA\Schema (type="integer"), description="0 yoki 1"),
     *     @OA\Parameter (name="filter[partner_id][]", in="query",  @OA\Schema (type="array", @OA\Items(type="integer")), description="send partner_id to filter for partner. Get partners list from partner/all to using select options"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, payed_date, amount_uzs, status, user.phone, trans.payment_type, policy_number . use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="excel string",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionOsagoExport()
    {
        $filter = \Yii::$app->request->get()['filter'] ?? [];
        if (!array_key_exists('from_date', $filter) and !array_key_exists('till_date', $filter)) {
            $filter['till_date'] = date('Y-m-d');
            $filter['from_date'] = date('Y-m-d', strtotime("-1 month"));
        }

        if (!array_key_exists('from_date', $filter) and array_key_exists('till_date', $filter))
            throw new BadRequestHttpException("from_date is required");
        if (array_key_exists('from_date', $filter) and !array_key_exists('till_date', $filter))
            throw new BadRequestHttpException("till_date is required");

        $ts1 = strtotime($filter['till_date']);
        $ts2 = strtotime($filter['from_date']);
        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);
        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);
        $diff = abs((($year2 - $year1) * 12) + ($month2 - $month1));
        if ($diff > 1)
            throw new BadRequestHttpException("Интервал времени не должен превышать одного месяца");

        $use_territories = [
            1 => 'ГОРОД ТАШКЕНТ, РУз',
            2 => 'ТАШКЕНТСКАЯ ОБЛАСТЬ, РУз',
            3 => 'АНДИЖАНСКАЯ ОБЛАСТЬ, РУз',
            4 => 'БУХАРСКАЯ  ОБЛАСТЬ, РУз',
            5 => 'ДЖИЗАКСКАЯ ОБЛАСТЬ, РУз',
            6 => 'КАШКАДАРЬИНСКАЯ ОБЛАСТЬ, РУз',
            7 => 'РЕСПУБЛИКА КАРАКАЛПАКСТАН, РУз',
            8 => 'НАВОИСКАЯ ОБЛАСТЬ, РУз',
            9 => 'НАМАНГАНСКАЯ ОБЛАСТЬ, РУз',
            10 => 'САМАРКАНДСКАЯ ОБЛАСТЬ, РУз',
            11 => 'СЫРЬДАРЬИНСКАЯ ОБЛАСТЬ, РУ',
            12 => 'СУРХАНДАРЬИНСКАЯ ОБЛАСТЬ, РУз',
            13 => 'ФЕРГАНСКАЯ ОБЛАСТЬ, РУз',
            14 => 'ХОРЕЗМСКАЯ ОБЛАСТЬ, РУз',
        ];

        $oblasts = [
            10 => 'ГОРОД ТАШКЕНТ',
            11 => 'ТАШКЕНТСКАЯ ОБЛАСТЬ',
            17 => 'АНДИЖАНСКАЯ ОБЛАСТЬ',
            20 => 'БУХАРСКАЯ  ОБЛАСТЬ',
            13 => 'ДЖИЗАКСКАЯ ОБЛАСТЬ',
            18 => 'КАШКАДАРЬИНСКАЯ ОБЛАСТЬ',
            23 => 'РЕСПУБЛИКА КАРАКАЛПАКСТАН',
            21 => 'НАВОИСКАЯ ОБЛАСТЬ',
            16 => 'НАМАНГАНСКАЯ ОБЛАСТЬ',
            14 => 'САМАРКАНДСКАЯ ОБЛАСТЬ',
            12 => 'СЫРЬДАРЬИНСКАЯ ОБЛАСТЬ',
            19 => 'СУРХАНДАРЬИНСКАЯ ОБЛАСТЬ',
            15 => 'ФЕРГАНСКАЯ ОБЛАСТЬ',
            22 => 'ХОРЕЗМСКАЯ ОБЛАСТЬ',
        ];

        return GeneralHelper::export(Osago::class, OsagoSearch::class, [
            'id' => 'integer',
            'payed_date' => 'string',
            'begin_date' => 'string',
            'amount_uzs' => 'integer',
            'amount_uzs_without_discount' => 'integer',
            'status' => 'string',
            'number_drivers' => 'string',
            'phone_number' => 'string',
            'payment_type' => 'string',
            'policy_number' => 'string',
            // policy_pdf_url' => 'string',
            'autonumber' => "string",
            'partner' => "string",
            'created_at' => "string",
            'accident_policy_number' => "string",
            'accident_amount' => "string",
            'pinfl' => "string",
            'created_in_telegram' => "string",
            'model_name' => "string",
            'tech_passport_issue_date' => "string",
            'issue_year' => "string",
            'body_number' => "string",
            'engine_number' => "string",
            'use_territory' => "string",
            'last_name_latin' => "string",
            'first_name_latin' => "string",
            'middle_name_latin' => "string",
            'oblast' => "string",
            'orgname' => "string",
            'insurer_birthday' => "string",
            'insurer_license_series' => "string",
            'insurer_license_number' => "string",
            'insurer_license_given_date' => "string",
            'bridge_company' => "string",
        ], [
            'id',
            'payed_date' => function ($model) {
                return is_null($model->payed_date) ? null :  date('d.m.Y H:i:s', $model->payed_date);
            },
            'begin_date' => function ($model) {
                return is_null($model->begin_date) ? null :  DateHelper::date_format($model->begin_date, 'Y-m-d', 'd.m.Y');
            },
            'amount_uzs',
            'amount_uzs_without_discount' =>  function ($model) {
                return $model->getAmountUzsWithoutDiscount();
            },
            //            'status',
            'status' =>  function ($model) {
                return in_array($model->status, [Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']]) ? "payed" : "no payed";
            },
            'number_drivers_id' => function ($model) {
                return $model->numberDrivers->name_ru ?? '';
            },
            'phone_number' =>  function ($model) {
                return $model->user->phone ?? '';
            },
            'payment_type' =>  function ($model) {
                return $model->trans->payment_type ?? '';
            },
            'policy_number',
            //            'policy_pdf_url',
            'autonumber',
            'partner' => function ($model) {
                return !is_null($model->partner) ? $model->partner->name : "";
            },
            'created_at' => function ($model) {
                return is_null($model->created_at) ? null :  date('d.m.Y', $model->created_at);
            },
            'accident_policy_number' => function ($model) {
                return $model->accident->policy_number ?? '';
            },
            'accident_amount',
            'pinfl' => function ($model) {
                return $model->insurer_pinfl;
            },
            'created_in_telegram',
            'model_name' => function ($model) {
                return $model->osagoFondData->model_name ?? "";
            },
            'tech_passport_issue_date' => function ($model) {
                return $model->osagoFondData->tech_passport_issue_date ?? "";
            },
            'issue_year' => function ($model) {
                return $model->osagoFondData->issue_year ?? "";
            },
            'body_number' => function ($model) {
                return $model->osagoFondData->body_number ?? "";
            },
            'engine_number' => function ($model) {
                return $model->osagoFondData->engine_number ?? "";
            },
            'use_territory' => function ($model) use ($use_territories) {
                return $use_territories[$model->osagoFondData->use_territory ?? ""] ?? "";
            },
            'last_name_latin' => function ($model) {
                return $model->osagoFondData->last_name_latin ?? "";
            },
            'first_name_latin' => function ($model) {
                return $model->osagoFondData->first_name_latin ?? "";
            },
            'middle_name_latin' => function ($model) {
                return $model->osagoFondData->middle_name_latin ?? "";
            },
            'oblast' => function ($model) use ($oblasts) {
                return $oblasts[$model->osagoFondData->oblast ?? ""] ?? "";
            },
            'orgname' => function ($model) {
                return $model->osagoFondData->orgname ?? "";
            },
            'insurer_birthday' => function ($model) {
                return !empty($model->insurer_birthday) ? date('d.m.Y', $model->insurer_birthday) : "";
            },
            'insurer_license_series',
            'insurer_license_number',
            'insurer_license_given_date',
            'bridge_company' =>  function ($model) {
                return $model->bridgeCompany->name ?? '';
            },
        ], $filter);
    }

    /**
     * @OA\Get (
     *     path="/partner/osago-by-id",
     *     summary="get single osago by id",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#components/parameters/id"),
     *
     *     @OA\Response(response="200", description="single osago",
     *          @OA\JsonContent( type="object", ref="#components/schemas/osago")
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionOsagoById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $osago = Osago::findOne($id))
            return $osago->getFullAdminArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Get (
     *     path="/partner/kasko-by-id",
     *     summary="get single kasko by id",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#components/parameters/id"),
     *
     *     @OA\Response(response="200", description="single osago",
     *          @OA\JsonContent( type="object")
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionKaskoById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $kasko = Kasko::findOne($id))
            return $kasko->getFullArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Get(
     *     path="/partner/kasko-by-subscription",
     *     summary="Method to get all kasko-by-subscription with pagination ",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from id, applicant_name, phone"),
     *     @OA\Parameter (name="filter[status][]", in="query", @OA\Schema (type="array", @OA\Items(type="integer")), description="step1 => 1, step2 => 2, step3 => 3, step4 => 4, step5 => 5, payed => 6, 'canceled' => 7"),
     *     @OA\Parameter (name="filter[autonumber]", in="query", @OA\Schema (type="string")),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: 'id', 'count', 'applicant_name', 'fUser.phone', 'autonumber', 'tech_pass_series', 'tech_pass_number'"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="kasko-by-subscription with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#components/schemas/kasko_by_subscription")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionKaskoBySubscription()
    {
        GeneralHelper::checkPermission();

        $searchModel = new KaskoBySubscriptionSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);
        $models = $dataProvider->getModels();

        return [
            'models' => KaskoBySubscription::getShortAdminArrCollection($models),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/partner/kasko-by-subscription-export",
     *     summary="Method to get excel which contain all osagos",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from id, applicant_name, phone"),
     *     @OA\Parameter (name="filter[status][]", in="query", @OA\Schema (type="array", @OA\Items(type="integer")), description="step1 => 1, step2 => 2, step3 => 3, step4 => 4, step5 => 5, payed => 6, 'canceled' => 7"),
     *     @OA\Parameter (name="filter[autonumber]", in="query", @OA\Schema (type="string")),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: 'id', 'count', 'applicant_name', 'fUser.phone', 'autonumber', 'tech_pass_series', 'tech_pass_number'"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="excel string",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionKaskoBySubscriptionExport()
    {
        GeneralHelper::checkPermission();

        return GeneralHelper::export(KaskoBySubscription::className(), KaskoBySubscriptionSearch::className(), [
            'id' => 'integer',
            'applicant_name' => 'string',
            'phone' => 'string',
            'amount_uzs' => 'string',
            'autonumber' => 'string',
            'tech_pass_series' => 'string',
            'tech_pass_number' => 'string',
            'payment_type' => 'string',
            'begin_date' => 'string',
            'end_date' => 'string',
            'status' => 'integer',
            'policies_count' => 'integer',
            'saved_card' => 'string',
        ], [
            'id',
            'applicant_name',
            'phone' => function ($model) {
                return $model->fUser->phone ?? "";
            },
            'amount_uzs',
            'autonumber',
            'tech_pass_series',
            'tech_pass_number',
            'payment_type' => function ($model) {
                return "payme";
            },
            'begin_date' => function ($model) {
                return $model->lastKaskoBySubscriptionPolicy->begin_date ?? "";
            },
            'end_date' => function ($model) {
                return $model->lastKaskoBySubscriptionPolicy->end_date ?? "";
            },
            "status",
            "policies_count",
            'saved_card' => function ($model) {
                return $model->savedCard->card_mask ?? "";
            },
        ]);
    }

    /**
     * @OA\Get (
     *     path="/partner/kasko-by-subscription-by-id",
     *     summary="get single osago by id",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#components/parameters/id"),
     *     @OA\Response(response="200", description="single osago",
     *          @OA\JsonContent( type="object", ref="#components/schemas/kasko_by_subscription")
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionKaskoBySubscriptionById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $kbs = KaskoBySubscription::findOne($id)) {
            $kbs->policies_count = KaskoBySubscriptionPolicy::find()
                ->where(['kasko_by_subscription_id' => $id])
                ->andWhere(['status' => KaskoBySubscriptionPolicy::STATUS['received_policy']])
                ->count();
            return $kbs->getShortAdminArr();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Get(
     *     path="/partner/travel",
     *     summary="Method to get all travels with pagination ",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from id, phone_number, policy_number"),
     *     @OA\Parameter (name="filter[from_date]", in="query", @OA\Schema (type="string"), example="2022-12-30", description="payed date bo'yicha"),
     *     @OA\Parameter (name="filter[till_date]", in="query", @OA\Schema (type="string"), example="2022-12-30", description="payed date bo'yicha"),
     *     @OA\Parameter (name="filter[status][]", in="query", @OA\Schema (type="array", @OA\Items(type="integer")), description="step1 => 1, step2 => 2, step3 => 3, step4 => 4, payed => 6, waiting_for_policy => 7, received_policy => 8, 'canceled' => 9,"),
     *     @OA\Parameter (name="filter[payment_type][]", in="query", @OA\Schema (type="array", @OA\Items(type="string")), description="send click, payme, payze"),
     *     @OA\Parameter (name="filter[partner_id][]", in="query",  @OA\Schema (type="array", @OA\Items(type="integer")), description="send partner_id to filter for partner. Get partners list from partner/all to using select options"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, payed_date, amount_uzs, status, user.phone, trans.payment_type, policy_number, partner.name, created_at . use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="products with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="payed_date", type="string|null", example="27.09.2023"),
     *                  @OA\Property(property="begin_date", type="string|null", example="27.09.2022"),
     *                  @OA\Property(property="end_date", type="string|null", example="30.09.2023"),
     *                  @OA\Property(property="phone_number", type="string", example="998946464400"),
     *                  @OA\Property(property="program_name", type="string", example="sayohatchi gold max plus pro"),
     *                  @OA\Property(property="amount_uzs", type="integer|null", example=168000),
     *                  @OA\Property(property="travel_members_count", type="integer", example=3),
     *                  @OA\Property(property="status", type="integer", example=2),
     *                  @OA\Property(property="policy_number", type="string", example="AS123"),
     *                  @OA\Property(property="policy_pdf_url", type="string", example="http://asdfadf"),
     *                  @OA\Property(property="partner", type="object", ref="#/components/schemas/id_name"),
     *              )),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionTravel()
    {
        GeneralHelper::checkPermission();

        $searchModel = new TravelSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);
        $models = $dataProvider->getModels();

        $models = array_map(function ($model) {
            return [
                'id' => $model->id,
                'created_at' => is_null($model->created_at) ? null :  date('d.m.Y H:i:s', $model->created_at),
                'payed_date' => is_null($model->payed_date) ? null :  date('d.m.Y H:i:s', $model->payed_date),
                'begin_date' => is_null($model->begin_date) ? null :  DateHelper::date_format($model->begin_date, 'Y-m-d', 'm.d.Y'),
                'end_date' => is_null($model->end_date) ? null :  DateHelper::date_format($model->end_date, 'Y-m-d', 'm.d.Y'),
                'phone_number' => $model->user->phone ?? null,
                'program_name' => $model->program_name,
                'amount_uzs' => $model->amount_uzs,
                'travel_members_count' => count($model->travelMembers),
                'status' => $model->status,
                'policy_number' => $model->policy_number,
                'policy_pdf_url' => $model->policy_pdf_url,
                'partner' => $model->partner->getForIdNameArr(),
            ];
        }, $models);

        return [
            'models' => $models,
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/partner/travel-export",
     *     summary="Method to get excel which contain all travels",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from id, phone_number, policy_number"),
     *     @OA\Parameter (name="filter[from_date]", in="query", @OA\Schema (type="string"), example="2022-12-30", description="payed date bo'yicha"),
     *     @OA\Parameter (name="filter[till_date]", in="query", @OA\Schema (type="string"), example="2022-12-30", description="payed date bo'yicha"),
     *     @OA\Parameter (name="filter[status][]", in="query", @OA\Schema (type="array", @OA\Items(type="integer")), description="step1 => 1, step2 => 2, step3 => 3, step4 => 4, payed => 6, waiting_for_policy => 7, received_policy => 8, 'canceled' => 9,"),
     *     @OA\Parameter (name="filter[payment_type][]", in="query", @OA\Schema (type="array", @OA\Items(type="string")), description="send click, payme, payze"),
     *     @OA\Parameter (name="filter[partner_id][]", in="query",  @OA\Schema (type="array", @OA\Items(type="integer")), description="send partner_id to filter for partner. Get partners list from partner/all to using select options"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, payed_date, amount_uzs, status, user.phone, trans.payment_type, policy_number, partner.name, created_at . use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="excel string",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionTravelExport()
    {
        $filter = \Yii::$app->request->get()['filter'] ?? [];
        if (!array_key_exists('from_date', $filter) and !array_key_exists('till_date', $filter)) {
            $filter['till_date'] = date('Y-m-d');
            $filter['from_date'] = date('Y-m-d', strtotime("-1 month"));
        }

        if (!array_key_exists('from_date', $filter) and array_key_exists('till_date', $filter))
            throw new BadRequestHttpException("from_date is required");
        if (array_key_exists('from_date', $filter) and !array_key_exists('till_date', $filter))
            throw new BadRequestHttpException("till_date is required");

        $ts1 = strtotime($filter['till_date']);
        $ts2 = strtotime($filter['from_date']);
        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);
        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);
        $diff = abs((($year2 - $year1) * 12) + ($month2 - $month1));
        if ($diff > 1)
            throw new BadRequestHttpException("Интервал времени не должен превышать одного месяца");

        return GeneralHelper::export(Travel::className(), TravelSearch::className(), [
            'id' => 'integer',
            'payed_date' => 'string',
            'begin_date' => 'string',
            'end_date' => 'string',
            'program_name' => 'string',
            'amount_uzs' => 'string',
            'travel_members_count' => 'integer',
            'status' => 'string',
            'phone_number' => 'string',
            'partner' => 'string',
            'policy_number' => 'string',
            'policy_pdf_url' => 'string',
        ], [
            'id',
            'payed_date' => function ($model) {
                return is_null($model->payed_date) ? null :  date('d.m.Y H:i:s', $model->payed_date);
            },
            'begin_date' => function ($model) {
                return is_null($model->begin_date) ? null :  DateHelper::date_format($model->begin_date, 'Y-m-d', 'd.m.Y');
            },
            'end_date' => function ($model) {
                return is_null($model->end_date) ? null :  DateHelper::date_format($model->end_date, 'Y-m-d', 'd.m.Y');
            },
            'program_name',
            'amount_uzs',
            'travel_members_count' => function ($model) {
                return count($model->travelMembers);
            },
            'status' =>  function ($model) {
                return in_array($model->status, [Travel::STATUSES['payed'], Travel::STATUSES['waiting_for_policy'], Travel::STATUSES['received_policy']]) ? "payed" : "no payed";
            },
            'phone_number' =>  function ($model) {
                return $model->user->phone ?? '';
            },
            'partner' =>  function ($model) {
                return $model->partner->name ?? '';
            },
            'policy_number',
            'policy_pdf_url',
        ], $filter);
    }

    /**
     * @OA\Get (
     *     path="/partner/travel-by-id",
     *     summary="get single travel by id",
     *     tags={"PartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#components/parameters/id"),
     *
     *     @OA\Response(response="200", description="single Travel",
     *          @OA\JsonContent( type="object", ref="#components/schemas/travel")
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionTravelById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $travel = Travel::findOne($id))
            return $travel->getFullAdminArr();

        throw new BadRequestHttpException("ID is incorrect");
    }
}
