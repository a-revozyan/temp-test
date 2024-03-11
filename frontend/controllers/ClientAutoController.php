<?php

namespace frontend\controllers;

use common\models\Automodel;
use common\models\ClientAuto;
use frontend\models\ClientAutoForms\CreateClientAutoForm;
use frontend\models\ClientAutoForms\UpdateClientAutoForm;
use frontend\models\Searchs\ClientAutoSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class ClientAutoController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['POST'],
                'update' => ['PUT'],
                'delete' => ['DELETE'],
            ]
        ];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/client-auto/all",
     *     summary="Method to get all client-autos with or without pagination ",
     *     tags={"ClientAutoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="client-auto with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/client_auto")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="autobmodel without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/client_auto")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        $searchModel = new ClientAutoSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => ClientAuto::getFullArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Post(
     *     path="/client-auto/create",
     *     summary="create new client-auto",
     *     tags={"ClientAutoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"autonumber", "tex_pass_series", "tex_pass_number"},
     *                 @OA\Property (property="autocomp_id", type="integer", example="12", description="autocomp id"),
     *                 @OA\Property (property="manufacture_year", type="integer", example="2021"),
     *                 @OA\Property (property="autonumber", type="string", example="80U950JA"),
     *                 @OA\Property (property="tex_pass_series", type="string", example="AAF"),
     *                 @OA\Property (property="tex_pass_number", type="string", example="234578"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created client-auto",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/client_auto")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        $model = new CreateClientAutoForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/client-auto/update",
     *     summary="udpate client-auto",
     *     tags={"ClientAutoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  required={"id", "autonumber", "tex_pass_series", "tex_pass_number"},
     *                  @OA\Property (property="id", type="integer", example="12", description="if of auto"),
     *                  @OA\Property (property="autocomp_id", type="integer", example="12", description="komplektatsiya id"),
     *                  @OA\Property (property="manufacture_year", type="integer", example="2022"),
     *                  @OA\Property (property="autonumber", type="string", example="80U950JA"),
     *                  @OA\Property (property="tex_pass_series", type="string", example="AAF"),
     *                  @OA\Property (property="tex_pass_number", type="string", example="1234567"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated client-auto",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/client_auto")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        $model = new UpdateClientAutoForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/client-auto/get-by-id",
     *     summary="get client-auto by id",
     *     tags={"ClientAutoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="client-auto",
     *         @OA\JsonContent(type="object", ref="#components/schemas/client_auto")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $client_auto = ClientAuto::findOne(['id' => $id, 'f_user_id' => Yii::$app->getUser()->id]))
            return $client_auto->getFullArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/client-auto/delete",
     *     summary="Method to delete client-auto by id",
     *     tags={"ClientAutoController"},
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
        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $client_auto = ClientAuto::findOne(['id' => $id, 'f_user_id' => Yii::$app->getUser()->id]))
        {
            return $client_auto->delete();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }
}