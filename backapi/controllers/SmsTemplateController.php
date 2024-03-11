<?php

namespace backapi\controllers;

use backapi\models\forms\smsTemplateForms\CreateForm;
use backapi\models\forms\smsTemplateForms\PauseForm;
use backapi\models\forms\smsTemplateForms\RunForm;
use backapi\models\forms\smsTemplateForms\UpdateForm;
use backapi\models\searchs\SmsTemplateSearch;
use common\helpers\GeneralHelper;
use common\models\SmsTemplate;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

class SmsTemplateController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'run' => ['POST'],
                'pause' => ['PUT'],
                'create' => ['POST'],
                'update' => ['PUT'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/sms-template/all",
     *     summary="Method to get all sms templates with or without pagination ",
     *     tags={"SmsTemplateController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from text, method, region_car_numbers"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="send id of autobrand to get automodels which are realted to the autobrand"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: id, status, all_users_count, sms_count, use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="automodel with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/sms_template")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="autobmodel without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/sms_template")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new SmsTemplateSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => SmsTemplate::getShortArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Post (
     *     path="/sms-template/create",
     *     summary="Method to create messages",
     *     tags={"SmsTemplateController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"type", "method"},
     *                 @OA\Property (property="method", type="string", example="sendPhoto", description="sendMessage, sendPhoto, sendDocument, sendVideo"),
     *                 @OA\Property (property="file_url", type="file", description="method sendMessage bo'lsa required emas. boshqa holatlarda required"),
     *                 @OA\Property (property="text", type="string", example="hello", description="yuborilishi kerak bo'lgan matn, method sendMessage bo'lsa required"),
     *                 @OA\Property (property="region_car_numbers[]", type="array", @OA\Items(type="string", example="01"), description="selectga viloyatlarni chiqaring, birortasini tanlaganda apiga davlat raqamining boshlanishini yuboring. frontda oldin ishlatilgan bunaqasi"),
     *                 @OA\Property (property="number_drivers_id", type="integer|null", example="1", description="number-drivers ni apidan olib selectga chiqarish kerak"),
     *                 @OA\Property (property="registered_from_date", type="string|null", example="2022-10-25 12:14:15", description="Shu vaqtdan keyin yaratilgan Klientlar ga yuboriladi, Y-m-d H:i:s"),
     *                 @OA\Property (property="registered_till_date", type="string|null", example="2022-10-25 12:14:15", description="Shu vaqtgacha yaratilgan Klientlar ga yuboriladi, Y-m-d H:i:s"),
     *                 @OA\Property (property="bought_from_date", type="string|null", example="2022-10-25 12:14:15", description="Shu vaqtdan keyin polis sotib olganlar, Y-m-d H:i:s"),
     *                 @OA\Property (property="bought_till_date", type="string|null", example="2022-10-25 12:14:15", description="Shu vaqtgacha polis sotib olganlar, Y-m-d H:i:s"),
     *                 @OA\Property (property="begin_date", type="string", example="2022-10-25 12:14:15", description="Shu vaqtda yuborish boshlanishi kerak, Y-m-d H:i:s"),
     *                 @OA\Property (property="type", type="integer", example="2", description="'first_telegram_else_sms' => 1, 'users_which_have_telegram_via_telegram' => 2, 'users_which_have_not_telegram_via_sms' => 3, 'all_users_via_sms' => 4,"),
     *                 @OA\Property (property="product", type="integer", example=1, description="osago => 1, kasko => 2, travel => 3, accident => 4, kbs => 5"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", description="created sms template",
     *         @OA\JsonContent(type="object", ref="#components/schemas/full_sms_template")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateForm();
        $model->setAttributes($this->post);
        $model->file_url = UploadedFile::getInstanceByName('file_url');
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put (
     *     path="/sms-template/update",
     *     summary="Method to update sms template",
     *     tags={"SmsTemplateController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"type"},
     *                 @OA\Property (property="sms_template_id", type="integer", example="12"),
     *                 @OA\Property (property="text", type="string", example="hello", description="yuborilishi kerak bo'lgan matn, method sendMessage bo'lsa required"),
     *                 @OA\Property (property="region_car_numbers[]", type="array", @OA\Items(type="string", example="01"), description="selectga viloyatlarni chiqaring, birortasini tanlaganda apiga davlat raqamining boshlanishini yuboring. frontda oldin ishlatilgan bunaqasi"),
     *                 @OA\Property (property="number_drivers_id", type="integer|null", example="1", description="number-drivers ni apidan olib selectga chiqarish kerak"),
     *                 @OA\Property (property="registered_from_date", type="string|null", example="2022-10-25 12:14:15", description="Shu vaqtdan keyin yaratilgan Klientlar ga yuboriladi, Y-m-d H:i:s"),
     *                 @OA\Property (property="registered_till_date", type="string|null", example="2022-10-25 12:14:15", description="Shu vaqtgacha yaratilgan Klientlar ga yuboriladi, Y-m-d H:i:s"),
     *                 @OA\Property (property="bought_from_date", type="string|null", example="2022-10-25 12:14:15", description="Shu vaqtdan keyin polis sotib olganlar, Y-m-d H:i:s"),
     *                 @OA\Property (property="bought_till_date", type="string|null", example="2022-10-25 12:14:15", description="Shu vaqtgacha polis sotib olganlar, Y-m-d H:i:s"),
     *                 @OA\Property (property="begin_date", type="string", example="2022-10-25 12:14:15", description="Shu vaqtda yuborish boshlanishi kerak, Y-m-d H:i:s"),
     *                 @OA\Property (property="type", type="integer", example="2", description="'first_telegram_else_sms' => 1, 'users_which_have_telegram_via_telegram' => 2, 'users_which_have_not_telegram_via_sms' => 3, 'all_users_via_sms' => 4,"),
     *                 @OA\Property (property="product", type="integer", example=1, description="osago => 1, kasko => 2, travel => 3, accident => 4, kbs => 5"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", description="updated sms template",
     *         @OA\JsonContent(type="object", ref="#components/schemas/full_sms_template")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/sms-template/get-by-id",
     *     summary="get sms template by id",
     *     tags={"SmsTemplateController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="automodel",
     *         @OA\JsonContent(type="object", ref="#components/schemas/full_sms_template")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $sms_template = SmsTemplate::findOne($id))
            return $sms_template->getFullArr();

        throw new BadRequestHttpException("ID is incorrect");
    }

    /**
     * @OA\Post (
     *     path="/sms-template/run",
     *     summary="Method to start sending messages",
     *     tags={"SmsTemplateController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"sms_template_id"},
     *                 @OA\Property (property="sms_template_id", type="integer", example=28),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", description="sms template",
     *         @OA\JsonContent(type="object", ref="#components/schemas/full_sms_template")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionRun()
    {
        GeneralHelper::checkPermission();

        $model = new RunForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put (
     *     path="/sms-template/pause",
     *     summary="Method to pause sending messages",
     *     tags={"SmsTemplateController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"sms_template_id"},
     *                 @OA\Property (property="sms_template_id", type="integer", example=28),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", description="sms template",
     *         @OA\JsonContent(type="object", ref="#components/schemas/full_sms_template")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionPause()
    {
        GeneralHelper::checkPermission();

        $model = new PauseForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Delete  (
     *     path="/sms-template/delete",
     *     summary="Method to delete sms template by id",
     *     tags={"SmsTemplateController"},
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $sms_template = SmsTemplate::findOne($id))
        {
            if ($sms_template->status != SmsTemplate::STATUS['created'])
                throw new BadRequestHttpException(Yii::t('app', 'Извините, этот шаблон sms со статусом не может быть удален'));

            return $sms_template->delete();
        }

        throw new BadRequestHttpException("ID is incorrect");
    }
}