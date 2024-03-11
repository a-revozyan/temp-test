<?php

namespace frontend\controllers;

use frontend\models\Searchs\QaSearch;
use common\helpers\GeneralHelper;
use common\models\City;
use common\models\Kasko;
use common\models\KaskoFile;
use common\models\KaskoRisk;
use common\models\News;
use common\models\Osago;
use common\models\Partner;
use common\models\Qa;
use common\models\Travel;
use common\models\TravelMember;
use common\models\User;
use frontend\models\GeneralForms\CreateOpinionForm;
use OpenApi\Generator;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class GeneralController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'opinion' => 'POST'
            ]
        ];

//        $behaviors['authenticator']['except'] = ["*"];
        $behaviors['authenticator']['only'] = ["delete-all-forever"];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/general/partners",
     *     summary="Method to get all partners",
     *     tags={"GeneralController"},
     *     @OA\Response(response="200",description="active partners",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="name", type="string", example="APEX Insurance"),
     *                  @OA\Property(property="image", type="string", example="/uploads/partners/partner1601399768.png"),
     *              )
     *          )
     *     )
     * )
     */
    public function actionPartners()
    {
        $partners = Partner::find()->where(['status' => 1])->all();
        return array_map(function ($partner) {
            return [
                'id' => $partner->id,
                'name' => $partner->name,
                'image' => '/uploads/partners/' . $partner->image,
            ];
        }, $partners);
    }

    /**
     * @OA\Get(
     *     path="/general/cities",
     *     summary="Method to get all cities",
     *     tags={"GeneralController"},
     *     @OA\Response(response="200",description="active cities",
     *          @OA\JsonContent(type="array", @OA\Items(type="object", ref="#/components/schemas/id_name"))
     *     )
     * )
     */
    public function actionCities()
    {
        $cities = City::find()->where(['status' => City::STATUS['active']])->all();
        return City::getShortArrCollection($cities);
    }

    /**
     * @OA\Get(
     *     path="/general/qas",
     *     summary="Method to get all questions",
     *     tags={"GeneralController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="filter[page]", in="query", @OA\Schema (type="integer"), description="0 => home_page, 1 => kbs, 2 => kasko, 3 => travel"),
     *     @OA\Response(response="200",description="active questions",
     *          @OA\JsonContent(
     *              @OA\Property(property="models", type="array", @OA\Items(type="object", ref="#/components/schemas/qas")),
     *              @OA\Property(property="pages", type="object", ref="#/components/schemas/pages"),
     *          )
     *
     *     )
     * )
     */
    public function actionQas()
    {
        $searchModel = new QaSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Qa::getShortClientArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/general/kasko-risks",
     *     summary="Method to get all risks of kasko product",
     *     tags={"GeneralController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Response(response="200", description="all risks of kasko",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="name", type="string", example="Повреждение автомобиля"),
     *                  @OA\Property(property="amount", type="float|null", example="5000000"),
     *                  @OA\Property(property="description", type="string", example="Это добровольное страхование автогражданской ответственности, т.е нанесение ущерба или вреда здоровью и жизни третьему лицу. Платить будет страховая компания, в рамках установленной суммы."),
     *                  @OA\Property(property="category_id", type="integer|null", example="1"),
     *                  @OA\Property(property="show_desc", type="integer", example="1", description="0 or 1"),
     *              )
     *          )
     *     )
     * )
     */
    public function actionKaskoRisks()
    {
        $kasko_risks = KaskoRisk::find()
            ->select([
                'id',
                'name' => 'name_' . GeneralHelper::lang_of_local(),
                'amount' => 'amount',
                'description' => 'description_' . GeneralHelper::lang_of_local(),
                'category_id' => 'category_id',
                'show_desc' => 'show_desc',
            ])->asArray()->all();

        $unique_name_kasko_risks = [];
        foreach ($kasko_risks as $kasko_risk) {
            $unique_name_kasko_risks[$kasko_risk['name']] = $kasko_risk;
        }

        return array_values($unique_name_kasko_risks);
    }

    //for developers
    public function actionDeleteAllForever()
    {
        $user = \Yii::$app->user;
        $kaskos = Kasko::find()->where(['f_user_id' => $user->id])->all();
        foreach ($kaskos as $kasko) {
            $kasko_files = KaskoFile::find()->where(['kasko_id' => $kasko->id])->all();
            foreach ($kasko_files as $kasko_file) {
                if (is_file(Yii::getAlias('@webroot') . $kasko_file->path))
                    unlink(Yii::getAlias('@webroot') . $kasko_file->path);
                $kasko_file->delete();
            }
            $kasko->delete();
        }

        $travels = Travel::find()->where(['f_user_id' => $user->id])->all();
        foreach ($travels as $travel) {
            $travel_members = TravelMember::find()->where(['travel_id' => $travel->id])->all();
            foreach ($travel_members as $travel_member) {
                $travel_member->delete();
            }
            $travel->delete();
        }

        User::deleteAll(['id' => $user->id]);

        return "bye";
    }

    public function actionGenerateDoc()
    {
        $openapi = Generator::scan([Yii::getAlias('@frontend') . "/controllers"]);
        header('Content-Type: application/json');
        return json_decode($openapi->toJson(), JSON_FORCE_OBJECT);
    }

    /**
     * @OA\Put(
     *     path="/general/create-opinion",
     *     summary="creating opinion",
     *     tags={"GeneralController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "phone", "message"},
     *                 @OA\Property (property="name", type="string", example="Jobir"),
     *                 @OA\Property (property="phone", type="string", example="998946464400"),
     *                 @OA\Property (property="message", type="string", example="Baraka topinglar)"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created opinion",
     *          @OA\JsonContent( type="object",)
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionCreateOpinion()
    {
        $model = new CreateOpinionForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getShortArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}