<?php

namespace frontend\controllers;

use common\models\Currency;
use common\models\Kasko;
use common\models\Promo;
use frontend\models\CascoStepForms\DonwloadPolicyForm;
use frontend\models\CascoStepForms\SetPromoForm;
use frontend\models\CascoStepForms\Step1Form;
use frontend\models\CascoStepForms\Step23Form;
use frontend\models\CascoStepForms\GetautoPersonInfoForm;
use frontend\models\CascoStepForms\Step4Form;
use frontend\models\CascoStepForms\VerifyForm;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class CascoStepController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'step1' => ['POST'],
                'step23' => ['PUT'],
                'step4' => ['POST'],
                'kasko-by-id' => ['GET'],
                'kaskos-of-user' => ['GET'],
                'get-auto-person-info' => ['GET'],
                'set-promo' => ['PUT'],
                'remove-promo' => ['PUT'],
                'delete' => ['DELETE'],
            ]
        ];

        $behaviors['authenticator']['only'] = ["step1", "step4", "kaskos-of-user", "delete", 'step3', 'step23', 'verify'];

        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/casco-step/step1",
     *     summary="create new kasko",
     *     tags={"CascoStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"autocomp_id", "year", "price", "tariff_id"},
     *                 @OA\Property (property="autocomp_id", type="integer", example=24, description="id of auto komplektatsiya"),
     *                 @OA\Property (property="year", type="integer", example=2018, description="year of car manufacture"),
     *                 @OA\Property (property="price", type="integer", example=35000000, description="the amount to be insured (choosen by user, must be minimum 30000000 and maximum price of auto )"),
     *                 @OA\Property (property="tariff_id", type="integer", example=7, description="tariff of kasko, must be one of tariffs which is returned in calc-kasko API"),
     *                 @OA\Property (property="bridge_company_code", type="integer|null", example=2, description="code of company which is buy kasko from our website for users"),
     *                 @OA\Property (property="promo_code", type="string", example="123456", description="code of promo for some disscount"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created new kasko",
     *          @OA\JsonContent( type="object", ref="#components/schemas/kasko")
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
            return $model->save()->getShortArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/casco-step/step23",
     *     summary="step23",
     *     tags={"CascoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"kasko_id", "autonumber", "insurer_tech_pass_series", "insurer_tech_pass_number", "insurer_passport_series", "insurer_passport_number", "insurer_phone", "insurer_address", "begin_date", "kasko_id"},
     *                 @OA\Property (property="kasko_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of kasko which is created by current user"),
     *                 @OA\Property (property="autonumber", type="string", example="01Y195DA", description="vehicle registration number(davlat raqami)"),
     *                 @OA\Property (property="insurer_tech_pass_series", type="string", example="AAF", description="vehicle technical inspection passport series(tex passport seriasi)"),
     *                 @OA\Property (property="insurer_tech_pass_number", type="string", example="0390422", description="vehicle technical inspection passport number(tex passport raqami)"),
     *                 @OA\Property (property="insurer_passport_series", type="string", example="AB", description="insurer passport series"),
     *                 @OA\Property (property="insurer_passport_number", type="string", example="1234567", description="insurer passport passport"),
     *                 @OA\Property (property="insurer_phone", type="string", example="998976543223", description="insurer phone number"),
     *                 @OA\Property (property="insurer_address", type="string", example="Toshkent shahar, Yunusobod tumani, 5-kvartl, 20-dom 30-xonadon", description="insurer address"),
     *                 @OA\Property (property="begin_date", type="date", example="31.01.2022", description="the time the policy comes into force (policy kuchga kiradigan vaqt, format:dd.mm.YY)"),
     *                 @OA\Property (property="insurer_name", type="string", example="Aliyev Vali", description="insurer name"),
     *                 @OA\Property (property="insurer_pinfl", type="string", example="sdf131345134", description="insurer pinfl"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="update kasko which is sended in request",
     *          @OA\JsonContent( type="object", ref="#components/schemas/kasko")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStep23()
    {
        $model = new Step23Form();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getShortArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/casco-step/get-auto-person-info",
     *     summary="get-auto-person-info",
     *     tags={"CascoStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="autonumber", in="query", @OA\Schema (type="string"), description="01Y195DA vehicle registration number(davlat raqami)"),
     *     @OA\Parameter (name="insurer_tech_pass_series", in="query", @OA\Schema (type="string"), description="AAF vehicle technical inspection passport series(tex passport seriasi)"),
     *     @OA\Parameter (name="insurer_tech_pass_number", in="query", @OA\Schema (type="string"), description="0390422 vehicle technical inspection passport number(tex passport raqami)"),
     *
     *     @OA\Response(response="200", description="kasko",
     *          @OA\JsonContent( type="object", ref="#components/schemas/kasko")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     * )
     */
    public function actionGetAutoPersonInfo()
    {
        $model = new GetautoPersonInfoForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->save()->getShortArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post (
     *     path="/casco-step/step4",
     *     summary="step4",
     *     tags={"CascoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"kasko_id", "payment_variant"},
     *                 @OA\Property (property="kasko_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of kasko which is created by current user"),
     *                 @OA\Property (property="payment_variant", type="integer", example="2", description="payment service ['PAYME' => 0,'CLICK' => 1,'PAYZE' => 2, 'ZOOD_PAY' => 3]"),
     *                 @OA\Property (property="card_id", type="integer", example="23", description="send while selecting Payze, get ids from /payze/cards API"),
     *                 @OA\Property (property="first_name", type="string|null", example="Vali", description="agar payment variant zood_pay bo'lsa first_name majburiy"),
     *                 @OA\Property (property="card_number", type="string", example="12345678", description="card number"),
     *                 @OA\Property (property="card_expiry", type="string", example="0223", description="car expire date monthYear format"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="return kasko and checkout url for payment",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property (property="casco", type="object", ref="#components/schemas/kasko"),
     *              @OA\Property (property="checkout", type="string", example="https://click.uz/dfghjklkmjnhbgfghjk"),
     *          )
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStep4()
    {
        $model = new Step4Form();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->send();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post (
     *     path="/casco-step/verify",
     *     summary="verify",
     *     tags={"CascoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"kasko_uuid", "verifycode"},
     *                 @OA\Property (property="kasko_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of kasko which is created by current user"),
     *                 @OA\Property (property="verifycode", type="string", example="123321", description="telefonga yuborilgan kod"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="return kasko",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property (property="casco", type="object", ref="#components/schemas/kasko"),
     *          )
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionVerify()
    {
        $model = new VerifyForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->send();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get (
     *     path="/casco-step/download-policy",
     *     summary="download policy pdf",
     *     tags={"CascoStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="kasko_id", in="query", required=true,  @OA\Schema(type="integer"), example=750, description="id of kasko which is created by current user and ready to download(status =7,8)"),
     *
     *     @OA\Response(response="200", description="the string which the pdf consist of",
     *           @OA\JsonContent(type="string", example="JVBERi0xLjQKJeLjz9MKMyAwIG9iago8PC9UeXBlIC9QYWdlCi9QYXJlbnQgMSAwIFI")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionDownloadPolicy()
    {
        $model = new DonwloadPolicyForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->download();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get (
     *     path="/casco-step/kasko-by-id",
     *     summary="get kasko infos",
     *     tags={"CascoStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="kasko_id", in="query", required=true,  @OA\Schema(type="integer"), example=750, description="id of kasko which is created by current user"),
     *
     *     @OA\Response(response="200", description="update kasko which is sended in request",
     *          @OA\JsonContent( type="object", ref="#components/schemas/kasko")
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionKaskoById()
    {
        $kasko_uuid = -1;
        if (array_key_exists('kasko_uuid', $this->get))
            $kasko_uuid = $this->get['kasko_uuid'];

        $kasko = Kasko::findOne(['uuid' => $kasko_uuid]);
        if ($kasko == null)
            throw new NotFoundHttpException(Yii::t('app', 'Kosko not found'));

        return $kasko->getShortArr();
    }

    /**
     * @OA\Get (
     *     path="/casco-step/kaskos-of-user",
     *     summary="get kasko infos",
     *     tags={"CascoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (ref="#/components/parameters/page"),
     *
     *     @OA\Response(response="200", description="update kasko which is sended in request",
     *          @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#components/schemas/kasko")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *          )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionKaskosOfUser()
    {
        return Kasko::with_pagination([
            'and',
            ['f_user_id' => Yii::$app->user->id],
            ['in', 'status', [
                Kasko::STATUS['payed'],
                Kasko::STATUS['attached'],
                Kasko::STATUS['processed'],
                Kasko::STATUS['policy_generated'],
            ]]
        ]);
    }

    /**
     * @OA\Put(
     *     path="/casco-step/set-promo",
     *     summary="set promo",
     *     tags={"CascoStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"kasko_id", "promo_code"},
     *                 @OA\Property (property="kasko_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of kasko which is created by current user"),
     *                 @OA\Property (property="promo_code", type="string", example="123456", description="code of promo for discount"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="update kasko which is sended in request",
     *          @OA\JsonContent( type="object", ref="#components/schemas/kasko")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionSetPromo()
    {
        $model = new SetPromoForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getShortArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/casco-step/remove-promo",
     *     summary="remove promo",
     *     tags={"CascoStepController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"kasko_id", "promo_code"},
     *                 @OA\Property (property="kasko_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of kasko which is created by current user and have promo"),
     *                 @OA\Property (property="promo_code", type="string", example="123456", description="code of promo which kasko have"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="update kasko which is sended in request",
     *          @OA\JsonContent( type="object", ref="#components/schemas/kasko")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionRemovePromo()
    {
        if (!array_key_exists('kasko_uuid', $this->put))
            throw new BadRequestHttpException('kasko_uuid is required');

        if (!$kasko = Kasko::find()
            ->where(['f_user_id' => Yii::$app->user->id, 'uuid' => $this->put['kasko_uuid']])
            ->andWhere(['not', ['promo_id' => null]])
            ->one()
        )
            throw new BadRequestHttpException('kasko_uuid is incorrect');

        $promo = Promo::findOne($kasko->promo_id);
        $promo->number = $promo->number + 1;
        $promo->save();

        $usd = Currency::getUsdRate();
        $kasko->amount_uzs += $kasko->promo_amount;
        $kasko->amount_usd += round($kasko->promo_amount / $usd, 2);
        $kasko->promo_amount = 0;
        $kasko->promo_percent = 0;
        $kasko->promo_id = null;
        $kasko->save();

        return $kasko->getShortArr();
    }

    /**
     * @OA\Put(
     *     path="/casco-step/delete",
     *     summary="delete kasko",
     *     tags={"CascoStepController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"kasko_id", "promo_code"},
     *                 @OA\Property (property="kasko_uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15", description="uuid of kasko which is created by current user and have promo"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="is kasko deleted success",
     *          @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionDelete($kasko_uuid)
    {
        if (!$kasko = Kasko::findOne(['uuid' => $kasko_uuid, 'f_user_id' => Yii::$app->user->id]))
            throw new BadRequestHttpException('UUID is incorrect');

        if (in_array($kasko->status, [
            Kasko::STATUS['payed'],
            Kasko::STATUS['attached'],
            Kasko::STATUS['processed'],
            Kasko::STATUS['policy_generated'],
        ]))
            throw new BadRequestHttpException(Yii::t("app", "You can not delete Kasko with this status"));

        if (!is_null($kasko->warehouse_id))
        {
            $warehouse = $kasko->warehouse;
            $warehouse->status = "0";
            $warehouse->save();
        }

        $kasko->delete();

        return true;
    }
}