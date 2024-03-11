<?php
namespace backapi\controllers;

use backapi\models\forms\carInspectionForms\CreateCarInspectionForm;
use backapi\models\searchs\CarInspectionPartnerSearch;
use backapi\models\searchs\CarInspectionSearch;
use backapi\models\searchs\CarInspectionStatisticsSearch;
use common\helpers\GeneralHelper;
use common\models\CarInspection;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class CarInspectionForPartnerController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create-car-inspection' => ['POST'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/car-inspection-for-partner/statistics",
     *     summary="Method to get all statistic about car inspection",
     *     tags={"CarInspectionForPartnerController"},
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
//        GeneralHelper::checkPermission();

        $filter = $this->get['filter'] ?? [];
        if (!array_key_exists('begin_date', $filter))
            $filter['begin_date'] = date('Y-m-01');

        $filter['partner_id'] = \Yii::$app->user->identity->partner_id;
        $search_model = new CarInspectionStatisticsSearch();

        $searchModel = new CarInspectionPartnerSearch();
        $dataProvider = $searchModel->search(['partner_id' => \Yii::$app->user->identity->partner_id]);

        return array_merge([
            'partner' => $dataProvider->getModels()[0]->getCarInspectionArr()
        ], $search_model->search($filter));
    }


    /**
     * @OA\Get(
     *     path="/car-inspection-for-partner/all",
     *     summary="Method to get all car inspection with or without pagination ",
     *     tags={"CarInspectionForPartnerController"},
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
//        GeneralHelper::checkPermission();

        $filter = ['partner_id' => \Yii::$app->user->identity->partner_id];
        $searchModel = new CarInspectionSearch();
        $dataProvider = $searchModel->search(array_merge($filter, ($this->get['filter'] ?? [])));

        return [
            'models' => CarInspection::getShortArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/car-inspection-for-partner/export",
     *     summary="Method to get all car_inspection excel",
     *     tags={"CarInspectionForPartnerController"},
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
        ], ['partner_id' => \Yii::$app->user->identity->partner_id]);
    }

    /**
     * @OA\Get(
     *     path="/car-inspection-for-partner/get-by-id",
     *     summary="get car_inspection by id",
     *     tags={"CarInspectionForPartnerController"},
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
//        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id
            and $car_inspection = CarInspection::find()->where(['partner_id' => \Yii::$app->user->identity->partner_id, 'id' => $id])->one()
        )
            return $car_inspection->getFullArr();

        throw new BadRequestHttpException("ID is incorrect");
    }


    /**
     * @OA\Post(
     *     path="/car-inspection-for-partner/create-car-inspection",
     *     summary="create new car inspection",
     *     tags={"CarInspectionForPartnerController"},
     *     security={ {"basicAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"tex_pass_series", "tex_pass_number", "phone", "autonumber"},
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
//        GeneralHelper::checkPermission();

        $model = new CreateCarInspectionForm();
        $model->setAttributes(array_merge($this->post, [
            'partner_id' => \Yii::$app->user->identity->partner_id
        ]));
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}