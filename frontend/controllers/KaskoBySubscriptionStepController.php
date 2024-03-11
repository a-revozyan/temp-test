<?php

namespace frontend\controllers;

use common\models\KaskoBySubscription;
use common\models\Partner;
use frontend\models\KaskoBySubscriptionStepForms\ChangeStatusForm;
use frontend\models\KaskoBySubscriptionStepForms\SetPromoForm;
use frontend\models\KaskoBySubscriptionStepForms\Step1Form;
use frontend\models\KaskoBySubscriptionStepForms\Step2Form;
use frontend\models\KaskoBySubscriptionStepForms\Step3Form;
use frontend\models\KaskoBySubscriptionStepForms\Step4Form;
use frontend\models\KaskoBySubscriptionStepForms\Step5Form;
use frontend\models\KaskoBySubscriptionStepForms\Step6Form;
use frontend\models\Searchs\KaskoBySubscriptionSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class KaskoBySubscriptionStepController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'step1' => ['POST'],
                'step2' => ['PUT'],
                'step3' => ['PUT'],
                'step4' => ['PUT'],  //select partner
                'step5' => ['POST'], //enter card info
                'step6' => ['POST'], //enter card verification sms code
            ]
        ];

        $behaviors['authenticator']['only'] = ["step3", "step4", "step5", "step6", "kasko-by-subscription-of-user", "change-status"];

        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/kasko-by-subscription-step/step1",
     *     summary="create new KaskoBySubscription or update KaskoBySubscription which sended by key kasko_by_subscription_uuid",
     *     tags={"KaskoBySubscriptionStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"program_id"},
     *                 @OA\Property (property="program_id", type="integer", example="2"),
     *                 @OA\Property (property="kasko_by_subscription_uuid", type="string", example="1234-dsdfghj-jhg456", description="uuid of kasko by subsciption which you want update"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created or updated kaskoBySubsciption",
     *          @OA\JsonContent( type="object", ref="#components/schemas/full_kasko_by_subscription")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionStep1()
    {
        $model = new Step1Form();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/kasko-by-subscription-step/step2",
     *     summary="step2",
     *     tags={"KaskoBySubscriptionStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"kasko_by_subscription_uuid", "autonumber", "tech_pass_series", "tech_pass_number"},
     *                 @OA\Property (property="kasko_by_subscription_uuid", type="string", example="sdfg4567-sdfg654-567dfghj", description="uuid of kasko by subscription which is created by current user"),
     *                 @OA\Property (property="autonumber", type="string", example="80W124PI"),
     *                 @OA\Property (property="tech_pass_series", type="string", example="AAF"),
     *                 @OA\Property (property="tech_pass_number", type="string", example="1234567"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated kasko by subscription",
     *          @OA\JsonContent( type="object", ref="#components/schemas/full_kasko_by_subscription")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionStep2()
    {
        $model = new Step2Form();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/kasko-by-subscription-step/step3",
     *     summary="step3",
     *     tags={"KaskoBySubscriptionStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"kasko_by_subscription_uuid", "applicant_pass_series", "applicant_pass_number", "applicant_pinfl"},
     *                 @OA\Property (property="kasko_by_subscription_uuid", type="string", example="sdfg4567-sdfg654-567dfghj", description="uuid of kasko by subscription which is created by current user"),
     *                 @OA\Property (property="applicant_name", type="string", example="Jobir Yusupov"),
     *                 @OA\Property (property="applicant_pass_series", type="string", example="AA"),
     *                 @OA\Property (property="applicant_pass_number", type="string", example="1234567"),
     *                 @OA\Property (property="applicant_pinfl", type="string", example="111111111111111"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated kasko by subscription",
     *          @OA\JsonContent( type="object", ref="#components/schemas/full_kasko_by_subscription")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStep3()
    {
        $model = new Step3Form();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/kasko-by-subscription-step/step4",
     *     summary="choose partner",
     *     tags={"KaskoBySubscriptionStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"kasko_by_subscription_uuid", "partner_id"},
     *                 @OA\Property (property="kasko_by_subscription_uuid", type="string", example="sdfg4567-sdfg654-567dfghj", description="uuid of kasko by subscription which is created by current user"),
     *                 @OA\Property (property="partner_id", type="integer", example="1"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated kasko by subscription",
     *          @OA\JsonContent( type="object", ref="#components/schemas/full_kasko_by_subscription")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStep4()
    {
        $model = new Step4Form();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/kasko-by-subscription-step/step5",
     *     summary="entering card info",
     *     tags={"KaskoBySubscriptionStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"card_number", "card_expiry", "kasko_by_subscription_uuid"},
     *                 @OA\Property (property="kasko_by_subscription_uuid", type="string", example="asdf34567-defg5678-jg654", description="uuid of kasko by subsciption which you want to pay"),
     *                 @OA\Property (property="card_number", type="string", example="123456789"),
     *                 @OA\Property (property="card_expiry", type="string", example="11/25"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="saved_card",
     *          @OA\JsonContent( type="object", ref="#components/schemas/full_kasko_by_subscription")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStep5()
    {
        $model = new Step5Form();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/kasko-by-subscription-step/step6",
     *     summary="entering card verification sms code",
     *     tags={"KaskoBySubscriptionStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"kasko_by_subscription_uuid", "saved_card_id", "verifycode"},
     *                 @OA\Property (property="kasko_by_subscription_uuid", type="string", example="asdf34567-defg5678-jg654", description="uuid of kasko by subsciption which you want to pay"),
     *                 @OA\Property (property="saved_card_id", type="integer", example=5),
     *                 @OA\Property (property="verifycode", type="string", example="11/25"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="saved_card",
     *          @OA\JsonContent( type="object",)
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStep6()
    {
        $model = new Step6Form();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/kasko-by-subscription-step/programs",
     *     summary="Method to get all programs of kasko by subscription",
     *     tags={"KaskoBySubscriptionStepController"},
     *     @OA\Response(
     *         response="200", description="programs",
     *          @OA\JsonContent(type="array", @OA\Items(type="object",
     *                @OA\Property(property="id", type="integer", example=9),
     *                @OA\Property(property="name", type="string", example="AVTO LIMIT 1"),
     *                @OA\Property(property="amount_avto", type="integer", example="1000000"),
     *                @OA\Property(property="amount", type="integer", example="60000"),
     *                @OA\Property(property="min_day", type="integer", example="31"),
     *                @OA\Property(property="max_day", type="integer", example="31"),
     *              ),
     *         )
     *     )
     * )
     */
    public function actionPrograms() {
        return KaskoBySubscription::getPrograms();
    }

    /**
     * @OA\Get(
     *     path="/kasko-by-subscription-step/partners",
     *     summary="Method to get all partners of kasko by subscription",
     *     tags={"KaskoBySubscriptionStepController"},
     *     @OA\Response(
     *         response="200", description="programs",
     *          @OA\JsonContent(type="array", @OA\Items(type="object", ref="#components/schemas/partner"))
     *     )
     * )
     */
    public function actionPartners() {
        $ids = [Partner::PARTNER['gross'], Partner::PARTNER['neo']];

        $partners = Partner::find()->where(['id' => $ids])->all();
        usort($partners, function ($a, $b) use ($ids){
            return array_search($a->id, $ids) - array_search($b->id, $ids);
        });
        $_partners = [];
        foreach ($partners as $partner) {
            $_partner = $partner->getForIdNameArr();
            $_partners[] = $_partner;
        }

        return $_partners;
    }

    /**
     * @OA\Get(
     *     path="/kasko-by-subscription-step/kasko-by-subscription-of-user",
     *     summary="Method to get all kasko-by-subscriptions which current user created",
     *     tags={"KaskoBySubscriptionStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Response(
     *         response="200", description="programs",
     *          @OA\JsonContent(type="array", @OA\Items(type="object",  ref="#components/schemas/kasko_by_subscription"),
     *         )
     *     )
     * )
     */
    public function actionKaskoBySubscriptionOfUser() {
        $searchModel = new KaskoBySubscriptionSearch();
        $dataProvider = $searchModel->search([
            'status' => [KaskoBySubscription::STATUS['payed'], KaskoBySubscription::STATUS['canceled']],
            'f_user_id' => Yii::$app->user->id,
        ]);
        return [
            'models' => KaskoBySubscription::getShortClientArrCollection($dataProvider->getModels()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/kasko-by-subscription-step/get-by-id",
     *     summary="get kasko-by-subscription-step which is created current user by id",
     *     tags={"KaskoBySubscriptionStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="kbs_uuid", in="query", @OA\Schema (type="string"), example="jhgfd23456-0987sdfg-yt5678"),
     *     @OA\Response(
     *         response="200", description="kasko-by-subscription object",
     *         @OA\JsonContent(type="object",  ref="#components/schemas/full_kasko_by_subscription")
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionGetById(string $kbs_uuid)
    {
        return $this->getByID($kbs_uuid)->getFullClientArr();
    }

    /**
     * @OA\Put(
     *     path="/kasko-by-subscription-step/change-status",
     *     summary="change status",
     *     tags={"KaskoBySubscriptionStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"kasko_by_subscription_uuid", "status"},
     *                 @OA\Property (property="kasko_by_subscription_uuid", type="string", example="sdfg4567-sdfg654-567dfghj", description="uuid of kasko by subscription which is created by current user"),
     *                 @OA\Property (property="status", type="integer", example="6 => prodoljiy straxovku, 7 => Otmenit"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated kasko by subscription",
     *          @OA\JsonContent( type="object", ref="#components/schemas/full_kasko_by_subscription")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionChangeStatus()
    {
        $model = new ChangeStatusForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/kasko-by-subscription-step/set-promo",
     *     summary="promo code ni ishlatish",
     *     tags={"KaskoBySubscriptionStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"kbs_uuid"},
     *                 @OA\Property (property="kbs_uuid", type="string", example="fghj7654-hc345676543-3456hjkh", description="uuid of kbs which is created by current user"),
     *                 @OA\Property (property="promo_code", type="string", example="trikchilik", description="promo code"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated osago",
     *          @OA\JsonContent( type="object", ref="#components/schemas/full_kasko_by_subscription")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSetPromo()
    {
        $model = new SetPromoForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullClientArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    private function getById($kbs_uuid)
    {
        if (!$kbs = KaskoBySubscription::findOne(['uuid' => $kbs_uuid]))
            throw new NotFoundHttpException('kbs_uuid not found');

        return $kbs;
    }
}