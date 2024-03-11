<?php
namespace backapi\controllers;

use backapi\models\forms\qaForms\CreateQaForm;
use backapi\models\forms\qaForms\UpdateQaForm;
use backapi\models\searchs\AutoBrandSearch;
use backapi\models\searchs\QaSearch;
use common\helpers\GeneralHelper;
use common\models\Autobrand;
use common\models\Qa;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class QaController extends BaseController
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
     *     path="/qa/all",
     *     summary="Method to get all questions with or without pagination ",
     *     tags={"QaController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from questions and answers"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="0 => inactive, 1 => active"),
     *     @OA\Parameter (name="filter[page]", in="query", @OA\Schema (type="integer"), description="0 => homepage, 1 => kbs"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, question_uz, question_ru, answer_uz, answer_ru. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="questions with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/qa")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new QaSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Qa::getShortAdminArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/qa/export",
     *     summary="Method to get all questions excel",
     *     tags={"QaController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from questions and answers"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, question_uz, question_ru, answer_uz, answer_ru. use '-' for descending"),
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

        return GeneralHelper::export(Qa::className(), QaSearch::className(), [
            'id' => 'integer',
            'question_ru' => 'string',
            'question_uz' => 'string',
            'question_en' => 'string',
            'answer_ru' => 'string',
            'answer_uz' => 'string',
            'answer_en' => 'string',
            'page' => 'integer',
            'status' => 'integer',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/qa/create",
     *     summary="create new questions",
     *     tags={"QaController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"status"},
     *                 @OA\Property (property="question_ru", type="string", example="new question"),
     *                 @OA\Property (property="question_uz", type="string", example="new question"),
     *                 @OA\Property (property="question_en", type="string", example="new question"),
     *                 @OA\Property (property="answer_ru", type="string", example="new answer"),
     *                 @OA\Property (property="answer_uz", type="string", example="new answer"),
     *                 @OA\Property (property="answer_en", type="string", example="new answer"),
     *                 @OA\Property (property="page", type="integer", example="0", description="0 => homepage, 1 => kbs"),
     *                 @OA\Property (property="status", type="integer", example="0", description="0 yoki 1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created questions",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/qa")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateQaForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/qa/update",
     *     summary="update questions",
     *     tags={"QaController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  required={"id", "status"},
     *                 @OA\Property (property="id", type="integer", example="4"),
     *                 @OA\Property (property="question_ru", type="string", example="new question"),
     *                 @OA\Property (property="question_uz", type="string", example="new question"),
     *                 @OA\Property (property="question_en", type="string", example="new question"),
     *                 @OA\Property (property="answer_ru", type="string", example="new answer"),
     *                 @OA\Property (property="answer_uz", type="string", example="new answer"),
     *                 @OA\Property (property="answer_en", type="string", example="new answer"),
     *                 @OA\Property (property="page", type="integer", example="0", description="0 =>home page, 1 => kasko by subscription"),
     *                 @OA\Property (property="status", type="integer", example="0", description="0 yoki 1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated questions",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/qa")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateQaForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/qa/get-by-id",
     *     summary="get questions by id",
     *     tags={"QaController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="agent",
     *         @OA\JsonContent(type="object", ref="#components/schemas/questions")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id)
            return Qa::findOne($id);

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/qa/delete",
     *     summary="Method to delete questions by id",
     *     tags={"QaController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $qa = Qa::findOne($id))
            return $qa->delete();

        throw new BadRequestHttpException("ID is incorrect");
    }

}