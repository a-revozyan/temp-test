<?php

namespace frontend\controllers;

use common\helpers\GeneralHelper;
use common\models\Country;
use common\models\Currency;
use common\models\Kasko;
use common\models\Promo;
use common\models\Travel;
use common\models\TravelExtraInsurance;
use common\models\TravelMultiplePeriod;
use common\models\TravelPurpose;
use frontend\models\TravelStepForms\CalcForm;
use frontend\models\TravelStepForms\DonwloadPolicyForm;
use frontend\models\TravelStepForms\SetPromoForm;
use frontend\models\TravelStepForms\Step1Form;
use frontend\models\TravelStepForms\Step2Form;
use frontend\models\TravelStepForms\Step3Form;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class TravelController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'step1' => ['POST'],
                'step2' => ['PUT'],
                'set-promo' => ['PUT'],
                'step3' => ['POST'],
            ]
        ];

        $behaviors['authenticator']['except'] = [
            "countries",
            "purposes",
            "calc",
            "extra-insurances",
            "available-interval-days",
            "days",
            'travel-extra-policy'
        ];

        return $behaviors;
    }

    public function actionCountries()
    {
        $lang = GeneralHelper::lang_of_local();
        $countries =  Country::find()->where(['not', ['parent_id' => null]])->orderBy(["order" => "asc"])->all();
        return ArrayHelper::toArray($countries, [
            Country::className() => [
                'id',
                'code',
                'name' => function($country) use($lang){
                    return $country["name_$lang"];
                },
                'order',
                'flug' => function ($country) {
                    return "https://flagcdn.com/w160/". strtolower($country['code']) .".png";
                },
            ],
        ]);
    }

    public function actionPurposes()
    {
        $lang = GeneralHelper::lang_of_local();
        return TravelPurpose::find()->select([
            'id',
            "name" => "name_$lang",
        ])->asArray()->all();
    }

    public function actionExtraInsurances()
    {
        $lang = GeneralHelper::lang_of_local();
        return TravelExtraInsurance::find()->select([
            'id',
            "name" => "name_$lang",
        ])->where(['status' => 1])->asArray()->all();
    }

    public function actionAvailableIntervalDays()
    {
        return TravelMultiplePeriod::find()->select([
            "available_interval_days",
        ])->distinct("available_interval_days")->column();
    }

    public function actionDays()
    {
        if (!array_key_exists('available_interval_days', $this->get))
            throw new BadRequestHttpException(Yii::t('app', 'available_interval_days is required'));
        return TravelMultiplePeriod::find()->select([
            "days",
        ])->distinct("days")->where(['available_interval_days' => $this->get['available_interval_days']])->column();
    }


    public function actionCalc()
    {
        $model = new CalcForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->calc();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    //steps -----------------------------------------------
    public function actionStep1()
    {
        $model = new Step1Form();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionStep2()
    {
        $model = new Step2Form();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionSetPromo()
    {
        $model = new SetPromoForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionRemovePromo()
    {
        if (!array_key_exists('travel_id', $this->put))
            throw new BadRequestHttpException('travel_id is required');

        if (!$travel = Travel::find()
            ->where(['f_user_id' => Yii::$app->user->id, 'id' => $this->put['travel_id']])
            ->andWhere(['not', ['promo_id' => null]])
            ->one()
        )
            throw new BadRequestHttpException('travel_id is incorrect');

        $promo = Promo::findOne($travel->promo_id);
        $promo->number = $promo->number + 1;
        $promo->save();

        $usd = Currency::getUsdRate();
        $travel->amount_uzs += $travel->promo_amount;
        $travel->amount_usd += round($travel->promo_amount / $usd, 2);
        $travel->promo_amount = 0;
        $travel->promo_percent = 0;
        $travel->promo_id = null;
        $travel->save();

        return $travel;
    }

    public function actionStep3()
    {
        $model = new Step3Form();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->send();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionTravelById()
    {
        $travel_id = -1;
        if (array_key_exists('travel_id', $this->get))
            $travel_id = $this->get['travel_id'];

        $travel = Travel::findOne(['id' => $travel_id, 'f_user_id' => Yii::$app->user->id]);
        if ($travel == null)
            throw new NotFoundHttpException(Yii::t('app', 'Travel not found'));

        return $travel;
    }

    public function actionDownloadPolicy()
    {
        $model = new DonwloadPolicyForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->download($this);

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionTravelExtraPolicy()
    {
       return Yii::$app->response->sendFile(Yii::getAlias('@frontend') . '/web/uploads/gross_extra_policy/policy.pdf', basename('extra_policy'), ['inline'=>true]);
    }
}