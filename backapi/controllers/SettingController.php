<?php
namespace backapi\controllers;

use backapi\models\forms\carPriceForm\changePaidForm;
use backapi\models\forms\carPriceForm\partnerMonthlyPaidForm;
use backapi\models\forms\carPriceForm\requestGraphForm;
use backapi\models\forms\Setting\changeValueForm;
use backapi\models\searchs\AutoModelSearch;
use backapi\models\searchs\SettingSearch;
use common\helpers\GeneralHelper;
use common\models\Automodel;
use common\models\Osago;
use common\models\Setting;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class SettingController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'change-paid' => ['PUT'],
            ],
        ];

//        $behaviors['authenticator']['except'] = ['import-file'];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/setting/all",
     *     summary="Method to get all settings with or without pagination ",
     *     tags={"SettingController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="settings with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/setting")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="settings without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/setting")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new SettingSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => Setting::getFullArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/setting/get-by-id",
     *     summary="get setting by id",
     *     tags={"SettingController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="id", in="query", @OA\Schema (type="integer"), example="234"),
     *     @OA\Response(response="200", description="setting",
     *            @OA\JsonContent( type="object", ref="#components/schemas/setting")
     *      ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionGetById(string $id)
    {
        return $this->getByID($id)->getFullArr();
    }

    private function getById($setting_id)
    {
        if (!$setting = Setting::findOne(['id' => $setting_id]))
            throw new NotFoundHttpException(Yii::t('app', 'setting id not found'));

        return $setting;
    }


    /**
     * @OA\Put(
     *     path="/setting/update-value",
     *     summary="Method to change value of setting",
     *     tags={"SettingController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "value"},
     *                 @OA\Property (property="id", type="integer", example=18),
     *                 @OA\Property (property="value", type="string", description="123"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="updated setting",
     *           @OA\JsonContent( type="object", ref="#components/schemas/setting")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdateValue()
    {
        GeneralHelper::checkPermission();

        $model = new changeValueForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}