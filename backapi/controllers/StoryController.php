<?php
namespace backapi\controllers;

use backapi\models\forms\automodelForms\UpdateAutomodelForm;
use backapi\models\forms\story\CreateStoryForm;
use backapi\models\forms\story\UpdateStoryForm;
use backapi\models\searchs\StorySearch;
use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\models\Story;
use common\models\StoryFile;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

class StoryController extends BaseController
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
     *     path="/story/all",
     *     summary="Method to get all stories with or without pagination ",
     *     tags={"StoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from names of automodel and auto brand"),
     *     @OA\Parameter (name="filter[period_status]", in="query", @OA\Schema (type="integer"), description="0 => inactive, 1 => active"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="1 => ready, 0 => draft"),
     *     @OA\Parameter (name="filter[type]", in="query", @OA\Schema (type="integer"), description="1 => reel, 0 => story"),
     *     @OA\Parameter (name="filter[view_condition]", in="query", @OA\Schema (type="integer"), description="'new_users' => 0, 'bought_only_1_policy' => 1, 'bought_several_policy' => 2, 'old_user_but_never_bought' => 3"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name, period_status, status, views_count, priority, type. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="stories with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/story")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="stories without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/story")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new StorySearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Story::getFullAdminArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/story/export",
     *     summary="Method to get all stories excel",
     *     tags={"StoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[name]", in="query", @OA\Schema (type="string"), description="search from names of automodel and auto brand"),
     *     @OA\Parameter (name="filter[period_status]", in="query", @OA\Schema (type="integer"), description="0 => inactive, 1 => active"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="1 => ready, 0 => draft"),
     *     @OA\Parameter (name="filter[type]", in="query", @OA\Schema (type="integer"), description="1 => reel, 0 => story"),
     *     @OA\Parameter (name="filter[view_condition]", in="query", @OA\Schema (type="integer"), description="'new_users' => 0, 'bought_only_1_policy' => 1, 'bought_several_policy' => 2, 'old_user_but_never_bought' => 3"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, name, period_status, status, views_count, priority, type. use '-' for descending"),
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

        return GeneralHelper::export(Story::className(), StorySearch::className(), [
            'id' => 'integer',
            'name' => 'string',
            'type' => 'string',
            'status' => 'string',
            'priority' => 'integer',
            'begin_period' => 'string',
            'end_period' => 'string',
            'begin_time' => 'string',
            'end_time' => 'string',
            'weekdays' => 'string',
            'view_condition' => 'string',
            'period_status' => 'string',
        ], [
            'id',
            'name',
            'type' => function($model){
                return array_flip(Story::TYPE)[$model->type] ?? "";
            },
            'status' => function($model){
                return array_flip(Story::STATUS)[$model->type] ?? "";
            },
            'priority',
            'begin_period' => function ($model) {
                return !empty($model->begin_period) ? DateHelper::date_format($model->begin_period, 'Y-m-d', 'd.m.Y') : null;
            },
            'end_period' => function ($model) {
                return !empty($model->end_period) ? DateHelper::date_format($model->end_period, 'Y-m-d', 'd.m.Y') : null;
            },
            'begin_time',
            'end_time',
            'weekdays' => function ($model) {
                return implode(', ', json_decode($model->weekdays) ?? []);
            },
            'view_condition' => function($model){
                return array_flip(Story::VIEW_CONDITION)[$model->view_condition] ?? "";
            },
            'period_status' => function($model){
                return array_flip(Story::PERIOD_STATUS)[$model->period_status] ?? "";
            },
        ]);
    }

    /**
     * @OA\Post(
     *     path="/story/create",
     *     summary="create new story",
     *     tags={"StoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "type", "files", "cover_file", "status"},
     *                 @OA\Property (property="name", type="string", example="new story name"),
     *                 @OA\Property (property="type", type="iteger", example=1, description="'story' => 0, 'reel' => 1"),
     *                 @OA\Property (property="files[]", type="array",  @OA\Items(type="file"), description="eng ko'pi bilan 5 ta fayl yuborish mumkin"),
     *                 @OA\Property (property="cover_file", type="file"),
     *                 @OA\Property (property="status", type="iteger", example=1, description="1 => active, 0 => inactive"),
     *                 @OA\Property (property="begin_period", type="string", example="29.01.2024", description="formt: d.m.Y"),
     *                 @OA\Property (property="end_period", type="string", example="29.01.2024", description="formt: d.m.Y"),
     *                 @OA\Property (property="weekdays[]", type="array",  @OA\Items(type="integer"), description="du => 1, sesha => 2, chor => 3, pay => 4, juma => 5, sham => 6, yak => 7"),
     *                 @OA\Property (property="begin_time", type="string", example="15:21", description="formt: H:i"),
     *                 @OA\Property (property="end_time", type="string", example="15:30", description="formt: H:i"),
     *                 @OA\Property (property="priority", type="iteger", example=1),
     *                 @OA\Property (property="view_condition", type="iteger", example=1, description="'new_users' => 0,'bought_only_1_policy' => 1,'bought_several_policy' => 2,'old_user_but_never_bought' => 3, "),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created story",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/story")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateStoryForm();
        $model->setAttributes($this->post);
        $model->files = UploadedFile::getInstancesByName('files');
        $model->cover_file = UploadedFile::getInstanceByName('cover_file');
        if ($model->validate())
            return $model->save()->getFullAdminArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/story/update",
     *     summary="udpate story",
     *     tags={"StoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                  required={"name", "type", "files", "cover_file", "status"},
     *                  @OA\Property (property="name", type="string", example="new story name"),
     *                  @OA\Property (property="type", type="iteger", example=1, description="'story' => 0, 'reel' => 1"),
     *                  @OA\Property (property="files[]", type="array",  @OA\Items(type="file"), description="eng ko'pi bilan 5 ta fayl yuborish mumkin"),
     *                  @OA\Property (property="cover_file", type="file"),
     *                  @OA\Property (property="status", type="iteger", example=1, description="1 => active, 0 => inactive"),
     *                  @OA\Property (property="begin_period", type="string", example="29.01.2024", description="formt: d.m.Y"),
     *                  @OA\Property (property="end_period", type="string", example="29.01.2024", description="formt: d.m.Y"),
     *                  @OA\Property (property="weekdays[]", type="array",  @OA\Items(type="integer"), description="du => 1, sesha => 2, chor => 3, pay => 4, juma => 5, sham => 6, yak => 7"),
     *                  @OA\Property (property="begin_time", type="string", example="15:21", description="formt: H:i"),
     *                  @OA\Property (property="end_time", type="string", example="15:30", description="formt: H:i"),
     *                  @OA\Property (property="priority", type="iteger", example=1),
     *                  @OA\Property (property="view_condition", type="iteger", example=1, description="'new_users' => 0,'bought_only_1_policy' => 1,'bought_several_policy' => 2,'old_user_but_never_bought' => 3, "),
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

        $model = new UpdateStoryForm();
        $model->setAttributes($this->post);
        $model->files = UploadedFile::getInstancesByName('files');
        $model->cover_file = UploadedFile::getInstanceByName('cover_file');
        if ($model->validate())
            return $model->save()->getFullAdminArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Delete(
     *     path="/story/delete-file",
     *     summary="delete file of story by id of file",
     *     tags={"StoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="story_file_id", in="query", @OA\Schema (type="integer"), example=34),
     *     @OA\Response(
     *         response="200", description="if successfully deleted API return true",
     *         @OA\JsonContent(type="boolean", example=true)
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionDeleteFile($file_id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($file_id) and is_numeric($file_id) and (int)$file_id == $file_id and $file = StoryFile::findOne($file_id))
        {
            $file->deleteWithFile();
            return true;
        }

        throw new BadRequestHttpException(\Yii::t('app', 'ID is incorrect'));
    }

    /**
     * @OA\Get(
     *     path="/story/get-by-id",
     *     summary="get story by id",
     *     tags={"StoryController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="automodel",
     *         @OA\JsonContent(type="object", ref="#components/schemas/story")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $story = Story::findOne($id))
            return $story->getFullAdminArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Delete  (
     *     path="/story/delete",
     *     summary="Method to delete story by id",
     *     tags={"StoryController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $story = Story::findOne($id))
        {
            $folder = str_replace('\\', '/', \Yii::getAlias('@backapi') . '/web/uploads/story/files/' . $story->id);
            GeneralHelper::deleteFolder($folder);
            return $story->delete();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }
}