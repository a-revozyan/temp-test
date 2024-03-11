<?php
namespace backapi\controllers;

use backapi\models\forms\tagForms\CreateTagForm;
use backapi\models\forms\tagForms\UpdateTagForm;
use backapi\models\searchs\TagSearch;
use common\helpers\GeneralHelper;
use common\models\NewsTag;
use common\models\Tag;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class TagController extends BaseController
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
     *     path="/tag/all",
     *     summary="Method to get all tags with or without pagination ",
     *     tags={"TagController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from name_uz, name_ru, name_en"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name_uz, name_ru, name_en. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="tags with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/tag")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Tag::getFullArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/tag/export",
     *     summary="Method to get all tag excel",
     *     tags={"TagController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from name_uz, name_ru, name_en"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name_uz, name_ru, name_en. use '-' for descending"),
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
        return GeneralHelper::export(Tag::className(), TagSearch::className(), [
            'id' => 'integer',
            'name_uz' => 'string',
            'name_ru' => 'string',
            'name_en' => 'string',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/tag/create",
     *     summary="create new tag",
     *     tags={"TagController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name_ru", "name_uz", "name_en"},
     *                 @OA\Property (property="name_uz", type="string", example="salom"),
     *                 @OA\Property (property="name_ru", type="string", example="salom"),
     *                 @OA\Property (property="name_en", type="string", example="salom"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created tag",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/tag")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        $model = new CreateTagForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/tag/update",
     *     summary="update tag",
     *     tags={"TagController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name_ru", "name_uz", "name_en"},
     *                  @OA\Property (property="name_uz", type="string", example="salom"),
     *                  @OA\Property (property="name_ru", type="string", example="salom"),
     *                  @OA\Property (property="name_en", type="string", example="salom"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated tag",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/tag")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateTagForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/tag/get-by-id",
     *     summary="get tag by id",
     *     tags={"TagController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="agent",
     *         @OA\JsonContent(type="object", ref="#components/schemas/tag")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $tag = Tag::findOne($id))
            return $tag->getFullArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/tag/delete",
     *     summary="Method to delete tag by id",
     *     tags={"TagController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $tag = Tag::findOne($id))
        {
            NewsTag::deleteAll(['tag_id' => $tag->id]);
            return $tag->delete();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }

}