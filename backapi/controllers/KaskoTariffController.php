<?php
namespace backapi\controllers;

use backapi\models\forms\kaskorisk\CreateKaskoRiskForm;
use backapi\models\forms\kaskorisk\UpdateKaskoRiskForm;
use backapi\models\forms\kaskoTariffForms\AttachAutoRiskTypesToKaskoTariffForm;
use backapi\models\forms\kaskoTariffForms\AttachCarAccessoriesToKaskoTariffForm;
use backapi\models\forms\kaskoTariffForms\AttachIslomicAmountForm;
use backapi\models\forms\kaskoTariffForms\AttachRisksToKaskoTariffForm;
use backapi\models\forms\kaskoTariffForms\CreateKaskoTariffForm;
use backapi\models\forms\kaskoTariffForms\UpdateKaskoTariffForm;
use backapi\models\searchs\KaskoTariffSearch;
use common\helpers\GeneralHelper;
use common\models\KaskoTariff;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use function GuzzleHttp\Promise\all;

class KaskoTariffController extends BaseController
{
    public $row_respons_action_ids = ["kasko-tariff-export"];
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['POST'],
                'attach-risks' => ['PUT'],
                'attach-car-accessories' => ['PUT'],
                'attach-auto-risk-types' => ['PUT'],
                'attach-islomic-amount' => ['POST'],
                'update' => ['POST'],
                'delete' => ['DELETE'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/kasko-tariff/all",
     *     summary="Method to get all kasko tariffs with or without pagination ",
     *     tags={"KaskoTariffController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[partner_name]", in="query", @OA\Schema (type="string"), description="search from name of partner"),
     *     @OA\Parameter (name="filter[partner_id]", in="query", @OA\Schema (type="integer"), description="filter by id of partner"),
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from name of tariff"),
     *     @OA\Parameter (name="filter[risk_count]", in="query", @OA\Schema (type="integer"), description="filter by risks count"),
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search form partner name and tariff name"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: name, partner_name, risks_count. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="kasko tariff with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object",
     *                  @OA\Property (property="id", type="integer", example=4),
     *                  @OA\Property (property="name", type="string", example="ALFA KASKO "),
     *                  @OA\Property (property="partner_name", type="string", example="ALFA Invest"),
     *                  @OA\Property (property="partner_id", type="integer", example=10),
     *                  @OA\Property (property="risk_count", type="integer", example=5),
     *              )),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="kasko tariff without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object",
     *                  @OA\Property (property="id", type="integer", example=4),
     *                  @OA\Property (property="name", type="string", example="ALFA KASKO "),
     *                  @OA\Property (property="partner_name", type="string", example="ALFA Invest"),
     *                  @OA\Property (property="partner_id", type="integer", example=10),
     *                  @OA\Property (property="risk_count", type="integer", example=5),
     *                  @OA\Property (property="amount", type="float", example=5.2),
     *                  @OA\Property (property="min_price", type="integer", example=1000),
     *                  @OA\Property (property="max_price", type="integer", example=2000),
     *                  @OA\Property (property="min_year", type="integer", example=2012),
     *                  @OA\Property (property="max_year", type="integer", example=2022),
     *              )),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new KaskoTariffSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => $dataProvider->getModels(),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/kasko-tariff/kasko-tariff-export",
     *     summary="Method to get all kasko tariffs excel",
     *     tags={"KaskoTariffController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[partner_name]", in="query", @OA\Schema (type="string"), description="search from name of partner"),
     *     @OA\Parameter (name="filter[partner_id]", in="query", @OA\Schema (type="integer"), description="filter by id of partner"),
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from name of tariff"),
     *     @OA\Parameter (name="filter[risk_count]", in="query", @OA\Schema (type="integer"), description="filter by risks count"),
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search form partner name and tariff name"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: name, partner_name, risks_count. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="excel string",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionKaskoTariffExport()
    {
        GeneralHelper::checkPermission();

        return GeneralHelper::export(KaskoTariff::className(), KaskoTariffSearch::className(), [
            'id' => 'integer',
            'partner_name' => 'string',
            'name' => 'string',
            'partner_id' => 'integer',
            'risks_count' => 'integer',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/kasko-tariff/create",
     *     summary="create new kasko tariff",
     *     tags={"KaskoTariffController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"partner_id", "name", "is_conditional"},
     *                 @OA\Property (property="amount", type="float", example=2.1, description="is_islomic = 0 bo'lsa required"),
     *                 @OA\Property (property="name", type="string", example="new tariff"),
     *                 @OA\Property (property="franchise_ru", type="string", example="test ru"),
     *                 @OA\Property (property="franchise_en", type="string", example="test en"),
     *                 @OA\Property (property="franchise_uz", type="string", example="test uz"),
     *                 @OA\Property (property="only_first_risk_ru", type="string", example="test ru first risk"),
     *                 @OA\Property (property="only_first_risk_en", type="string", example="test en first risk"),
     *                 @OA\Property (property="only_first_risk_uz", type="string", example="test uz first risk"),
     *                 @OA\Property (property="partner_id", type="iteger", example=12),
     *                 @OA\Property (property="is_conditional", type="iteger", example=1, description="0 or 1"),
     *                 @OA\Property (property="file", type="file"),
     *                 @OA\Property (property="is_islomic", type="iteger", example=1, description="0 or 1"),
     *                 @OA\Property (property="min_price", type="iteger", example=123),
     *                 @OA\Property (property="max_price", type="iteger", example=456),
     *                 @OA\Property (property="min_year", type="iteger", example=2009),
     *                 @OA\Property (property="max_year", type="iteger", example=2022),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created kasko risk",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/kasko_tariff")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateKaskoTariffForm();
        $this->post['file'] = UploadedFile::getInstanceByName('file');

        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/kasko-tariff/attach-risks",
     *     summary="attach risks to kasko tariff",
     *     tags={"KaskoTariffController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"tariff_id"},
     *                 @OA\Property (property="tariff_id", type="integer", example=28),
     *                 @OA\Property (property="risk_ids", type="array", @OA\Items(type="integer", example=2)),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created kasko risk",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/kasko_tariff")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAttachRisks()
    {
        GeneralHelper::checkPermission();

        $model = new AttachRisksToKaskoTariffForm();

        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/kasko-tariff/attach-auto-risk-types",
     *     summary="attach auto type risk to kasko tariff",
     *     tags={"KaskoTariffController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"tariff_id"},
     *                 @OA\Property (property="tariff_id", type="integer", example=28),
     *                 @OA\Property (property="auto_risk_type_ids", type="array", @OA\Items(type="integer", example=8)),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created kasko risk",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/kasko_tariff")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAttachAutoRiskTypes()
    {
        GeneralHelper::checkPermission();

        $model = new AttachAutoRiskTypesToKaskoTariffForm();

        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/kasko-tariff/attach-car-accessories",
     *     summary="attach car accessories to kasko tariff",
     *     tags={"KaskoTariffController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"tariff_id"},
     *                 @OA\Property (property="tariff_id", type="integer", example=28, description="is_islomic = 0 bo'lgan tariff ning id sini yuborish kerak"),
     *                 @OA\Property (property="car_accessory_ids", type="array", @OA\Items(type="integer", example=1)),
     *                 @OA\Property (property="coeffs", type="array", @OA\Items(type="integer", example=2),
     *                      description="har bir aksessuarning index iga mos koef yuboirsh kerak.
     *                      0-index dagi coef 0-indexdagi aksessuarga tegishli, 1-indexdagisi 1-indexdagi aksessuarga tegishli"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created kasko risk",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/kasko_tariff")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAttachCarAccessories()
    {
        GeneralHelper::checkPermission();

        $model = new AttachCarAccessoriesToKaskoTariffForm();

        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/kasko-tariff/update",
     *     summary="update existing kasko tariff",
     *     tags={"KaskoTariffController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"tariff_id", "partner_id", "name", "is_conditional"},
     *                 @OA\Property (property="tariff_id", type="integer", example=28),
     *                 @OA\Property (property="amount", type="float", example=2.1, description="is_islomic = 0 bo'lsa required"),
     *                 @OA\Property (property="name", type="string", example="new tariff"),
     *                 @OA\Property (property="franchise_ru", type="string", example="test ru"),
     *                 @OA\Property (property="franchise_en", type="string", example="test en"),
     *                 @OA\Property (property="franchise_uz", type="string", example="test uz"),
     *                 @OA\Property (property="only_first_risk_ru", type="string", example="test ru first risk"),
     *                 @OA\Property (property="only_first_risk_en", type="string", example="test en first risk"),
     *                 @OA\Property (property="only_first_risk_uz", type="string", example="test uz first risk"),
     *                 @OA\Property (property="partner_id", type="iteger", example=12),
     *                 @OA\Property (property="is_conditional", type="iteger", example=1, description="0 or 1"),
     *                 @OA\Property (property="file", type="file"),
     *                 @OA\Property (property="is_islomic", type="iteger", example=1, description="0 or 1"),
     *                 @OA\Property (property="min_price", type="iteger", example=123),
     *                 @OA\Property (property="max_price", type="iteger", example=456),
     *                 @OA\Property (property="min_year", type="iteger", example=2009),
     *                 @OA\Property (property="max_year", type="iteger", example=2022),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created kasko risk",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/kasko_tariff")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateKaskoTariffForm();
        $this->post['file'] = UploadedFile::getInstanceByName('file');

        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post (
     *     path="/kasko-tariff/attach-islomic-amount",
     *     summary="attach islomic amounts to kasko tariff",
     *     tags={"KaskoTariffController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"tariff_id"},
     *                 @OA\Property (property="tariff_id", type="integer", example=28, description="is_islomic = 1 bo'lgan tariff ning id sini yuborish kerak"),
     *                 @OA\Property (property="tariff_islomic_amounts[]", type="array", @OA\Items(type="integer", example=2),
     *                      description="arrayning indexi auto_risk_type ning id si bo'lishi kerak. qiymati amount bo'lishi kerak
     *                          masalan tariff_islomic_amounts[18] = 3, bu yerda 18 auto_risk_type_id, 3 amount
     *                      "),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created kasko risk",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/kasko_tariff")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAttachIslomicAmount()
    {
        GeneralHelper::checkPermission();

        $model = new AttachIslomicAmountForm();

        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/kasko-tariff/get-by-id",
     *     summary="get kasko tariff by id",
     *     tags={"KaskoTariffController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="kasko tariff",
     *         @OA\JsonContent(type="object", ref="#components/schemas/kasko-tariff")
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
            $tariff = KaskoTariff::findOne($id);
            if (empty($tariff))
                throw new NotFoundHttpException();
            return $tariff->getFullArr();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/kasko-tariff/delete",
     *     summary="Method to delete kasko tariff by id",
     *     tags={"KaskoTariffController"},
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

        if (!(!is_null($id) and is_numeric($id) and (int)$id == $id and $kaskoTariff = KaskoTariff::findOne($id)))
            throw new BadRequestHttpException("ID is incorrect");

        if (!empty($kaskoTariff->kaskoRisks))
            throw new BadRequestHttpException("This risk is associated with some tariffs");

        return $kaskoTariff->delete();
    }
}