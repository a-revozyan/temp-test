<?php
namespace backapi\controllers;

use backapi\models\forms\newsForms\CreateNewsForm;
use backapi\models\forms\newsForms\UpdateNewsForm;
use backapi\models\searchs\NewsSearch;
use common\helpers\GeneralHelper;
use common\models\News;
use common\models\NewsTag;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

class NewsController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['POST'],
                'update' => ['POST'],
                'delete' => ['DELETE'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/news/all",
     *     summary="Method to get all news with or without pagination ",
     *     tags={"NewsController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from title, short_info, body"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="1 => acktive, 0 => inactive"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, created_at, updated_at, status. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="news with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/news")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => News::getFullArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/news/export",
     *     summary="Method to get all news excel",
     *     tags={"NewsController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from title, short_info, body"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="1 => acktive, 0 => inactive"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, created_at, updated_at, status. use '-' for descending"),
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
        return GeneralHelper::export(News::className(), NewsSearch::className(), [
            'id' => 'integer',
            'title_uz' => 'string',
            'title_ru' => 'string',
            'title_en' => 'string',
            'image_uz' => 'string',
            'image_ru' => 'string',
            'image_en' => 'string',
            'short_info_uz' => 'string',
            'short_info_ru' => 'string',
            'short_info_en' => 'string',
            'body_uz' => 'string',
            'body_ru' => 'string',
            'body_en' => 'string',
            'status' => 'integer',
            'created_at' => 'string',
            'updated_at' => 'string',
        ], [
            'id',
            'title_uz',
            'title_ru',
            'title_en',
            'image_uz' => function($model){
                return !empty($model->image_uz) ? News::images_path() . $model->image_uz : null;
            },
            'image_ru' => function($model){
                return !empty($model->image_ru) ? News::images_path() . $model->image_ru : null;
            },
            'image_en' => function($model){
                return !empty($model->image_en) ? News::images_path() . $model->image_en : null;
            },
            'short_info_uz',
            'short_info_ru',
            'short_info_en',
            'body_uz',
            'body_ru',
            'body_en',
            'status',
            'created_at' => function($model){
                return !empty($model->created_at) ? date('d.m.Y H:i:s', $model->created_at) : "";
            },
            'updated_at' => function($model){
                return !empty($model->created_at) ? date('d.m.Y H:i:s', $model->created_at) : "";
            },
        ]);
    }

    /**
     * @OA\Post(
     *     path="/news/create",
     *     summary="create new news",
     *     tags={"NewsController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title_uz", "title_ru", "short_info_uz", "short_info_ru", "body_uz", "body_ru", "status"},
     *                 @OA\Property(property="title_uz", type="string|null", example="text"),
     *                 @OA\Property(property="title_ru", type="string|null", example="text"),
     *                 @OA\Property(property="title_en", type="string|null", example="text"),
     *                 @OA\Property(property="image_uz", type="string|null", example="url"),
     *                 @OA\Property(property="image_ru", type="string|null", example="url"),
     *                 @OA\Property(property="image_en", type="string|null", example="url"),
     *                 @OA\Property(property="short_info_uz", type="string|null", example="text"),
     *                 @OA\Property(property="short_info_ru", type="string|null", example="text"),
     *                 @OA\Property(property="short_info_en", type="string|null", example="text"),
     *                 @OA\Property(property="body_uz", type="string|null", example="text"),
     *                 @OA\Property(property="body_ru", type="string|null", example="text"),
     *                 @OA\Property(property="body_en", type="string|null", example="text"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created news",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/news")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateNewsForm();
        $model->setAttributes($this->post);
        $model->image_uz = UploadedFile::getInstanceByName('image_uz');
        $model->image_ru = UploadedFile::getInstanceByName('image_ru');
        $model->image_en = UploadedFile::getInstanceByName('image_en');
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/news/update",
     *     summary="update news",
     *     tags={"NewsController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"id", "title_uz", "title_ru", "short_info_uz", "short_info_ru", "body_uz", "body_ru", "status"},
     *                  @OA\Property(property="id", type="integer|null", example="12"),
     *                  @OA\Property(property="title_uz", type="string|null", example="text"),
     *                  @OA\Property(property="title_ru", type="string|null", example="text"),
     *                  @OA\Property(property="title_en", type="string|null", example="text"),
     *                  @OA\Property(property="image_uz", type="string|null", example="url"),
     *                  @OA\Property(property="image_ru", type="string|null", example="url"),
     *                  @OA\Property(property="image_en", type="string|null", example="url"),
     *                  @OA\Property(property="short_info_uz", type="string|null", example="text"),
     *                  @OA\Property(property="short_info_ru", type="string|null", example="text"),
     *                  @OA\Property(property="short_info_en", type="string|null", example="text"),
     *                  @OA\Property(property="body_uz", type="string|null", example="text"),
     *                  @OA\Property(property="body_ru", type="string|null", example="text"),
     *                  @OA\Property(property="body_en", type="string|null", example="text"),
     *                  @OA\Property(property="status", type="integer", example=1),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated news",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/news")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateNewsForm();
        $model->setAttributes($this->post);

        $model->image_uz = UploadedFile::getInstanceByName('image_uz');
        $model->image_ru = UploadedFile::getInstanceByName('image_ru');
        $model->image_en = UploadedFile::getInstanceByName('image_en');
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/news/get-by-id",
     *     summary="get news by id",
     *     tags={"NewsController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="agent",
     *         @OA\JsonContent(type="object", ref="#components/schemas/news")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $news = News::findOne($id))
            return $news->getFullArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/news/delete",
     *     summary="Method to delete news by id",
     *     tags={"NewsController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $news = News::findOne($id))
        {
            foreach (['uz', 'ru', 'en'] as $lang) {
                $image_attr = "image_" . $lang;
                if (!empty($news->{$image_attr}))
                    unlink(News::images_folder() . $news->{$image_attr});
            }
            NewsTag::deleteAll(['news_id' => $news->id]);
            return $news->delete();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }

}