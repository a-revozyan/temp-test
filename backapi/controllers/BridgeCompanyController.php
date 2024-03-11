<?php
namespace backapi\controllers;

use backapi\models\forms\bridgeCompanyForms\CreateUpdateBridgeCompanyDivvyForm;
use backapi\models\forms\bridgeCompanyForms\CreateUpdateBridgeCompanyForm;
use backapi\models\searchs\BridgeCompanySearch;
use common\helpers\GeneralHelper;
use common\models\BridgeCompany;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class BridgeCompanyController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['POST'],
                'update' => ['PUT'],
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
     *     path="/bridge-company/all",
     *     summary="Method to get all bridge companies with or without pagination ",
     *     tags={"BridgeCompanyController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from name, code"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="0, 10"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: 'id', 'name', 'code', 'created_at', 'updated_at', 'status'. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="bridge companies with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/bridge_company")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="bridge companies without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/bridge_company")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new BridgeCompanySearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => BridgeCompany::getShortArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/bridge-company/export",
     *     summary="Method to get all bridge companies excel",
     *     tags={"BridgeCompanyController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from name, code"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="0, 10"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: 'id', 'name', 'code', 'created_at', 'updated_at', 'status'. use '-' for descending"),
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

        $searchModel = new BridgeCompanySearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        $models = $dataProvider->getModels();
        $models = ArrayHelper::toArray($models, [
            BridgeCompany::className() => [
                'id',
                'name',
                'code',
                'created_at' => function($model){
                    return date('Y/m/d', $model->created_at);
                },
                'updated_at' => function($model){
                    return date('Y/m/d', $model->updated_at);
                },
                'status',
            ]
        ]);

        $writer = new \XLSXWriter();
        $writer->writeSheet($models,'bridge company',
            [
                'id' => 'integer',
                'name' => 'string',
                'code' => 'string',
                'created_at' => 'string',
                'updated_at' => 'string',
                'status' => 'string',
            ]);

        return $writer->writeToString();
    }

    /**
     * @OA\Post(
     *     path="/bridge-company/create",
     *     summary="create new bridge company",
     *     tags={"BridgeCompanyController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"username", "email", "status", "name", "code", "password"},
     *                 @OA\Property (property="name", type="string", example="Bridge company name"),
     *                 @OA\Property (property="code", type="string", example="Bridge company code"),
     *                 @OA\Property (property="username", type="string", example="username12", description="Bridge company username which is used for login"),
     *                 @OA\Property (property="email", type="string", example="company@gmail.com", description="Bridge company  email"),
     *                 @OA\Property (property="status", type="iteger", example=10, description="Bridge company status 0 or 10"),
     *                 @OA\Property (property="password", type="string", example="password", description="Bridge company password"),
     *                 @OA\Property (property="phone_number", type="string", example="998946464400", description="phone number of Bridge company"),
     *                 @OA\Property (property="last_name", type="string", example="Yusupov", description="last name of bridge company representative"),
     *                 @OA\Property (property="first_name", type="string", example="Jobir", description="first name of bridge company representative"),
     *                 @OA\Property (property="success_webhook_url", type="string", example="https://asdf/asdf.asd", description="bridge company ni ping qilamiz"),
     *                 @OA\Property (property="error_webhook_url", type="string", example="https://asdf/asdf.asd", description="bridge company ni ping qilamiz"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created company",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/bridge_company")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateUpdateBridgeCompanyForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getShortArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/bridge-company/update",
     *     summary="create new bridge company",
     *     tags={"BridgeCompanyController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "username", "email", "status", "name", "code"},
     *                 @OA\Property (property="id", type="integer", example=14),
     *                 @OA\Property (property="name", type="string", example="Bridge company name"),
     *                 @OA\Property (property="code", type="string", example="Bridge company code"),
     *                 @OA\Property (property="username", type="string", example="username12", description="Bridge company username which is used for login"),
     *                 @OA\Property (property="email", type="string", example="company@gmail.com", description="Bridge company  email"),
     *                 @OA\Property (property="status", type="iteger", example=10, description="Bridge company status 0 or 10"),
     *                 @OA\Property (property="password", type="string", example="password", description="Bridge company password"),
     *                 @OA\Property (property="phone_number", type="string", example="998946464400", description="phone number of Bridge company"),
     *                 @OA\Property (property="last_name", type="string", example="Yusupov", description="last name of bridge company representative"),
     *                 @OA\Property (property="first_name", type="string", example="Jobir", description="first name of bridge company representative"),
     *                 @OA\Property (property="success_webhook_url", type="string", example="https://asdf/asdf.asd", description="bridge company ni ping qilamiz"),
     *                 @OA\Property (property="error_webhook_url", type="string", example="https://asdf/asdf.asd", description="bridge company ni ping qilamiz"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created company",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/bridge_company")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateUpdateBridgeCompanyForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getShortArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/bridge-company/get-by-id",
     *     summary="get bridge company by id",
     *     tags={"BridgeCompanyController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="bridge company",
     *         @OA\JsonContent(type="object", ref="#components/schemas/bridge_company_with_divvies")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $company = BridgeCompany::find()
                ->with(['user', 'monthlyDivvies.partner', 'monthlyDivvies.bridgeCompany', 'monthlyDivvies.product',
                    'monthlyDivvies.numberDrivers'])
                ->where(['id' => $id])->one())
        {
            return $company->getShortWithDivvyArr();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/bridge-company/delete",
     *     summary="Method to delete bridge company by id",
     *     tags={"BridgeCompanyController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $bridge_company = BridgeCompany::findOne($id))
        {
            if (!empty($bridge_company->kaskos))
                throw new BadRequestHttpException(Yii::t('app', 'There are some Kaskos connected by this Bridge Company'));
            return $bridge_company->delete();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Put(
     *     path="/bridge-company/update-divvy",
     *     summary="update divvy of bridge company by month",
     *     tags={"BridgeCompanyController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"bridge_company_id", "partner_id", "product_id", "month", "percent"},
     *                 @OA\Property (property="bridge_company_id", type="integer", example=14),
     *                 @OA\Property (property="partner_id", type="integer", example="1"),
     *                 @OA\Property (property="product_id", type="integer", example="1", description="1 => osago, 4 => accident"),
     *                 @OA\Property (property="number_drivers_id", type="integer", example="1", description="1 => neogranichaniya, 4 => 5 kishigacha"),
     *                 @OA\Property (property="month", type="string", example="2023-12"),
     *                 @OA\Property (property="percent", type="float", example=10.22),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created divvy",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/bridge_company_divvy")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdateDivvy()
    {
        GeneralHelper::checkPermission();

        $model = new CreateUpdateBridgeCompanyDivvyForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getShortArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }


}