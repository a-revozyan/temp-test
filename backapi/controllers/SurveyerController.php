<?php
namespace backapi\controllers;

use backapi\models\forms\surveyerForms\CreateSurveyerForm;
use backapi\models\forms\surveyerForms\SetServiceAmountSurveyerForm;
use backapi\models\forms\surveyerForms\UpdateSurveyerForm;
use backapi\models\searchs\KaskoSearch;
use backapi\models\searchs\SurveyerSearch;
use common\helpers\GeneralHelper;
use common\models\Kasko;
use common\models\Surveyer;
use XLSXWriter;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class SurveyerController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['POST'],
                'update' => ['PUT'],
                'set-service-amount' => ['PUT'],
                'delete' => ['DELETE'],
            ],
        ];
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
     *     path="/surveyer/statistics",
     *     summary="Method to get all statistic info which in Anketor page",
     *     tags={"SurveyerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Response(
     *         response="200", description="all statistic info",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="in_process", type="integer", example=12),
     *              @OA\Property(property="processed_in_month", type="integer", example=12),
     *              @OA\Property(property="dayly_average_process_time", type="integer", example=12),
     *              @OA\Property(property="dayly_problem_kaskos", type="integer", example=12),
     *              @OA\Property(property="surveyer_count", type="integer", example=12),
     *              @OA\Property(property="monthly_amount", type="integer|null", example=12),
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStatistics()
    {
        GeneralHelper::checkPermission();

        $before_24_housers = strtotime('-24 hours');
        $beginning_of_month = date_create_from_format('Y-m-d', date('Y-m-01'))->setTime(0, 0, 0)->getTimestamp();

        $in_process = Kasko::find()->where(['status' => Kasko::STATUS['attached']])->count();
        $processed_in_month = Kasko::find()->where(['between', 'processed_date', $beginning_of_month, time()])->count();
        $today_processed_kaskos = Kasko::find()->where(['between', 'processed_date', $before_24_housers, time()]);
        $average_process_time = 0;
        if ($today_processed_kaskos->count() != 0)
            $average_process_time = $today_processed_kaskos->sum("processed_date - payed_date")
                / $today_processed_kaskos->count();

        $problem_kaskos = $today_processed_kaskos->andWhere("(processed_date - payed_date) > 86400")->count();
        $surveyer_count = Surveyer::find()
            ->leftJoin('auth_assignment', '"user"."id" = CAST("auth_assignment"."user_id" AS INTEGER)')
            ->where(['auth_assignment.item_name' => Surveyer::SURVEYER_ROLE_NAME])
            ->count();
        $amount = Surveyer::find()->leftJoin(
            [
                "kasko" => Kasko::find()->select([
                    "sum(kasko.surveyer_amount) as amount",
                    'surveyer_id',
                ])
                    ->where(['between', 'processed_date', $beginning_of_month, time()])
                    ->groupBy('surveyer_id')
            ],
            '"kasko"."surveyer_id" = "user"."id"'
        )->sum('kasko.amount');

        return [
            'in_process' => $in_process,
            'processed_in_month' => $processed_in_month,
            'dayly_average_process_time' => round($average_process_time / (60 * 60)),
            'dayly_problem_kaskos' => $problem_kaskos,
            'surveyer_count' => $surveyer_count,
            'monthly_amount' => $amount
        ];
    }


    /**
     * @OA\Get(
     *     path="/surveyer/all",
     *     summary="Method to get all users with or without pagination ",
     *     tags={"SurveyerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="search by name and phone number"),
     *     @OA\Parameter (name="filter[region_id]", in="query", @OA\Schema (type="integer"), description="get list from /region/all API"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: 'created_at', 'kasko_count', 'average_processed_time', 'phone_number'. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="users with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/surveyer")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="autobmodel without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/surveyer")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new SurveyerSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => $dataProvider->getModels(),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/surveyer/export",
     *     summary="Method to get all surveyer excel",
     *     tags={"SurveyerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="search by name and phone number"),
     *     @OA\Parameter (name="filter[region_id]", in="query", @OA\Schema (type="integer"), description="get list from /region/all API"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: 'created_at', 'kasko_count', 'average_processed_time', 'phone_number'. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="excel string",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionExport()
    {
        GeneralHelper::checkPermission();

        $searchModel = new SurveyerSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        $models = array_map(function ($model){
            return [
                'id' => $model['id'],
                'first_name' => $model['first_name'],
                'last_name' => $model['last_name'],
                'region_name' => $model['region_name'],
                'created_at' => empty($model['created_at']) ? '' : date('Y-m-d', $model['created_at']),
                'kasko_count' => $model['kasko_count'],
                'average_processed_time' => $model['average_processed_time'],
                'phone_number' => $model['phone_number'],
                'status' => $model['status'],
            ];
        }, $dataProvider->getModels());

        $writer = new XLSXWriter();
        $writer->writeSheet($models,'kasko tariffs',
            [
                'id' => 'integer',
                'first_name' => 'string',
                'last_name' => 'string',
                'region_name' => 'string',
                'created_at' => 'string',
                'kasko_count' => 'integer',
                'average_processed_time' => 'integer',
                'phone_number' => 'string',
                'status' => 'integer',
            ]);
        return $writer->writeToString();
    }

    /**
     * @OA\Post(
     *     path="/surveyer/create",
     *     summary="create new surveyer",
     *     tags={"SurveyerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"region_id", "first_name", "phone_number", "password", "repeat_password", "status"},
     *                 @OA\Property (property="region_id", type="integer", example="get from /region/all"),
     *                 @OA\Property (property="first_name", type="string", example="Jobir"),
     *                 @OA\Property (property="phone_number", type="string", example="998946464400"),
     *                 @OA\Property (property="password", type="string", example="password"),
     *                 @OA\Property (property="repeat_password", type="string", example="password"),
     *                 @OA\Property (property="status", type="integer", example="1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created surveyer",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/surveyer")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateSurveyerForm();

        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/surveyer/set-service-amount",
     *     summary="set service amount",
     *     tags={"SurveyerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "service_amount"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="service_amount", type="integer", example="service amount"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated surveyer",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/surveyer")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSetServiceAmount()
    {
        GeneralHelper::checkPermission();

        $model = new SetServiceAmountSurveyerForm();

        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/surveyer/update",
     *     summary="update surveyer",
     *     tags={"SurveyerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"region_id", "first_name", "phone_number", "password", "repeat_password", "status", "id"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="region_id", type="integer", example="1"),
     *                 @OA\Property (property="first_name", type="string", example="Jobir"),
     *                 @OA\Property (property="phone_number", type="string", example="998946464400"),
     *                 @OA\Property (property="password", type="string", example="password"),
     *                 @OA\Property (property="repeat_password", type="string", example="password"),
     *                 @OA\Property (property="status", type="integer", example="1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated surveyer",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/surveyer")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateSurveyerForm();

        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/surveyer/get-by-id",
     *     summary="get surveyer by id",
     *     tags={"SurveyerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="agent",
     *         @OA\JsonContent(type="object",
     *          @OA\Property(property="id", type="integer", example=1),
     *          @OA\Property(property="first_name", type="string", example="Jobir"),
     *          @OA\Property(property="last_name", type="string", example="Yusupov"),
     *          @OA\Property(property="region_name", type="string", example="Buxoro"),
     *          @OA\Property(property="created_at", type="integer", example="1658840738"),
     *          @OA\Property(property="kasko_count", type="integer", example="10"),
     *          @OA\Property(property="average_processed_time", type="integer|null", example="1658840738"),
     *          @OA\Property(property="phone_number", type="string", example="998946464400"),
     *          @OA\Property (property="status", type="integer", example="1"),
     *          @OA\Property (property="average_process_time", type="integer", example="100000"),
     *          @OA\Property (property="processed_kaskos_count", type="integer", example="5"),
     *          @OA\Property (property="amount", type="integer", example="250000"),
     *     )
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        $surveyer = $this->findById($id);

        $beginning_of_month = date_create_from_format('Y-m-d', date('Y-m-01'))->setTime(0, 0, 0)->getTimestamp();
        $monthly_processed_kaskos = Kasko::find()->where(['between', 'processed_date', $beginning_of_month, time()])
            ->andWhere(['surveyer_id' => $id]);
        $processed_kaskos_count = $monthly_processed_kaskos->count();
        $average_process_time = 0;
        if ($processed_kaskos_count != 0)
            $average_process_time = $monthly_processed_kaskos->sum("processed_date - payed_date")
                / $processed_kaskos_count;

        $surveyer_array = $surveyer->toArray();
        unset($surveyer_array['auth_key']);
        unset($surveyer_array['password_hash']);
        unset($surveyer_array['password_reset_token']);
        unset($surveyer_array['access_token']);
        return array_merge($surveyer_array, [
            "average_process_time" => $average_process_time,
            "processed_kaskos_count" => $processed_kaskos_count,
            "amount" => $monthly_processed_kaskos->sum('surveyer_amount'),
        ]);
    }

    /**
     * @OA\Delete  (
     *     path="/surveyer/delete",
     *     summary="Method to delete surveyer by id",
     *     tags={"SurveyerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="return 1 if successfully deleted",
     *         @OA\JsonContent(type="integer", example=1)
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionDelete($id)
    {
        GeneralHelper::checkPermission();

        $surveyer = $this->findById($id);
        if (!empty($surveyer->kaskos))
            throw new BadRequestHttpException("This surveyer has some kaskos");

        return $surveyer->delete();
    }

    /**
     * @param $id
     * @return array|Surveyer
     * @throws BadRequestHttpException
     */
    public function findById($id)
    {
        if (!is_null($id) and is_numeric($id) and (int)$id == $id
            and $surveyer = Surveyer::find()->leftJoin('auth_assignment', '"user"."id" = CAST("auth_assignment"."user_id" AS INTEGER)')
                ->where(['auth_assignment.item_name' => Surveyer::SURVEYER_ROLE_NAME])->andWhere(['id' => $id])->one()
        )
            return $surveyer;

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Get(
     *     path="/surveyer/kaskos",
     *     summary="Method to get all kaskos",
     *     tags={"SurveyerController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[region_name]", in="query", @OA\Schema (type="integer"), description="search by name and phone number"),
     *     @OA\Parameter (name="filter[processed_date_from]", in="query", @OA\Schema (type="integer"), description="1659409879"),
     *     @OA\Parameter (name="filter[processed_date_to]", in="query", @OA\Schema (type="integer"), example="1659409879"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: 'created_at', 'kasko_count', 'average_processed_time', 'phone_number'. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="users with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="payed_date", type="string", example="d.m.Y"),
     *                  @OA\Property(property="processed_date", type="string", example="d.m.Y"),
     *                  @OA\Property(property="status", type="integer", example="1"),
     *                  @OA\Property(property="region_name", type="string", example="Buxoro"),
     *                  @OA\Property(property="surveyer_id", type="integer", example="12"),
     *                  @OA\Property(property="surveyer_name", type="string", example="Jobir"),
     *                  @OA\Property(property="user_id", type="integer", example="12"),
     *                  @OA\Property(property="user_first_name", type="string", example="Jobir"),
     *                  @OA\Property(property="user_last_name", type="string", example="Yusupov"),
     *                  @OA\Property(property="user_phone_number", type="string", example="998946464400"),
     *                  @OA\Property(property="partner_name", type="string", example="Gross"),
     *                  @OA\Property(property="partner_id", type="integer", example="12"),
     *                  @OA\Property(property="kasko_files", type="array", @OA\Items(type="object",
     *                      @OA\Property(property="partner_id", type="integer", example="12"),
     *                  )),
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

        $models = array_map(function ($model){
            return [
                'id' => $model->id,
                'payed_date' => is_null($model->payed_date) ? null : date('d.m.Y', $model->payed_date),
                'processed_date' => is_null($model->processed_date) ? null :  date('d.m.Y', $model->processed_date),
                'status' => $model->status,
                'region_name' => $model->surveyer->region->name_ru ?? "",
                'surveyer_id' => $model->surveyer->id ?? null,
                'surveyer_name' => $model->surveyer->first_name ?? "",
                'user_id' => $model->fUser->id ?? null,
                'user_first_name' => $model->fUser->first_name ?? "",
                'user_last_name' => $model->fUser->last_last ?? "",
                'user_phone_number' => $model->fUser->phone ?? "",
                'partner_name' => $model->partner->name ?? "",
                'partner_id' => $model->partner->id ?? null,
                'kasko_files' => $model->kaskoFile,
            ];
        }, $models);

        return [
            'models' => $models,
            'pages' => $dataProvider->getPagination()
        ];
    }
}