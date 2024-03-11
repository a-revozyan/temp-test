<?php
namespace backapi\controllers;

use backapi\models\forms\carInspectionForms\ChangeStatusForm;
use backapi\models\forms\carInspectionForms\CreateCarInspectionForm;
use backapi\models\forms\carInspectionForms\SendInviteSmsForm;
use backapi\models\forms\carInspectionForms\UpdateCarInspectionForm;
use backapi\models\searchs\CarInspectionSearch;
use backapi\models\searchs\CarInspectionStatisticsSearch;
use backapi\models\searchs\StatusHistorySearch;
use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\CarInspection;
use common\models\StatusHistory;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class CarInspectionController extends BaseController
{
    public $row_respons_action_ids = ["get-status-history-excel"];
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'send-invite-sms' => ['POST'],
            ],
        ];

        $behaviors['authenticator']['except'] = ["get-act-inspection"];
        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/car-inspection/send-invite-sms",
     *     summary="Method to send sms which contain link to mobile app",
     *     tags={"CarInspectionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property (property="id", type="integer", example="id of car inspection"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated car inspection",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/car_inspection")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSendInviteSms()
    {
        GeneralHelper::checkPermission();

        $model = new SendInviteSmsForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/car-inspection/statistics",
     *     summary="Method to get all statistic about car inspection",
     *     tags={"CarInspectionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[begin_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[end_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Response(
     *         response="200", description="all statistic info",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="new", type="integer", example="78"),
     *              @OA\Property(property="processing", type="integer", example="38"),
     *              @OA\Property(property="problematic", type="integer", example="38"),
     *              @OA\Property(property="uploaded", type="integer", example="38"),
     *              @OA\Property(property="confirmed_to_cvat", type="integer", example="30"),
     *              @OA\Property(property="rejected", type="integer", example="37"),
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

        $search_model = new CarInspectionStatisticsSearch();
        return $search_model->search($filter);
    }


    /**
     * @OA\Get(
     *     path="/car-inspection/all",
     *     summary="Method to get all car inspection with or without pagination ",
     *     tags={"CarInspectionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from id, autonumber, phone"),
     *     @OA\Parameter (name="filter[status][]", in="query", @OA\Schema (type="integer"), description="
    'created' => 0,
    'processing' => 1,
    'uploaded' => 2,
    'rejected' => 3,
    'confirmed_to_cvat' => 4,
    'completed' => 5,
    'sent_verification_sms' => 6,
    'verified_by_client' => 7,
    'problematic' => 8,"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, created_at, phone, autonumber, status. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="car inspections with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/car_inspection")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new CarInspectionSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => CarInspection::getShortArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/car-inspection/export",
     *     summary="Method to get all car_inspection excel",
     *     tags={"CarInspectionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from name"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="1 => acktive, 0 => inactive"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name, order, status. use '-' for descending"),
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
        return GeneralHelper::export(CarInspection::className(), CarInspectionSearch::className(), [
            'id' => 'integer',
            'created_at' => 'string',
            'phone' => 'string',
            'autonumber' => 'string',
            'status' => 'string',
        ], [
            'id',
            'created_at',
            'phone' => function($model){
                return $model->client->phone;
            },
            'autonumber',
            'status' => function($model){
                if (array_key_exists($model->status, array_flip(CarInspection::STATUS)))
                    return array_flip(CarInspection::STATUS)[$model->status];
                else
                    return "";
            },
        ]);
    }

    /**
     * @OA\Get(
     *     path="/car-inspection/get-by-id",
     *     summary="get car_inspection by id",
     *     tags={"CarInspectionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="agent",
     *         @OA\JsonContent(type="object", ref="#components/schemas/full_car_inspection")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id)
            return CarInspection::findOne($id)->getFullArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Post(
     *     path="/car-inspection/update",
     *     summary="Method to change update car inspection",
     *     tags={"CarInspectionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property (property="id", type="integer", description="id of car inspection"),
     *                 @OA\Property (property="runwaty", type="integer", example="1234"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated car inspection",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/car_inspection")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateCarInspectionForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/car-inspection/change-status",
     *     summary="Method to change status of car inspection",
     *     tags={"CarInspectionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property (property="id", type="integer", description="id of car inspection"),
     *                 @OA\Property (property="status", type="integer", description="
                        'rejected' => 3,
                        'confirmed_to_cvat' => 4"),
     *                 @OA\Property (property="comment", type="string", description="comment of changing status"),
     *                 @OA\Property (property="types[]", type="array", @OA\Items(type="integer"),  description="checked positions"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated car inspection",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/car_inspection")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionChangeStatus()
    {
        GeneralHelper::checkPermission();

        $model = new ChangeStatusForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/car-inspection/create-car-inspection",
     *     summary="create new car inspection",
     *     tags={"CarInspectionController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"partner_id", "tex_pass_series", "tex_pass_number", "phone", "autonumber"},
     *                 @OA\Property (property="partner_id", type="integer", example="1"),
     *                 @OA\Property (property="tex_pass_series", type="string", example="XA"),
     *                 @OA\Property (property="tex_pass_number", type="string", example="5486256"),
     *                 @OA\Property (property="phone", type="string", example="998946464400"),
     *                 @OA\Property (property="autonumber", type="string", example="80U950JA"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created car inspection",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/car_inspection")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreateCarInspection()
    {
        $model = new CreateCarInspectionForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save(true)->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/car-inspection/send-ready-message",
     *     summary="send notification about ready akt osmotr",
     *     tags={"CarInspectionController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property (property="id", type="integer", example="1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created car inspection",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/full_car_inspection")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSendReadyMessage()
    {
        if (empty($this->post) or !array_key_exists('id', $this->post) or (int)$this->post['id'] != $this->post['id'] or !$car_inspection = CarInspection::findOne(['id' => $this->post['id']]))
            throw new NotFoundHttpException();

        $verification_url =  GeneralHelper::env('saas_front_site_url') . '/ru/' . $car_inspection->uuid . '/document';
        $car_inspection->notify_client(Yii::t('app', "Tabriklaymiz, sizning akt osmotiringiz tayyor bo'ldi. quyidagi linkka kiring: ") . $verification_url);

        return $car_inspection->getFullArr();
    }

    /**
     * @OA\Get(
     *     path="/car-inspection/get-act-inspection",
     *     summary="get act of car inspection by id",
     *     tags={"CarInspectionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="uuid", in="query", @OA\Schema (type="string"), example="sdfgh-dfghjkl-45678ig"),
     *     @OA\Response(
     *         response="200", description="act pdf",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionGetActInspection(string $uuid)
    {
        if (!$car_inspection = CarInspection::findOne(['uuid' => $uuid]))
            throw new NotFoundHttpException(Yii::t('app', 'uuid not found'));

        return $car_inspection->getActInspection();
    }

    /**
     * @OA\Get(
     *     path="/car-inspection/get-status-history",
     *     summary="Method to get all status hisotories with or without pagination ",
     *     tags={"CarInspectionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="status history with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/status_history")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetStatusHistory(int $id)
    {
        $filter = $this->get['filter'] ?? [];
        $filter = array_merge($filter, [
            'model_id' => $id, 'model_class' => 'CarInspection'
        ]);
        $searchModel = new StatusHistorySearch();
        $dataProvider = $searchModel->search($filter);

        return [
            'models' => StatusHistory::getFullArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/car-inspection/get-status-history-excel",
     *     summary="Method to get all status history excel",
     *     tags={"CarInspectionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="excel string",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetStatusHistoryExcel(int $id)
    {
        $filter = $this->get['filter'] ?? [];
        $filter = array_merge($filter, [
            'model_id' => (int)$id, 'model_class' => 'CarInspection'
        ]);

        return GeneralHelper::export(StatusHistory::className(), StatusHistorySearch::className(), [
            'to_status' => 'string',
            'created_at' => 'string',
        ], [
            'to_status' => function($model){
               return CarInspection::getStatusLabel($model->to_status);
            },
            'created_at' => function($model){
                return is_null($model->created_at) ? null :  DateHelper::date_format($model->created_at, 'Y-m-d H:i:s', 'd.m.Y H:i:s');
            }
        ], $filter);
    }

}