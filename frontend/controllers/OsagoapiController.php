<?php
namespace frontend\controllers;

use common\models\Autotype;
use common\models\Citizenship;
use common\models\NumberDrivers;
use common\models\Osago;
use common\models\OsagoAmount;
use common\models\OsagoPartnerRating;
use common\models\PartnerProduct;
use common\models\Period;
use common\models\Region;
use common\models\Relationship;
use frontend\models\OsagoapiForms\GetNumberDriversForm;
use frontend\models\OsagoapiForms\GetPartners2Form;
use frontend\models\OsagoapiForms\GetPartnersForm;
use frontend\models\OsagoapiForms\GetPaymentSystemsForm;
use frontend\models\OsagoapiForms\GetPeriodsForm;
use Yii;
use yii\filters\VerbFilter;


class OsagoapiController extends BaseController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'autotype' => ['GET'],
                'citizenship' => ['GET'],
                'region' => ['GET'],
                'period' => ['GET'],
                'number-drivers' => ['GET'],
                'relationships' => ['GET'],
                'calc-osago' => ['GET'],
            ]
        ];

        $behaviors['authenticator']['except'] = ["*"];
//        $behaviors['authenticator']['only'] = [];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/osagoapi/get-partners",
     *     summary="partners",
     *     tags={"OsagoapiController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="autonumber", in="query", @OA\Schema (type="string"), description="Mashina davlat raqami"),
     *     @OA\Parameter (name="number_drivers_id", in="query", @OA\Schema (type="integer"), description="checklangan yoki cheklanmagan"),
     *     @OA\Parameter (name="period_id", in="query", @OA\Schema (type="integer"), description="1 yillik yoki 6 oylik"),
     *     @OA\Parameter (name="partner_ability", in="query", @OA\Schema (type="integer"), description="0 yoki 1. osagodagi keyga qarab"),
     *
     *     @OA\Response(response="200", description="partners",
     *           @OA\JsonContent( type="array", @OA\Items(type="object", ref="#/components/schemas/partner_with_accident"))
     *      ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionGetPartners()
    {
        $model = new GetPartnersForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/osagoapi/get-partners2",
     *     summary="partners2",
     *     tags={"OsagoapiController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="autonumber", in="query", @OA\Schema (type="string"), description="Mashina davlat raqami"),
     *     @OA\Parameter (name="insurer_tech_pass_series", in="query", @OA\Schema (type="string"), example="AAF", description="tex passport seria"),
     *     @OA\Parameter (name="insurer_tech_pass_number", in="query", @OA\Schema (type="string"), example="0390422", description="tex passport raqam"),
     *     @OA\Parameter (name="number_drivers_id", in="query", @OA\Schema (type="integer"), description="checklangan yoki cheklanmagan"),
     *     @OA\Parameter (name="period_id", in="query", @OA\Schema (type="integer"), description="1 yillik yoki 6 oylik"),
     *     @OA\Parameter (name="insurer_passport_series", in="query", @OA\Schema (type="string"), example="AA", description="passport seria"),
     *     @OA\Parameter (name="insurer_passport_number", in="query", @OA\Schema (type="string"), example="0390422", description="passport raqam"),
     *     @OA\Parameter (name="insurer_birthday", in="query", @OA\Schema (type="string"), example="25.12.1991", description="owner yoki applicant tug'ilgan sanasi, formt: dd.mm.YY"),
     *     @OA\Parameter (name="super_agent_key", in="query", @OA\Schema (type="string"), example="qwertyuiop", description="super agent ekanligini bildiradigan key"),
     *
     *     @OA\Response(response="200", description="partners",
     *           @OA\JsonContent( type="array", @OA\Items(type="object", ref="#/components/schemas/partner_with_accident"))
     *      ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionGetPartners2()
    {
        $model = new GetPartners2Form();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/osagoapi/get-periods",
     *     summary="periods",
     *     tags={"OsagoapiController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="autonumber", in="query", @OA\Schema (type="string"), description="Mashina davlat raqami"),
     *     @OA\Parameter (name="number_drivers_id", in="query", @OA\Schema (type="integer"), description="checklangan yoki cheklanmagan"),
     *
     *     @OA\Response(response="200", description="periods",
     *           @OA\JsonContent(type="array", @OA\Items(type="object", ref="#/components/schemas/id_name"))
     *      ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionGetPeriods()
    {
        $model = new GetPeriodsForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/osagoapi/get-number-drivers",
     *     summary="number drivers",
     *     tags={"OsagoapiController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="autonumber", in="query", @OA\Schema (type="string"), description="Mashina davlat raqami"),
     *
     *     @OA\Response(response="200", description="number drivers",
     *           @OA\JsonContent(type="array", @OA\Items(type="object", ref="#/components/schemas/id_name"))
     *      ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionGetNumberDrivers()
    {
        $model = new GetNumberDriversForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/osagoapi/relationships",
     *     summary="Method to get all relationships of driver",
     *     tags={"OsagoapiController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Response(
     *         response="200", description="relationships of driver",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#components/schemas/id_name")),
     *     )
     * )
     */
    public function actionRelationships() {
        return  Relationship::find()->select("id, name_$this->lang as name")->asArray()->all();
    }

    /**
     * @OA\Get(
     *     path="/osagoapi/get-payment-systems",
     *     summary="payment systemps",
     *     tags={"OsagoapiController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="autonumber", in="query", @OA\Schema (type="string"), description="Mashina davlat raqami"),
     *
     *     @OA\Response(response="200", description="payment systems",
     *           @OA\JsonContent(type="array", @OA\Items(type="object", ref="#/components/schemas/id_name"))
     *      ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionGetPaymentSystems()
    {
        $model = new GetPaymentSystemsForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionCalcOsago() {
        $data = Yii::$app->request->queryParams;

        if(isset($data['autotype']) && isset($data['region']) && isset($data['period']) && isset($data['citizenship']) && isset($data['number_drivers'])) {
            $autotype = Autotype::findOne([$data['autotype']]);
            $period = Period::findOne([$data['period']]);
            $region = Region::findOne([$data['region']]);
            $citizen = Citizenship::findOne([$data['citizenship']]);
            $number = NumberDrivers::findOne([$data['number_drivers']]);
            $osago = new Osago();

            $osago->autotype_id = $data['autotype'];
            $osago->region_id = $data['region'];
            $osago->period_id = $data['period'];
            $osago->citizenship_id = $data['citizenship'];
            $osago->number_drivers_id = $data['number_drivers'];

            $osago->calc();

            if(!is_null($autotype) && !is_null($period) && !is_null($region) && !is_null($citizen) && !is_null($number)) {
                $amount = OsagoAmount::find()->one();
                $partner_products = PartnerProduct::find()->where(['product_id' => 1])->all();

                $partners = [];
                $price = round((($osago->promo_percent + 100) / 100) * $osago->amount_uzs, 2);

                foreach($partner_products as $p) {
                    $osago_rating = OsagoPartnerRating::find()->where(['partner_id' => $p->partner_id])->one();

                    if(!$osago_rating) {
                        $rating = '';
                        $order_no = 1;
                    } else {
                        $rating = $osago_rating->rating;
                        $order_no = $osago_rating->order_no;
                    }

                    if(!is_null($p->percent)) {
                        $n = [
                            'partner_name' => $p->partner->name,
                            //'partner_img' => $p->partner->image,
                            'price' => $price,
//                            'rating' => $rating,
//                            'order_no' => $order_no,
//                            'star' => $p->star
                        ];
                        $partners[] = $n;
                    }

                }

                return [
                    "result" => true, 
                    "message" => "success",
                    "response" => [
                        "data" => $partners
                    ]
                ];
            } else {
                return [
                    "result" => false, 
                    "message" => "Params are incorrect",
                    "response" => null
                ];
            }


        } else {
            return [
                "result" => false, 
                "message" => "Params are incorrect",
                "response" => null
            ];
        }
    }

}