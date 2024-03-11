<?php
namespace frontend\controllers;

use common\models\Tag;
use frontend\models\Searchs\NewsSearch;
use common\models\News;
use frontend\models\Searchs\TagSearch;
use yii\web\BadRequestHttpException;

class NewsController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ["*"];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/news/all",
     *     summary="Method to get all news with or without pagination ",
     *     tags={"NewsController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from title, short_info, body"),
     *     @OA\Parameter (name="filter[is_main]", in="query", @OA\Schema (type="integer"), description="1 => main, 0 => not main"),
     *     @OA\Parameter (name="filter[tag_ids][]", in="query", @OA\Schema (type="array", @OA\Items(type="integer"))),
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
            'models' => News::getFullClientArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }


    /**
     * @OA\Get(
     *     path="/news/get-by-id",
     *     summary="get news by id",
     *     tags={"NewsController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
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
        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $news = News::findOne($id))
            return $news->getFullClientArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Get(
     *     path="/news/tags",
     *     summary="Method to get all tags with or without pagination ",
     *     tags={"NewsController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200", description="news with pagination for table",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/id_name")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionTags()
    {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Tag::getFullClientArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

}