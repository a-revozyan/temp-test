<?php
namespace backapi\controllers;

use backapi\models\forms\carInspectionPartner\CreateAccountForm;
use backapi\models\searchs\CarInspectionPartnerSearch;
use backapi\models\searchs\CarInspectionPartnerStatisticsSearch;
use backapi\models\searchs\PartnerAccountSearch;
use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\Partner;
use common\models\PartnerAccount;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class CarInspectionPartnerController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create-account' => ['POST'],
            ],
        ];

        $behaviors['authenticator']['except'] = [];

        return $behaviors;
    }

    public $row_respons_action_ids = ['export-partner-accounts'];


    /**
     * @OA\Get(
     *     path="/car-inspection-partner/statistics",
     *     summary="Method to get all statistic about car inspection partner",
     *     tags={"CarInspectionPartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[begin_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[end_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Response(
     *         response="200", description="all statistic info",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="available_car_inspection_count", type="integer", example="78"),
     *              @OA\Property(property="done_car_inspection_count", type="integer", example="38"),
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

        $search_model = new CarInspectionPartnerStatisticsSearch();
        return $search_model->search($filter);
    }


    /**
     * @OA\Get(
     *     path="/car-inspection-partner/all",
     *     summary="Method to get all partner with or without pagination ",
     *     tags={"CarInspectionPartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[begin_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[end_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name, service_amount, available_car_inspection_count, done_car_inspection_count, status, account_count. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="partners with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $filter = $this->get['filter'] ?? [];
        if (!array_key_exists('begin_date', $filter))
            $filter['begin_date'] = date('Y-m-01');

        $searchModel = new CarInspectionPartnerSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Partner::getCarInspectionArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }


    /**
     * @OA\Get(
     *     path="/car-inspection-partner/get-by-id",
     *     summary="get partner by id",
     *     tags={"CarInspectionPartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="agent",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id)
        {
            if (empty(Partner::findOne($id)))
                throw new BadRequestHttpException("ID is incorrect");

            $filter = ['id' => $id];
            $searchModel = new CarInspectionPartnerSearch();
            $dataProvider = $searchModel->search($filter);
            return $dataProvider->getModels()[0]->getCarInspectionArr();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Get(
     *     path="/car-inspection-partner/partner-accounts",
     *     summary="Method to get all partner accounts with or without pagination ",
     *     tags={"CarInspectionPartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[begin_date]", in="query", @OA\Schema (type="string"), description="filtering by period: Y-m-d"),
     *     @OA\Parameter (name="filter[end_date]", in="query", @OA\Schema (type="string"), description="filtering by period: Y-m-d"),
     *     @OA\Parameter (name="filter[partner_id]", in="query", @OA\Schema (type="integer"), description="filtering by partner"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, created_at, amount. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="account with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/partner_account")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionPartnerAccounts()
    {
        GeneralHelper::checkPermission();

        $searchModel = new PartnerAccountSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => PartnerAccount::getShortArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/car-inspection-partner/export-partner-accounts",
     *     summary="Method to get all partner accounts excel",
     *     tags={"CarInspectionPartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[begin_date]", in="query", @OA\Schema (type="string"), description="filtering by period: Y-m-d"),
     *     @OA\Parameter (name="filter[end_date]", in="query", @OA\Schema (type="string"), description="filtering by period: Y-m-d"),
     *     @OA\Parameter (name="filter[partner_id]", in="query", @OA\Schema (type="integer"), description="filtering by partner"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, created_at, amount. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="excel string",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionExportPartnerAccounts()
    {
        return GeneralHelper::export(PartnerAccount::className(), PartnerAccountSearch::className(), [
            'id' => 'integer',
            'amount' => 'string',
            'note' => 'string',
            'username' => 'string',
            'created_at' => 'string',
        ], [
            'id',
            'amount',
            'note',
            'username' => function($model){
                return $model->user->username;
            },
            'created_at' => function($model){
                return !empty($model->created_at) ? DateHelper::date_format($model->created_at, 'Y-m-d H:i:s', 'd.m.Y H:i:s') : "";
            },
        ]);
    }


    /**
     * @OA\Post(
     *     path="/car-inspection-partner/create-account",
     *     summary="Method to top up account of partner",
     *     tags={"CarInspectionPartnerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"partner_id", "amount"},
     *                 @OA\Property (property="partner_id", type="integer", description="id of partner"),
     *                 @OA\Property (property="amount", type="integer", description="amount of money"),
     *                 @OA\Property (property="note", type="string", description="note for top up account"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created account",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/partner_account")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreateAccount(): array
    {
        GeneralHelper::checkPermission();

        $model = new CreateAccountForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getShortArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

}