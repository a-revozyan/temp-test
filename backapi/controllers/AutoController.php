<?php
namespace backapi\controllers;

use backapi\models\forms\autocompForms\AutocompAttachPartnerForm;
use backapi\models\forms\autocompForms\CreateAutocompForm;
use backapi\models\forms\autocompForms\UpdateAutocompForm;
use backapi\models\searchs\AutoCompSearch;
use common\helpers\GeneralHelper;
use common\models\Autocomp;
use XLSXWriter;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class AutoController extends BaseController
{
    public $row_respons_action_ids = ["auto-export"];
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['POST'],
                'attach-partner' => ['PUT'],
                'update' => ['PUT'],
                'delete' => ['DELETE'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/auto/all",
     *     summary="Method to get all autos with or without pagination ",
     *     tags={"AutoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from automodel name, auto brand name, auto komplektatsiya name, production_year, price, id of auto komplektatsiya"),
     *     @OA\Parameter (name="filter[autobrand_id]", in="query", @OA\Schema (type="integer"), description="send id of autobrand to get autos which are realted to the autobrand"),
     *     @OA\Parameter (name="filter[automodel_id]", in="query", @OA\Schema (type="integer"), description="send id of automodel to get autos which are realted to the automodel"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="1 => acktive, 0 => inactive"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, automodel_id, automodel.autobrand_id, name, production_year, price, status. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="autocomp with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/auto")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="autocomp without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/auto")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new AutoCompSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Autocomp::getWithParentCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/auto/auto-export",
     *     summary="Method to get all autos with or without pagination ",
     *     tags={"AutoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from automodel name, auto brand name, auto komplektatsiya name, production_year, price, id of auto komplektatsiya"),
     *     @OA\Parameter (name="filter[autobrand_id]", in="query", @OA\Schema (type="integer"), description="send id of autobrand to get autos which are realted to the autobrand"),
     *     @OA\Parameter (name="filter[automodel_id]", in="query", @OA\Schema (type="integer"), description="send id of automodel to get autos which are realted to the automodel"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="1 => acktive, 0 => inactive"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, automodel_id, automodel.autobrand_id, name, production_year, price, status. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="excel string",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAutoExport()
    {
        GeneralHelper::checkPermission();

        return GeneralHelper::export(Autocomp::className(), AutoCompSearch::className(), [
            'id' => 'integer',
            'brand_name' => 'string',
            'model_name' => 'string',
            'name' => 'string',
            'production_year' => 'integer',
            'price' => 'integer',
        ], [
            'id',
            'brand_name' => function($model){
                return $model->automodel->autobrand->name;
            },
            'model_name' => function($model){
                return $model->automodel->name;
            },
            'name',
            'production_year',
            'price'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/auto/create",
     *     summary="create new auto",
     *     tags={"AutoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "autobrand_name", "automodel_name", "production_year", "price"},
     *                 @OA\Property (property="autobrand_name", type="string", example="autobrand name", description="new or existing autobrand name"),
     *                 @OA\Property (property="automodel_name", type="string", example="automodel name", description="new or existing automodel name"),
     *                 @OA\Property (property="name", type="string", example="new model name"),
     *                 @OA\Property (property="production_year", type="iteger", example=2019),
     *                 @OA\Property (property="price", type="iteger", example=20000),
     *                 @OA\Property (property="status", type="iteger", example=1, description="1 => active, 0 => inactive"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created auto",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/auto")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateAutocompForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getWithParentArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/auto/attach-partner",
     *     summary="attach partner to autocomp",
     *     tags={"AutoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"autocomp_id"},
     *                 @OA\Property (property="autocomp_id", type="integer", example=2),
     *                 @OA\Property (property="partner_ids", type="array", @OA\Items(type="integer", example=2)),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated auto",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/auto")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAttachPartner()
    {
        GeneralHelper::checkPermission();

        $model = new AutocompAttachPartnerForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getWithParentArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/auto/update",
     *     summary="update autocomp",
     *     tags={"AutoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "name", "autobrand_name", "automodel_name", "production_year", "price"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="name", type="integer", example="autocomp name 4-pazitsiya"),
     *                 @OA\Property (property="autobrand_name", type="string", example="autobrand name", description="new or existing autobrand name"),
     *                 @OA\Property (property="automodel_name", type="string", example="automodel name", description="new or existing automodel name"),
     *                 @OA\Property (property="production_year", type="integer", example=2022),
     *                 @OA\Property (property="price", type="integer", example=5800000),
     *                 @OA\Property (property="status", type="iteger", example=1, description="1 => active, 0 => inactive"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated auto",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/auto")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateAutocompForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getWithParentArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/auto/get-by-id",
     *     summary="get autocomp by id",
     *     tags={"AutoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="auto",
     *         @OA\JsonContent(type="object", ref="#components/schemas/auto")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id)
            return Autocomp::findOne($id)->getWithParentArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/auto/delete",
     *     summary="Method to delete auto by id",
     *     tags={"AutoController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $autocomp = Autocomp::findOne($id))
        {
            if (!empty($autocomp->kaskos))
                throw new BadRequestHttpException(Yii::t('app', 'There are some kaskos of this Autocomp'));
            return $autocomp->delete();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }
}
