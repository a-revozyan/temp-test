<?php
namespace backapi\controllers;

use backapi\models\forms\bridgeCompanyForms\Statistics;
use backapi\models\searchs\OsagoSearch;
use backapi\models\User;
use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\Osago;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class BridgeCompanyProfileController extends BaseController
{
    public $row_respons_action_ids = ["kaskos-export", "osago-export", "kasko-by-subscription-export", "travel-export"];
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [],
        ];

        $behaviors['authenticator']['except'] = [];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/bridge-company-profile/statistics",
     *     summary="Method to get all statistic info which in bridge company profile page",
     *     tags={"BridgeCompanyProfile"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[month]", in="query", @OA\Schema (type="string"), description="2023-03"),
     *     @OA\Response(
     *         response="200", description="all statistic info",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="partner", ref="#components/schemas/id_name"),
     *              @OA\Property(property="product", ref="#components/schemas/id_name"),
     *              @OA\Property(property="number_drivers", ref="#components/schemas/id_name"),
     *              @OA\Property(property="count", type="integer", example=142),
     *              @OA\Property(property="sum", type="integer", example=12),
     *              @OA\Property(property="bridge_company_divvy_percent", type="integer", example=12),
     *              @OA\Property(property="bridge_company_divvy_amount", type="integer", example=12),
     *              @OA\Property(property="month", type="string", example="2023-11"),
     *              @OA\Property(property="divvy_percent", type="integer", example="12"),
     *              @OA\Property(property="divvy_amount", type="integer", example="25000"),
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStatistics()
    {
//        GeneralHelper::checkPermission();

        $model = new Statistics();
        $model->setAttributes($this->get['filter'] ?? []);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/bridge-company-profile/osago",
     *     summary="Method to get all osagos with pagination ",
     *     tags={"BridgeCompanyProfileController"},
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
        $filter = $this->get['filter'] ?? [];

        $user = User::findOne(Yii::$app->user->identity->getId());
        if (empty($user->bridgeCompany))
            throw new BadRequestHttpException('You are not bridge company');
        $filter = array_merge($filter, ['bridge_company_id' => $user->bridgeCompany->id]);

        $dataProvider = $searchModel->search($filter);
        $models = $dataProvider->getModels();

        $models = array_map(function ($model){
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
            ];
        }, $models);

        return [
            'models' => $models,
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/bridge-company-profile/osago-export",
     *     summary="Method to get excel which contain all osagos",
     *     tags={"BridgeCompanyProfileController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from id, phone_number, policy_number"),
     *     @OA\Parameter (name="filter[status][]", in="query", @OA\Schema (type="array", @OA\Items(type="integer")), description="step1 => 1, step2 => 2, step3 => 3, step4 => 4, payed => 6, waiting_for_policy => 7, received_policy => 8, 'canceled' => 9,"),
     *     @OA\Parameter (name="filter[payment_type][]", in="query", @OA\Schema (type="array", @OA\Items(type="string")), description="send click, payme, payze"),
     *     @OA\Parameter (name="filter[from_date]", in="query", @OA\Schema (type="string"), example="2022-12-30", description="payed date bo'yicha"),
     *     @OA\Parameter (name="filter[till_date]", in="query", @OA\Schema (type="string"), example="2022-12-30", description="payed date bo'yicha"),
     *     @OA\Parameter (name="filter[with_discount]", in="query", @OA\Schema (type="integer"), description="0 yoki 1"),
     *     @OA\Parameter (name="filter[is_juridic]", in="query", @OA\Schema (type="integer"), description="0 yoki 1"),
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
        if (!array_key_exists('from_date', $filter) and !array_key_exists('till_date', $filter))
        {
            $filter['till_date'] = date('Y-m-d');
            $filter['from_date'] = date('Y-m-d', strtotime("-1 month"));
        }

        if (!array_key_exists('from_date', $filter) and array_key_exists('till_date', $filter))
            throw new BadRequestHttpException("from_date is required");
        if (array_key_exists('from_date', $filter) and !array_key_exists('till_date', $filter))
            throw new BadRequestHttpException("till_date is required");

        $user = User::findOne(Yii::$app->user->identity->getId());
        if (empty($user->bridgeCompany))
            throw new BadRequestHttpException('You are not bridge company');
        $filter = array_merge($filter, ['bridge_company_id' => $user->bridgeCompany->id]);

        $ts1 = strtotime($filter['till_date']);
        $ts2 = strtotime($filter['from_date']);
        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);
        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);
        $diff = abs((($year2 - $year1) * 12) + ($month2 - $month1));
        if ($diff > 1)
            throw new BadRequestHttpException("Интервал времени не должен превышать одного месяца");

        return GeneralHelper::export(Osago::className(), OsagoSearch::className(), [
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
//            'policy_pdf_url' => 'string',
            'autonumber' => "string",
            'partner' => "string",
            'created_at' => "string",
            'accident_policy_number' => "string",
            'accident_amount' => "string",
            'pinfl' => "string",
            'created_in_telegram' => "string",
        ], [
            'id',
            'payed_date' => function($model){
                return is_null($model->payed_date) ? null :  date('d.m.Y H:i:s', $model->payed_date);
            },
            'begin_date' => function($model){
                return is_null($model->begin_date) ? null :  DateHelper::date_format($model->begin_date, 'Y-m-d', 'd.m.Y');
            },
            'amount_uzs',
            'amount_uzs_without_discount' =>  function($model){
                return $model->getAmountUzsWithoutDiscount();
            },
//            'status',
            'status' =>  function($model){
                return in_array($model->status, [Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']]) ? "payed" : "no payed";
            },
            'number_drivers_id' => function($model){
                return $model->numberDrivers->name_ru ?? '';
            },
            'phone_number' =>  function($model){
                return $model->user->phone ?? '';
            },
            'payment_type' =>  function($model){
                return $model->trans->payment_type ?? '';
            },
            'policy_number',
//            'policy_pdf_url',
            'autonumber',
            'partner' => function($model){
                return !is_null($model->partner) ? $model->partner->name : "";
            },
            'created_at' => function($model){
                return is_null($model->created_at) ? null :  date('d.m.Y', $model->created_at);
            },
            'accident_policy_number' => function($model){
                return $model->accident->policy_number ?? '';
            },
            'accident_amount',
            'pinfl' => function($model){
                return $model->insurer_pinfl;
            },
            'created_in_telegram',
        ], $filter);
    }

    /**
     * @OA\Get (
     *     path="/bridge-company-profile/osago-by-id",
     *     summary="get single osago by id",
     *     tags={"BridgeCompanyProfileController"},
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

        $user = User::findOne(Yii::$app->user->identity->getId());
        if (empty($user->bridgeCompany))
            throw new BadRequestHttpException('You are not bridge company');

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $osago = Osago::findOne(['id' => $id, 'bridge_company_id' => $user->bridgeCompany->id]))
            return $osago->getFullAdminArr();

        throw new BadRequestHttpException("ID is incorrect");
    }
}