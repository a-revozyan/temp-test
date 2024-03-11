<?php
namespace frontend\controllers;

use common\helpers\GeneralHelper;
use frontend\models\KaskoapiForms\CalcAutoCompPriceForm;
use frontend\models\KaskoapiForms\CalcKaskoForm;
use frontend\models\KaskoapiForms\DataOfPolicyForm;
use frontend\models\KaskoapiForms\GeneratePathOfTariffForm;
use yii\filters\VerbFilter;
use common\models\Autobrand;
use common\models\Automodel;
use common\models\Autocomp;


class KaskoapiController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'autobrand' => ['GET'],
                'automodel' => ['GET'],
                'autocomp' => ['GET'],
                'years' => ['GET'],
                'tariff' => ['GET'],
                'calc-kasko' => ['GET'],
                'kasko-save' => ['POST'],
            ]
        ];

        $behaviors['authenticator']['only'] = ['risk-categories', 'risks'];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/kaskoapi/autobrand",
     *     summary="Method to get all autobrands by sorting with order",
     *     tags={"KaskoapiController"},
     *     @OA\Response(
     *         response="200", description="autobrands",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#components/schemas/id_name")),
     *     )
     * )
     */
    public function actionAutobrand() {
        return Autobrand::find()->select('id, name')->where(['status' => Autobrand::status['active']])->orderBy(['order' => SORT_ASC, 'id' => SORT_ASC])->all();
    }

    /**
     * @OA\Get(
     *     path="/kaskoapi/automodel",
     *     summary="Method to get all or related to one autobrand automodels by sorting with order",
     *     tags={"KaskoapiController"},
     *     @OA\Parameter (name="autobrand_id", in="query", @OA\Schema (type="integer"), example=2),
     *     @OA\Response(
     *         response="200", description="automodels",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#components/schemas/id_name")),
     *     )
     * )
     */
    public function actionAutomodel() {
        $automodels = Automodel::find()->select('id, name')->where(['status' => Automodel::status['active']]);
        if (array_key_exists('autobrand_id', $this->get) and !is_null($this->get['autobrand_id']))
            $automodels = $automodels->andWhere(['autobrand_id' => $this->get['autobrand_id']]);
        
        return $automodels->orderBy(['order' => SORT_ASC, 'id' => SORT_ASC])->asArray()->all();
    }

    /**
     * @OA\Get(
     *     path="/kaskoapi/autocomp",
     *     summary="Method to get all or related to one automodel autocomps  by sorting with name",
     *     tags={"KaskoapiController"},
     *     @OA\Parameter (name="automodel_id", in="query", @OA\Schema (type="integer"), example=3),
     *     @OA\Response(
     *         response="200", description="autocomps",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#components/schemas/id_name")),
     *     )
     * )
     */
    public function actionAutocomp() {
        $autocomps = Autocomp::find()->select('id, name')->where(['status' => Autocomp::status['active']]);
        if (array_key_exists('automodel_id', $this->get) and !is_null($this->get['automodel_id']))
            $autocomps = $autocomps->andWhere(['automodel_id' => $this->get['automodel_id']]);

        return $autocomps->asArray()->orderBy('name')->all();
    }

    /**
     * @OA\Get(
     *     path="/kaskoapi/calc-auto-comp-price",
     *     summary="Method to get price of car by 'komplektatsiya' and produced year",
     *     tags={"KaskoapiController"},
     *     @OA\Parameter (name="autocomp_id", in="query", @OA\Schema (type="integer"), example=26),
     *     @OA\Parameter (name="year", in="query", @OA\Schema (type="integer"), example=2020),
     *     @OA\Response(
     *         response="200", description="price of autocomp",
     *         @OA\JsonContent(type="integer", example=251186000)
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     * )
     */
    public function actionCalcAutoCompPrice()
    {
        $model = new CalcAutoCompPriceForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->calc();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/kaskoapi/years",
     *     summary="Method to get years for select produce of car",
     *     tags={"KaskoapiController"},
     *     @OA\Response(
     *         response="200", description="array of prices",
     *         @OA\JsonContent(type="array", @OA\Items(type="integer"), example="[2016, 2017, 2018, 2019]")
     *     ),
     * )
     */
    public function actionYears() {
        return range(date('Y'), GeneralHelper::env('begin_year_of_kasko'));
    }

    /**
     * @OA\Get(
     *     path="/kaskoapi/calc-kasko",
     *     summary="Method to get tariffs which is suitable for request",
     *     tags={"KaskoapiController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="autocomp_id", in="query", required=true, @OA\Schema (type="integer"), example=190),
     *     @OA\Parameter (name="year", in="query", required=true, @OA\Schema (type="integer"), example=2022),
     *     @OA\Parameter (name="selected_price", in="query", required=true, @OA\Schema (type="integer"), example=100000000),
     *     @OA\Parameter (name="is_islomic", in="query", required=true, @OA\Schema (type="integer"), example=0, description="0 or 1"),
     *     @OA\Parameter (name="car_accessory_ids[]", in="query", @OA\Schema (type="array",  @OA\Items(type="integer")), example="1"),
     *     @OA\Parameter (name="car_accessory_amounts[]", in="query", @OA\Schema (type="array",  @OA\Items(type="integer")), example="60"),
     *     @OA\Response(
     *         response="200", description="tariffs which is suitable for request",
     *         @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *          @OA\Property(property="tariff_id", type="integer", example=7),
     *          @OA\Property(property="partner", type="string", example="APEX Insurance"),
     *          @OA\Property(property="partner_image", type="string", example="uploads/partners/partner1601399768.png"),
     *          @OA\Property(property="tariff_file", type="string|null", example="/uploads/kasko-tariff/7-APEX KASKO 3/APEX KASKO 3_ПРАВИЛА-bBtO6.pdf"),
     *          @OA\Property(property="tariff", type="string", example="APEX KASKO 3"),
     *          @OA\Property(property="risks", type="array", @OA\Items(ref="#components/schemas/kasko_risk_for_calc")),
     *          @OA\Property(property="amount_without_margin", type="integer", example=800000),
     *          @OA\Property(property="amount_usd", type="float", example=72.1),
     *          @OA\Property(property="amount", type="integer", example=800000),
     *          @OA\Property(property="star", type="integer", example=3),
     *          @OA\Property(property="franchise", type="string", example="1 000 000 сум – это часть ущерба, НЕ выплачиваемая страховой ко..."),
     *          @OA\Property(property="only_first_risk", type="string", example=""),
     *          @OA\Property(property="is_conditional", type="integer", example=1),
     *          @OA\Property(property="is_islomic", type="integer", example=0),
     *           )
     *          )
     *     ),
     * )
     */
    public function actionCalcKasko()
    {
        $model = new CalcKaskoForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->calc();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/kaskoapi/pdf-path-of-tariff",
     *     summary="Method to get pdf file of tariff",
     *     tags={"KaskoapiController"},
     *     @OA\Parameter (name="id", in="query", required=true, @OA\Schema (type="integer"), example=8),
     *     @OA\Response(
     *     response=200,
     *     description="pdf file of tariff",
     *     @OA\MediaType(
     *         mediaType="application/pdf"
     *     )
     * ),
     * )
     */
    public function actionPdfPathOfTariff()
    {
        $model = new GeneratePathOfTariffForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->path();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/kaskoapi/data-of-policy",
     *     summary="Method to get policy info when scanning QR",
     *     tags={"KaskoapiController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="kasko_id", in="query", required=true, @OA\Schema (type="integer"), example=565),
     *     @OA\Response(
     *         response="200", description="policy info when scanning QR",
     *         @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *          @OA\Property(property="partner_name", type="string", example="APEX Insurance"),
     *          @OA\Property(property="insurer_passport_series", type="string", example="AA"),
     *          @OA\Property(property="insurer_passport_number", type="string", example="4167316"),
     *          @OA\Property(property="autonumber", type="string", example="01Y800BB"),
     *          @OA\Property(property="tariff_name", type="string", example="APEX KASKO 2"),
     *          @OA\Property(property="insurer_name", type="string", example="SAIDXUDJAYEV GANIXOJA BAXTIYOROVICH"),
     *          @OA\Property(property="autocomp", type="string", example="2.0 AT / 245 л.с"),
     *          @OA\Property(property="year", type="integer", example=2016),
     *          @OA\Property(property="automodel", type="string", example="IS"),
     *          @OA\Property(property="product", type="string", example="kasko"),
     *          @OA\Property(property="begin_date", type="string", example="01/08/2022"),
     *          @OA\Property(property="end_date", type="string", example="31/07/2023"),
     *          @OA\Property(property="tariff_risks", type="array", @OA\Items(ref="#components/schemas/kasko_risk_for_calc")),
     *          @OA\Property(property="tariff_franchise", type="string", example="about franchise"),
     *           )
     *          )
     *     ),
     * )
     */
    public function actionDataOfPolicy()
    {
        $model = new DataOfPolicyForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->data();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

}