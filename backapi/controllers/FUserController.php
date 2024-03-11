<?php
namespace backapi\controllers;

use backapi\models\forms\fUserForms\GetById;
use backapi\models\forms\fUserForms\ProductCountsByPolicyEndDate;
use backapi\models\forms\fUserForms\SendSmsByPolicyEndDateForm;
use backapi\models\forms\fUserForms\SendSmsOrTelegramMessage;
use backapi\models\forms\fUserForms\SendUniqueuLinkForm;
use backapi\models\forms\fUserForms\Statistics;
use backapi\models\searchs\FUserByProductSearch;
use backapi\models\searchs\FUserProductsSearch;
use backapi\models\searchs\FUserSearch;
use backapi\models\searchs\ProductsByPolicyEndDateSearch;
use common\helpers\GeneralHelper;

use common\models\Product;
use common\models\User;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class FUserController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'send-sms-or-telegram-message' => ['POST'],
                'send-sms-by-policy-end-date' => ['POST'],
//                'delete' => ['DELETE'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/f-user/statistics",
     *     summary="Method to get all statistic info which in marketing(Маркетинг) page",
     *     tags={"FUserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[from_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[till_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Response(
     *         response="200", description="all statistic info",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="user_count", type="integer", example=540),
     *              @OA\Property(property="average_age", type="integer", example=142),
     *              @OA\Property(property="female", type="integer", example=12),
     *              @OA\Property(property="male", type="integer", example=12),
     *              @OA\Property(property="average_policy_amount_uzs", type="integer", example=12),
     *              @OA\Property(property="average_check", type="integer", example=12),
     *              @OA\Property(property="new_add_users", type="integer", example=12, description="yuborilgan intervalda register bo'lgan userlar"),
     *              @OA\Property(property="new_add_and_payed_users", type="integer", example=12, description="yuborilgan intervalda register bo'lgan va bitta bo'lsa ham product sotib olgan userlar"),
     *              @OA\Property(property="conversion", type="integer", example=12, description="new_add_and_payed_users*100/new_add_users"),
     *              @OA\Property(property="top_regions", type="array", @OA\Items(type="object",
     *                  @OA\Property(property="region", type="string", example="01", description="01 bo'lsa toshkent deb chiqarish kerak, 80 bo'lsa buxoro"),
     *                  @OA\Property(property="count", type="integer", example="30"),
     *              )),
     *              @OA\Property(property="logged_in_users_count", type="integer", example=12, description="how many users logged in in given period"),
     *              @OA\Property(property="products_passed_payment_page", type="integer", example=12, description="how many products passed from payment page in given period, counted unique autonumbers"),
     *              @OA\Property(property="payed_products", type="integer", example=12, description="how many products payed in given period"),
     *              @OA\Property(property="users_bought_more_than_2", type="integer", example=12, description="how many users bought more than 1 product which is blongs to same type(kasko, osago, travel)"),
     *              @OA\Property(property="telegram_users_count", type="integer", example=12, description="how many users have telegram"),
     *              @OA\Property(property="new_added_telegram_users", type="integer", example=12, description="how many users which have telegram registered in given period"),
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStatistics()
    {
        GeneralHelper::checkPermission();

        $model = new Statistics();
        $model->setAttributes($this->get['filter'] ?? []);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/f-user/get-by-id",
     *     summary="Method to get one user info which in marketing(Маркетинг) page",
     *     tags={"FUserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="user info",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="id", type="integer", example="12345"),
     *              @OA\Property(property="name", type="string|null", example="Jobir Yusupov"),
     *              @OA\Property(property="phone", type="string", example="998946464400"),
     *              @OA\Property(property="created_at", type="string", example="07.04.2022"),
     *              @OA\Property(property="age", type="integer|null", example=28),
     *              @OA\Property(property="gender", type="integer|null", example=null),
     *              @OA\Property(property="policy_count", type="integer", example=5),
     *              @OA\Property(property="policy_amount_uzs", type="integer", example=24363000),
     *              @OA\Property(property="autos", type="string", example="Chevrolet Malibu 2 2,5л. LT "),
     *              @OA\Property(property="countries", type="integer", example="Польша"),
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById()
    {
        GeneralHelper::checkPermission();

        $model = new GetById();
        $model->setAttributes($this->get ?? []);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/f-user/all",
     *     summary="Method to get all users with or without pagination ",
     *     tags={"FUserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search by name and phone number"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name, last_payed_date. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="users with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/f_user_in_marketing")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="autobmodel without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/f_user_in_marketing")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new FUserSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => $dataProvider->getModels(),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/f-user/get-products",
     *     summary="Method to get products of user which in marketing(Маркетинг) page",
     *     tags={"FUserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="products with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/product")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetProducts()
    {
        GeneralHelper::checkPermission();

        $searchModel = new FUserProductsSearch();
        $dataProvider = $searchModel->search($this->get ?? []);

        return [
            'models' => $dataProvider->getModels(),
            'pages' => $dataProvider->getPagination(),
        ];
    }

    public function findById($id)
    {
        if (!is_null($id) and is_numeric($id) and (int)$id == $id
            and $user = User::find()
                ->where(['not', ['status' => User::STATUS_DELETED]])
                ->andWhere(['role' => User::ROLES['user'], 'id' => $id])->one()
        )
            return $user;

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Post(
     *     path="/f-user/send-sms-or-telegram-message",
     *     summary="telegramga yoki telegram bloklangan bo'lsa klient telefoniga sms yuboradi",
     *     tags={"FUserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"message"},
     *                 @OA\Property (property="user_id", type="integer", example=2),
     *                 @OA\Property (property="phone", type="string", example="998946464400"),
     *                 @OA\Property (property="message", type="string", example="Bizni tanlaganingiz uchun raxmat"),
     *                 @OA\Property (property="only_by_sms", type="integer", example="0 yoki 1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="true",
     *          @OA\JsonContent( type="boolean", example="true")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSendSmsOrTelegramMessage()
    {
        GeneralHelper::checkPermission();

        $model = new SendSmsOrTelegramMessage();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/f-user/product-counts-by-policy-end-date",
     *     summary="Method to get product counts which is ending given period",
     *     tags={"FUserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[from_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[till_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Response(
     *         response="200", description="all statistic info",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="sb", type="object",
     *                  @OA\Property(property="osago", type="object",
     *                      @OA\Property(property="all", type="integer", example="100"),
     *                      @OA\Property(property="already_bought", type="integer", example="30"),
     *                  ),
     *                  @OA\Property(property="kasko", type="object",
     *                      @OA\Property(property="all", type="integer", example="100"),
     *                      @OA\Property(property="already_bought", type="integer", example="30"),
     *                  ),
     *              ),
     *              @OA\Property(property="stranger", type="object",
     *                  @OA\Property(property="osago", type="object",
     *                      @OA\Property(property="all", type="integer", example="100"),
     *                      @OA\Property(property="already_bought", type="integer", example="30"),
     *                  ),
     *                  @OA\Property(property="kasko", type="object",
     *                      @OA\Property(property="all", type="integer", example="100"),
     *                      @OA\Property(property="already_bought", type="integer", example="30"),
     *                  ),
     *              ),
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionProductCountsByPolicyEndDate()
    {
        GeneralHelper::checkPermission();

        $model = new ProductCountsByPolicyEndDate();
        $model->setAttributes($this->get['filter'] ?? []);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/f-user/send-sms-by-policy-end-date",
     *     summary="Method to send sms users which is policy ending given period",
     *     tags={"FUserController"},
     *     security={ {"bearerAuth": {} } },
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"message"},
     *                 @OA\Property (property="from_date", type="string", example="2023-03-24"),
     *                 @OA\Property (property="till_date", type="string", example="2023-03-24"),
     *                 @OA\Property (property="product", type="integer", example="1", description="osago => 1, kasko => 2"),
     *                 @OA\Property (property="type", type="integer", example="1", description="sb => 0, stranger => 1"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", description="all statistic info",
     *         @OA\JsonContent(type="boolean", example="true")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSendSmsByPolicyEndDate()
    {
        GeneralHelper::checkPermission();

        $model = new SendSmsByPolicyEndDateForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/f-user/users-by-policy-end-date",
     *     summary="Method to get users which is policy ending given period",
     *     tags={"FUserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[from_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[till_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[product]", in="query", @OA\Schema (type="integer"), description="osago => 1, kasko => 2"),
     *     @OA\Parameter (name="filter[type]", in="query", @OA\Schema (type="integer"), description="sb => 0, stranger => 1"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="all users",
     *         @OA\JsonContent(type="boolean", example="true")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUsersByPolicyEndDate()
    {
        GeneralHelper::checkPermission();

        $searchModel = new FUserByProductSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        if (is_array($dataProvider))
            return $dataProvider;

        return [
            'models' => $dataProvider->getModels(),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/f-user/products-by-policy-end-date",
     *     summary="Method to get products which is policy ending given period",
     *     tags={"FUserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[from_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[till_date]", in="query", @OA\Schema (type="string"), description="2023-03-24"),
     *     @OA\Parameter (name="filter[product]", in="query", @OA\Schema (type="integer"), description="osago => 1, kasko => 2"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="all products",
     *         @OA\JsonContent(type="object", example="")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionProductsByPolicyEndDate()
    {
        GeneralHelper::checkPermission();

        $searchModel = new ProductsByPolicyEndDateSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        if (is_array($dataProvider))
            return $dataProvider;

        return [
            'models' => Product::getEndDateCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Post(
     *     path="/f-user/send-unique-link",
     *     summary="sms orqali skidkali link yuborish",
     *     tags={"FUserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"amount", "amount_type"},
     *                 @OA\Property (property="user_id", type="integer", description="id of user"),
     *                 @OA\Property (property="phone", type="string", description="phone of user", example="998946464400"),
     *                 @OA\Property (property="amount", type="integer", description="amount of money or percent"),
     *                 @OA\Property (property="amount_type", type="integer", description="1 => fixed, 0 => percent"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="true",
     *          @OA\JsonContent( type="boolean", example="true")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSendUniqueLink()
    {
        GeneralHelper::checkPermission();

        $model = new SendUniqueuLinkForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}