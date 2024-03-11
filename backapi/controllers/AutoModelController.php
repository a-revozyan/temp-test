<?php
namespace backapi\controllers;

use backapi\models\forms\automodelForms\CreateAutomodelForm;
use backapi\models\forms\automodelForms\UpdateAutomodelForm;
use backapi\models\searchs\AutoModelSearch;
use common\helpers\GeneralHelper;
use common\models\Automodel;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class AutoModelController extends BaseController
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
     *     path="/auto-model/all",
     *     summary="Method to get all automodels with or without pagination ",
     *     tags={"AutoModelController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from names of automodel and auto brand"),
     *     @OA\Parameter (name="filter[autobrand_id]", in="query", @OA\Schema (type="integer"), description="send id of autobrand to get automodels which are realted to the autobrand"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="1 => acktive, 0 => inactive"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name, order, status. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="automodel with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/automodel")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="autobmodel without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/automodel")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new AutoModelSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Automodel::getFullWithParentCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/auto-model/export",
     *     summary="Method to get all automodels excel",
     *     tags={"AutoModelController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from names of automodel and auto brand"),
     *     @OA\Parameter (name="filter[autobrand_id]", in="query", @OA\Schema (type="integer"), description="send id of autobrand to get automodels which are realted to the autobrand"),
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
        GeneralHelper::checkPermission();

        return GeneralHelper::export(Automodel::className(), AutoModelSearch::className(), [
            'id' => 'integer',
            'name' => 'string',
            'brand_name' => 'string',
            'order' => 'integer',
            'auto_risk_type_name' => 'string',
        ], [
            'id',
            'name',
            'brand_name' => function($model){
                return $model->autobrand->name ?? "";
            },
            "order",
            'auto_risk_type_name' => function($model){
                return $model->autoRiskType->name ?? "";
            },
        ]);
    }

    /**
     * @OA\Post(
     *     path="/auto-model/create",
     *     summary="create new automodel",
     *     tags={"AutoModelController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "autobrand_name"},
     *                 @OA\Property (property="autobrand_name", type="string", example="autobrand name", description="new or existing autobrand name"),
     *                 @OA\Property (property="name", type="string", example="new model name"),
     *                 @OA\Property (property="order", type="iteger", example=2),
     *                 @OA\Property (property="status", type="iteger", example=1, description="1 => active, 0 => inactive"),
     *                 @OA\Property (property="auto_risk_type_id", type="iteger", example=2, description="define this model is elektrocar, import or local ... "),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created automodel",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/automodel")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateAutomodelForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullWithParent();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/auto-model/update",
     *     summary="udpate automodel",
     *     tags={"AutoModelController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "name", "autobrand_name"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="autobrand_name", type="string", example="autobrand name", description="new or existing autobrand name"),
     *                 @OA\Property (property="name", type="string", example="new model name"),
     *                 @OA\Property (property="order", type="iteger", example=2),
     *                 @OA\Property (property="auto_risk_type_id", type="iteger", example=2, description="define this model is elektrocar, import or local ... "),
     *                 @OA\Property (property="status", type="iteger", example=1, description="1 => active, 0 => inactive"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated automodel",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/automodel")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateAutomodelForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/auto-model/get-by-id",
     *     summary="get automodel by id",
     *     tags={"AutoModelController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="automodel",
     *         @OA\JsonContent(type="object", ref="#components/schemas/automodel")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id)
            return Automodel::findOne($id);

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/auto-model/delete",
     *     summary="Method to delete automodel by id",
     *     tags={"AutoModelController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $auto_model = Automodel::findOne($id))
        {
            if (!empty($auto_model->autocomps))
                throw new BadRequestHttpException(Yii::t('app', 'There are some autocomps of this Automodel'));

            return $auto_model->delete();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }
}