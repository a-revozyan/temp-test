<?php
namespace backapi\controllers;

use backapi\models\forms\autobrandForms\CreateAutobrandForm;
use backapi\models\forms\autobrandForms\UpdateAutobrandForm;
use backapi\models\searchs\AutoBrandSearch;
use common\helpers\GeneralHelper;
use common\models\Autobrand;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class AutoBrandController extends BaseController
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
     *     path="/auto-brand/all",
     *     summary="Method to get all autobrands with or without pagination ",
     *     tags={"AutoBrandController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from name"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="1 => acktive, 0 => inactive"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name, order, status. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="autobrands with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/autobrand")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        $searchModel = new AutoBrandSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Autobrand::getFullArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/auto-brand/export",
     *     summary="Method to get all autobrand excel",
     *     tags={"AutoBrandController"},
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
        return GeneralHelper::export(Autobrand::className(), AutoBrandSearch::className(), [
            'id' => 'integer',
            'name' => 'string',
            'order' => 'string',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/auto-brand/create",
     *     summary="create new autobrand",
     *     tags={"AutoBrandController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property (property="name", type="string", example="new brand"),
     *                 @OA\Property (property="order", type="iteger", example=2),
     *                 @OA\Property (property="status", type="iteger", example=1, description="1 => active, 0 => inactive"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created autobrand",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/autobrand")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        $model = new CreateAutobrandForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/auto-brand/update",
     *     summary="update autobrand",
     *     tags={"AutoBrandController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "name"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="name", type="string", example="new brand"),
     *                 @OA\Property (property="order", type="iteger", example=2),
     *                 @OA\Property (property="status", type="iteger", example=1, description="1 => active, 0 => inactive"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated autobrand",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/autobrand")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateAutobrandForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/auto-brand/get-by-id",
     *     summary="get autobrand by id",
     *     tags={"AutoBrandController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="agent",
     *         @OA\JsonContent(type="object", ref="#components/schemas/autobrand")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id)
            return Autobrand::findOne($id);

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/auto-brand/delete",
     *     summary="Method to delete autobrand by id",
     *     tags={"AutoBrandController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $auto_brand = Autobrand::findOne($id))
        {
            if (!empty($auto_brand->automodels))
                throw new BadRequestHttpException(Yii::t('app', 'There are some automodels of this Autobrand'));
            return $auto_brand->delete();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }

}