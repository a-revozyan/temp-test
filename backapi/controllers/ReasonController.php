<?php
namespace backapi\controllers;

use backapi\models\forms\reasonForms\CreateReasonForm;
use backapi\models\forms\reasonForms\UpdateReasonForm;
use backapi\models\searchs\ReasonSearch;
use common\helpers\GeneralHelper;
use common\models\Reason;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class ReasonController extends BaseController
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
     *     path="/reason/all",
     *     summary="Method to get all reasons with or without pagination ",
     *     tags={"ReasonController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from name"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="0 yoki 1"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="reasons with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/id_name_status")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        $searchModel = new ReasonSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Reason::getShortArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/reason/export",
     *     summary="Method to get all reason excel",
     *     tags={"ReasonController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from name"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="0 yoki 1"),
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
        return GeneralHelper::export(Reason::className(), ReasonSearch::className(), [
            'id' => 'integer',
            'name' => 'string',
            'status' => 'integer',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/reason/create",
     *     summary="create new reason",
     *     tags={"ReasonController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property (property="name", type="string", example="new brand"),
     *                 @OA\Property (property="status", type="integer", example="0 yoki 1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created reason",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/id_name_status")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        $model = new CreateReasonForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/reason/update",
     *     summary="update reason",
     *     tags={"ReasonController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "name"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="name", type="string", example="new brand"),
     *                 @OA\Property (property="status", type="integer", example="0 yoki 1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated reason",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/id_name_status")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateReasonForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/reason/get-by-id",
     *     summary="get reason by id",
     *     tags={"ReasonController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="reason",
     *         @OA\JsonContent(type="object", ref="#components/schemas/id_name_status")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id)
            return Reason::findOne($id)->getShortArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/reason/delete",
     *     summary="Method to delete reason by id",
     *     tags={"ReasonController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $reason = Reason::findOne($id))
        {
            if (!empty($reason->kaskos) or !empty($reason->osagos) or !empty($reason->travels) or !empty($reason->accidents) or !empty($reason->kaskoBySubscriptionPolicies))
                throw new BadRequestHttpException(Yii::t('app', 'There are some products which is used this Reason, please just make inactive'));
            return $reason->delete();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }

}