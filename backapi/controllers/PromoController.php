<?php
namespace backapi\controllers;

use backapi\models\forms\promoForms\UpdatePromoForm;
use backapi\models\forms\promoForms\CreatePromoForm;
use backapi\models\searchs\PromoSearch;
use common\helpers\GeneralHelper;
use common\models\Promo;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class PromoController extends BaseController
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

    /**
     * @OA\Get(
     *     path="/promo/all",
     *     summary="Method to get all promos with or without pagination",
     *     tags={"PromoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from code, amount, id of promo"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, code, amount, begin_date, end_date, amount_type, status, number. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="promo with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="promos", type="array",  @OA\Items(type="object", ref="#/components/schemas/promo")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="promo without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="promos", type="array",  @OA\Items(type="object", ref="#/components/schemas/promo")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new PromoSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'promos' => Promo::getFullArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/promo/export",
     *     summary="Method to get all promos excel",
     *     tags={"PromoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from code, amount, id of promo"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, code, amount, begin_date, end_date, amount_type, status, number. use '-' for descending"),
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

        return GeneralHelper::export(Promo::className(), PromoSearch::className(), [
            'id' => 'integer',
            'code' => 'string',
            'amount' => 'integer',
            'begin_date' => 'string',
            'end_date' => 'string',
            'amount_type' => 'integer',
            'status' => 'string',
            'number' => 'integer',
        ], [
            'id',
            'code',
            'amount',
            'begin_date' => function($promo){
                return is_null($promo->begin_date) ? "" : date('d.m.Y', strtotime($promo->begin_date));
            },
            'end_date' => function($promo){
                return is_null($promo->end_date) ? "" : date('d.m.Y', strtotime($promo->end_date));
            },
            'amount_type',
            'status',
            'number',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/promo/create",
     *     summary="create new promo",
     *     tags={"PromoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"code", "amount", "amount_type", "status", "number"},
     *                 @OA\Property (property="code", type="string", example="tirikchilik", description="promo code"),
     *                 @OA\Property (property="amount", type="integer", example="-5", description="type ga qarab foiz yoki fixed pull, doim manfiy bo'lishi kerak"),
     *                 @OA\Property (property="begin_date", type="string", example="29.01.2021", description="formt: d.m.Y"),
     *                 @OA\Property (property="end_date", type="string", example="29.01.2022", description="formt: d.m.Y"),
     *                 @OA\Property (property="status", type="integer", example="1", description="active => 1, inactive => 0"),
     *                 @OA\Property (property="number", type="integer", example="100", description="promocode soni"),
     *                 @OA\Property (property="amount_type", type="integer", example="1", description="'percent' => 0, 'fixed' => 1"),
     *                 @OA\Property (property="product_ids", type="array", @OA\Items(type="integer", example=2), description="osago => 1, kasko => 2, travel => 3, accident => 4, kasko-by-subscription => 5"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created promo",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/promo")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $promo = new CreatePromoForm();
        $promo->setAttributes($this->post);
        if ($promo->validate())
            return $promo->save()->getFullArr();

        return $this->sendFailedResponse($promo->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/promo/update",
     *     summary="udpate promo",
     *     tags={"PromoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "code", "amount", "amount_type"},
     *                 @OA\Property (property="id", type="integer", example="12"),
     *                 @OA\Property (property="code", type="string", example="tirikchilik", description="promo code"),
     *                 @OA\Property (property="amount", type="integer", example="-5", description="type ga qarab foiz yoki fixed pull, doim manfiy bo'lishi kerak"),
     *                 @OA\Property (property="begin_date", type="string", example="29.01.2021", description="formt: d.m.Y"),
     *                 @OA\Property (property="end_date", type="string", example="29.01.2022", description="formt: d.m.Y"),
     *                 @OA\Property (property="status", type="integer", example="1", description="active => 1, inactive => 0"),
     *                 @OA\Property (property="number", type="integer", example="100", description="promocode soni"),
     *                 @OA\Property (property="amount_type", type="integer", example="1", description="'percent' => 0, 'fixed' => 1"),
     *                 @OA\Property (property="product_ids", type="array", @OA\Items(type="integer", example=2), description="osago => 1, kasko => 2, travel => 3, accident => 4, kasko-by-subscription => 5"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated promo",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/promo")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $promo = new UpdatePromoForm();
        $promo->setAttributes($this->put);
        if ($promo->validate())
            return $promo->save()->getFullArr();

        return $this->sendFailedResponse($promo->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/promo/get-by-id",
     *     summary="get promo by id",
     *     tags={"PromoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="promo",
     *         @OA\JsonContent(type="object", ref="#components/schemas/promo")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $promo = Promo::findOne($id))
            return $promo->getFullArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/promo/delete",
     *     summary="Method to delete promo by id",
     *     tags={"PromoController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $promo = Promo::findOne($id))
            return $promo->delete();

        throw new BadRequestHttpException("ID is incorrect");
    }
}