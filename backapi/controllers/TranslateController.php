<?php
namespace backapi\controllers;

use backapi\models\forms\translateForms\UpdateTranslateForm;
use backapi\models\searchs\TranslateSearch;
use common\helpers\GeneralHelper;
use common\models\SourceMessage;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class TranslateController extends BaseController
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
     *     path="/translate/all",
     *     summary="Method to get all translates with or without pagination ",
     *     tags={"TranslateController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[message]", in="query", @OA\Schema (type="string"), description="search from key of translate"),
     *     @OA\Parameter (name="filter[uz]", in="query", @OA\Schema (type="string"), description="search from uz message of translate"),
     *     @OA\Parameter (name="filter[ru]", in="query", @OA\Schema (type="string"), description="search from ru message of translate"),
     *     @OA\Parameter (name="filter[en]", in="query", @OA\Schema (type="string"), description="search from en message of translate"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, uz, ru, en. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="translate with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/translate")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="translate without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/translate")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new TranslateSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => SourceMessage::getFullArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/translate/export",
     *     summary="Method to get all translates excel",
     *     tags={"TranslateController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[message]", in="query", @OA\Schema (type="string"), description="search from key of translate"),
     *     @OA\Parameter (name="filter[uz]", in="query", @OA\Schema (type="string"), description="search from uz message of translate"),
     *     @OA\Parameter (name="filter[ru]", in="query", @OA\Schema (type="string"), description="search from ru message of translate"),
     *     @OA\Parameter (name="filter[en]", in="query", @OA\Schema (type="string"), description="search from en message of translate"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, uz, ru, en. use '-' for descending"),
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

        return GeneralHelper::export(SourceMessage::className(), TranslateSearch::className(), [
            'id' => 'integer',
            'message' => 'string',
            'ru' => 'string',
            'uz' => 'string',
            'en' => 'string',
        ], [
            'id',
            'message',
            'ru' => function($model){
                return $model->ru->translation ?? "";
            },
            'uz' => function($model){
                return $model->uz->translation ?? "";
            },
            'en' => function($model){
                return $model->en->translation ?? "";
            },
        ]);
    }

    /**
     * @OA\Put(
     *     path="/translate/update",
     *     summary="udpate translate",
     *     tags={"TranslateController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="ru", type="string", example="ru text"),
     *                 @OA\Property (property="uz", type="string", example="uz text"),
     *                 @OA\Property (property="en", type="string", example="en text"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated translate",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/translate")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateTranslateForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/translate/get-by-id",
     *     summary="get translate by id",
     *     tags={"TranslateController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="translate",
     *         @OA\JsonContent(type="object", ref="#components/schemas/translate")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $source_message = SourceMessage::findOne($id))
            return $source_message->getFullArr();

        throw new BadRequestHttpException("ID is incorrect");
    }
}