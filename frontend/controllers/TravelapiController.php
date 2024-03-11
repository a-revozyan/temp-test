<?php
namespace frontend\controllers;

use common\models\TravelExtraInsurance;
use common\models\TravelPurpose;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\Controller;
use common\models\Country;
use common\models\TravelProgram;
use common\models\Travel;
use common\models\Traveler;
use common\models\Currency;
use yii\httpclient\Client;
use yii\web\UploadedFile;
use yii\helpers\Url;
use common\models\Promo;


class TravelapiController extends Controller
{
    public $access_key = '****';

    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    protected function verbs()
    {
        return [
            'country' => ['GET'],
            'purpose' => ['GET'],
            'calc' => ['GET'],
        ];
    }

    public static function allowedDomains()
    {
        return [
            '*',
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBasicAuth::class,
                HttpBearerAuth::class
            ],
            'only' => ['save', 'save-payfly', 'save-travel', 'payments']
        ];
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        return $behaviors;
    }

    public function checkAccess($key) {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        return true;
    }

    public function actionCountry() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $header = Yii::$app->request->getHeaders();

        //Yii::$app->language = $data['lang'];

        $lang = $header->get('accept-language');

        if($lang) {
            switch($lang) {
                case "ru":
                    $countries = Country::find()->select('id, name_ru as name, code ')->where(['not', ['parent_id' => null]])->asArray()->all();
                    break;
                case "uz":
                    $countries = Country::find()->select('id, name_uz as name, code')->where(['not', ['parent_id' => null]])->asArray()->all();
                    break;
                case "en":
                    $countries = Country::find()->select('id, name_en as name, code')->where(['not', ['parent_id' => null]])->asArray()->all();
                    break;
                default:
                    $countries = Country::find()->select('id, name_uz as name, code')->where(['not', ['parent_id' => null]])->asArray()->all();
            }
        } else {
            $countries = Country::find()->select('id, name_uz as name, code')->where(['not', ['parent_id' => null]])->asArray()->all();
        }

        return [
            'result' => true,
            'message' => null,
            'response' => $countries
        ];        
    }

    public function actionPurpose() {   
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $header = Yii::$app->request->getHeaders();

        $lang = $header->get('accept-language');

        if($lang) {
            switch($lang) {
                case "ru":
                    $purposes = TravelPurpose::find()->select('id, name_ru as name')->asArray()->all();
                    break;
                case "uz":
                    $purposes = TravelPurpose::find()->select('id, name_uz as name')->asArray()->all();
                    break;
                case "en":
                    $purposes = TravelPurpose::find()->select('id, name_en as name')->asArray()->all();
                    break;
                default:
                    $purposes = TravelPurpose::find()->select('id, name_uz as name')->asArray()->all();
            }
        } else {
            $purposes = TravelPurpose::find()->select('id, name_uz as name')->asArray()->all();
        }

        return [
            'result' => true,
            'message' => null,
            'response' => $purposes
        ];     
    }

    public function actionExtraInsurance() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $header = Yii::$app->request->getHeaders();

        $lang = $header->get('accept-language');

        if($lang) {
            switch($lang) {
                case "ru":
                    $extra_insurances = TravelExtraInsurance::find()->select('id, name_ru as name')->asArray()->all();
                    break;
                case "uz":
                    $extra_insurances = TravelExtraInsurance::find()->select('id, name_uz as name')->asArray()->all();
                    break;
                case "en":
                    $extra_insurances = TravelExtraInsurance::find()->select('id, name_en as name')->asArray()->all();
                    break;
                default:
                    $extra_insurances = TravelExtraInsurance::find()->select('id, name_uz as name')->asArray()->all();
            }
        } else {
            $extra_insurances = TravelExtraInsurance::find()->select('id, name_uz as name')->asArray()->all();
        }

        return [
            'result' => true,
            'message' => null,
            'response' => $extra_insurances
        ];
    }

    public static function ceiling($number, $significance = 1)
    {
        return (is_numeric($number) && is_numeric($significance)) ? (ceil($number/$significance)*$significance) : false;
    }

    public function actionCalc() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->queryParams;
        $header = Yii::$app->request->getHeaders();

        //Yii::$app->language = $data['lang'];

        \Yii::$app->language = $header->get('accept-language');
        $model = new Travel();

        $model->purpose_id = $data['purpose_id'];
        $model->isFamily = $data['isFamily'] ? 1 : 0;
        $model->begin_date = $data['begin_date'];
        $model->end_date = $data['end_date'];
        $model->extraInsurances = $data['extraInsurances'];
        $model->travelerBirthdays = $data['travelers'];

        $model->countries = $data['countries'];

        if(!isset($data['purpose_id']) || empty($data['purpose_id'])) {
            return [
                'result' => false,
                'message' => 'Purpose is null',
                'response' => []
            ];
        } else {
            $purpose = TravelPurpose::findOne($data['purpose_id']);

            if(!$purpose) {
                return [
                    'result' => false,
                    'message' => 'Purpose is invalid',
                    'response' => []
                ];
            }
        }

        if(isset($data['extraInsurances']) && !empty($data['extraInsurances'])) {
            $ext = TravelExtraInsurance::findAll($data['extraInsurances']);

            if(!$ext) {
                return [
                    'result' => false,
                    'message' => 'Extra insurances are invalid',
                    'response' => []
                ];
            }
        }

        if(!isset($data['countries']) || empty($data['countries'])) {
            return [
                'result' => false,
                'message' => 'Countries is null',
                'response' => []
            ];
        } else {
            $countries = Country::findAll($data['countries']);

            if(!$countries) {
                return [
                    'result' => false,
                    'message' => 'Countries are invalid',
                    'response' => []
                ];
            }
        }

        $partners = $model->calc();

        usort($partners, function ($item1, $item2) {
            return $item2['amount'] <=> $item1['amount'];
        });

        $result = [];

        foreach($partners as $p) {
            $d = [
                'partner_id' => $p['partner']->id,
                'partner_name' => $p['partner']->name,
                'program_id' => $p['program']->id,
                'program_name' => $p['program']->name,
                'amount_usd' => $p['amount_usd'],
                'amount_uzs' => $p['amount'],
//                'info' => $p['info'],
            ];

            $result[] = $d;
        }

        return $result;
    }

    public function actionSave() {      
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $header = Yii::$app->request->getHeaders();

        if(!$this->checkAccess($header->get('Authorization'))) {
            return [
                "result" => false,
                "message" => "Access denied",
                "response" => null
            ];
        }

        $model = new Travel();

        if(!isset($data['begin_date']) || empty($data['begin_date'])) {
            return [
                'result' => false,
                'message' => 'Begin date is null',
                'response' => []
            ]; 
        }

        if(!isset($data['end_date']) || empty($data['end_date'])) {
            return [
                'result' => false,
                'message' => 'End date is null',
                'response' => []
            ]; 
        }

        if(!isset($data['purpose_id']) || empty($data['purpose_id'])) {
            return [
                'result' => false,
                'message' => 'Purpose is null',
                'response' => []
            ]; 
        } else {
            $purpose = Purpose::findOne($data['purpose_id']);

            if(!$purpose) {
                return [
                    'result' => false,
                    'message' => 'Purpose is invalid',
                    'response' => []
                ]; 
            }
        }

        if(!isset($data['program_id']) || empty($data['program_id'])) {
            return [
                'result' => false,
                'message' => 'Program is null',
                'response' => []
            ]; 
        } else {
            $program = Program::findOne($data['program_id']);

            if(!$program) {
                return [
                    'result' => false,
                    'message' => 'Program is invalid',
                    'response' => []
                ]; 
            }
        }

        if(!isset($data['insurer_name']) || empty($data['insurer_name'])) {
            return [
                'result' => false,
                'message' => 'Insurer name is null',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_birthday']) || empty($data['insurer_birthday'])) {
            return [
                'result' => false,
                'message' => 'Insurer birthday is null',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_pinfl']) || empty($data['insurer_pinfl'])) {
            return [
                'result' => false,
                'message' => 'Insurer pinfl is null',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_passport_series']) || empty($data['insurer_passport_series'])) {
            return [
                'result' => false,
                'message' => 'Insurer passport_series is null',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_passport_number']) || empty($data['insurer_passport_number'])) {
            return [
                'result' => false,
                'message' => 'Insurer passport_number is null',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_phone']) || empty($data['insurer_phone'])) {
            return [
                'result' => false,
                'message' => 'Insurer phone is null',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_address']) || empty($data['insurer_address'])) {
            return [
                'result' => false,
                'message' => 'Insurer address is null',
                'response' => []
            ]; 
        }

        if(!isset($data['is_family'])) {
            return [
                'result' => false,
                'message' => 'Is family is null',
                'response' => []
            ]; 
        }

        if($data['is_family']) {
            $model->isFamily = 1;
        } else {
            $model->isFamily = 0;
        }

        if(!isset($data['countries']) || empty($data['countries'])) {
            return [
                'result' => false,
                'message' => 'Countries is null',
                'response' => []
            ]; 
        }

        if(!isset($data['travelers']) || empty($data['travelers'])) {
            return [
                'result' => false,
                'message' => 'Travelers is null',
                'response' => []
            ]; 
        }

        $model->begin_date = date('Y-m-d', strtotime($data['begin_date']));
        $model->end_date = date('Y-m-d', strtotime($data['end_date']));
        $model->days = abs(round((strtotime($data['begin_date']) - strtotime($data['end_date'])) / 86400)) + 1; 
        $model->purpose_id = $purpose->id;
        $model->program_id = $program->id;
        $model->create_date = date('Y-m-d');
        $model->status = 1;
        $model->created_at = time();

        $model->insurer_name = $data['insurer_name'];
        $model->insurer_pinfl = $data['insurer_pinfl'];
        $model->insurer_passport_series = $data['insurer_passport_series'];
        $model->insurer_passport_number = $data['insurer_passport_number'];
        $model->insurer_phone = $data['insurer_phone'];
        $model->insurer_address = $data['insurer_address'];

        if(isset($data['bot']) && $data['bot']) {
            $model->source = "bot";
        }

        if(isset($data['source'])) {
            $model->source = $data["source"];
        }

        $period_pr = ProgramPeriod::find()
            ->where($model->days . " between from_day and to_day")
            ->andWhere(['program_id' => $model->program_id])
            ->one();

        $usd = Currency::getUsdRate();

        $total = 0;

        if($model->isFamily) {
            $total = $period_pr->amount * $model->days * 2.5;
        } else {
            foreach($data['travelers'] as $tr) {
              $amount = $period_pr->amount * $model->days;

              $age = floor((time() - strtotime($tr['birthday'])) / 31556926);

              if($age <= 65 && ($purpose->id == 3 || $purpose->id == 4)) {
                $total += $amount;
              } else {
                $age_group = AgeGroup::find()
                    ->where($age . " between from_age and to_age")
                    ->one();

                $total += $amount * $age_group->coeff;
              }
      
            }
        }

        $model->amount_usd = round($total * $purpose->coeff, 2);
        $model->amount_uzs = self::ceiling($model->amount_usd * $usd, 1000);

        $countries = Country::find()->where(['id' => $data['countries']])->asArray()->all();
        $is_shengen = false;

        foreach($countries as $c) {
            if($c['shengen']) $is_shengen = true;
        }

        if($is_shengen) {
          $model->end_date = date('Y-m-d', strtotime($model->end_date. ' + 15 day'));
        }

        $model->save();

        foreach($data['countries'] as $c) {
            $tc = new \common\models\TravelCountry;
            $tc->travel_id = $model->getPrimaryKey();
            $tc->country_id = $c;
            $tc->save();
        }

        foreach ($data['travelers'] as $tr) {
            $traveler = new Traveler();
            $traveler->travel_id = $model->getPrimaryKey();
            $traveler->name = $tr['name'];
            $traveler->passport_series = $tr['passport_series'];
            $traveler->passport_number = $tr['passport_number'];
            $traveler->phone = $tr['phone'];
            $traveler->address = $tr['address'];
            $timestamp = strtotime($tr['birthday']);
            $traveler->birthday = date('Y-m-d', $timestamp);
            $traveler->save();
        }

        return [
            'result' => true,
            'message' => 'success',
            'response' => [
                'travel_id' => $model->getPrimaryKey(),
            ]
        ];
                
    }

    public function actionSavePayfly() {      
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $header = Yii::$app->request->getHeaders();

        if(!$this->checkAccess($header->get('Authorization'))) {
            return [
                "result" => false,
                "message" => "Access denied",
                "response" => null
            ];
        }

        $model = new Travel();

        if(!isset($data['begin_date']) || empty($data['begin_date'])) {
            return [
                'result' => false,
                'message' => 'Begin date is null',
                'response' => []
            ]; 
        }

        if(!isset($data['end_date']) || empty($data['end_date'])) {
            return [
                'result' => false,
                'message' => 'End date is null',
                'response' => []
            ]; 
        }

        $purpose = Purpose::findOne(1);

        if(!$purpose) {
            return [
                'result' => false,
                'message' => 'Purpose is invalid',
                'response' => []
            ]; 
        }        

        if(!isset($data['countries']) || empty($data['countries'])) {
            return [
                'result' => false,
                'message' => 'Countries is null',
                'response' => []
            ]; 
        }

        $countries = Country::find()->where(['code' => $data['countries']])->asArray()->all();
        $is_shengen = false;

        foreach($countries as $c) {
            if($c['shengen']) $is_shengen = true;
        }

        $parent_ids = array_unique(array_column($countries, 'parent_id'));


        $exp_countries = ExceptCountries::find()->select('program_id')->where(['country_id' => $parent_ids])->asArray()->all();

        $p_ids = array_unique(array_column($exp_countries, 'program_id'));

        $result_programs = [];

        foreach($p_ids as $pr) {
          $av = true;
          foreach($parent_ids as $par) {
            if(is_null(ExceptCountries::find()->where(['program_id' => $pr, 'country_id' => $par])->one()))
              $av = false;
          }
          if($av) $result_programs[] = $pr;
        }

        $program = Program::find()->where(['id' => $result_programs])->orderBy('id')->one();
        
        if($data['has_covid']) {
            $program = Program::findOne(6);
        }

        if(!$program) {
            return [
                'result' => false,
                'message' => 'Program is invalid',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_name']) || empty($data['insurer_name'])) {
            return [
                'result' => false,
                'message' => 'Insurer name is null',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_birthday']) || empty($data['insurer_birthday'])) {
            return [
                'result' => false,
                'message' => 'Insurer birthday is null',
                'response' => []
            ]; 
        }

        // if(!isset($data['insurer_pinfl']) || empty($data['insurer_pinfl'])) {
        //     return [
        //         'result' => false,
        //         'message' => 'Insurer pinfl is null',
        //         'response' => []
        //     ]; 
        // }

        if(!isset($data['insurer_passport_series']) || empty($data['insurer_passport_series'])) {
            return [
                'result' => false,
                'message' => 'Insurer passport_series is null',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_passport_number']) || empty($data['insurer_passport_number'])) {
            return [
                'result' => false,
                'message' => 'Insurer passport_number is null',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_phone']) || empty($data['insurer_phone'])) {
            return [
                'result' => false,
                'message' => 'Insurer phone is null',
                'response' => []
            ]; 
        }

        $model->isFamily = 0;

        if(!isset($data['travelers']) || empty($data['travelers'])) {
            return [
                'result' => false,
                'message' => 'Travelers is null',
                'response' => []
            ]; 
        }

        $model->begin_date = date('Y-m-d', strtotime($data['begin_date']));
        $model->end_date = date('Y-m-d', strtotime($data['end_date']));
        $model->days = abs(round((strtotime($data['begin_date']) - strtotime($data['end_date'])) / 86400)) + 1; 
        $model->purpose_id = $purpose->id;
        $model->program_id = $program->id;
        $model->create_date = date('Y-m-d');
        $model->status = 1;
        $model->created_at = time();

        $model->insurer_name = $data['insurer_name'];
        $model->insurer_pinfl = $data['insurer_pinfl'];
        $model->insurer_passport_series = $data['insurer_passport_series'];
        $model->insurer_passport_number = $data['insurer_passport_number'];
        $model->insurer_phone = $data['insurer_phone'];

        if(isset($data['source'])) {
            $model->source = $data["source"];
        }

        $period_pr = ProgramPeriod::find()
            ->where($model->days . " between from_day and to_day")
            ->andWhere(['program_id' => $model->program_id])
            ->one();

        $usd = Currency::getUsdRate();

        $total = 0;

        foreach($data['travelers'] as $tr) {
          $amount = $period_pr->amount * $model->days;

          $age = floor((time() - strtotime($tr['birthday'])) / 31556926);

          $age_group = AgeGroup::find()
            ->where($age . " between from_age and to_age")
            ->one();

          $total += $amount * $age_group->coeff;  
        }
        
        $model->amount_usd = round($total * $purpose->coeff, 2);
        $model->amount_uzs = self::ceiling($model->amount_usd * $usd, 1000);

        if($is_shengen && $model->days < 93) {
          $model->end_date = date('Y-m-d', strtotime($model->end_date. ' + 15 day'));
        }

        $model->save();

        foreach($countries as $c) {
            $tc = new \common\models\TravelCountry;
            $tc->travel_id = $model->getPrimaryKey();
            $tc->country_id = $c['id'];
            $tc->save();
        }

        foreach ($data['travelers'] as $tr) {
            $traveler = new Traveler();
            $traveler->travel_id = $model->getPrimaryKey();
            $traveler->name = $tr['name'];
            $traveler->passport_series = $tr['passport_series'];
            $traveler->passport_number = $tr['passport_number'];
            $traveler->phone = $tr['phone'];
            $timestamp = strtotime($tr['birthday']);
            $traveler->birthday = date('Y-m-d', $timestamp);
            $traveler->save();
        }

        return [
            'result' => true,
            'message' => 'success',
            'response' => [
                'travel_id' => $model->getPrimaryKey(),
            ]
        ];
    }
  

    public function actionSaveTravel() {      
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $header = Yii::$app->request->getHeaders();
        
        if(!$this->checkAccess($header->get('Authorization'))) {
            return [
                "result" => false, 
                "message" => "Access denied",
                "response" => null
            ];
        }

        $model = new Travel();

        if(!isset($data['begin_date']) || empty($data['begin_date'])) {
            return [
                'result' => false,
                'message' => 'Begin date is null',
                'response' => []
            ]; 
        }

        if(!isset($data['end_date']) || empty($data['end_date'])) {
            return [
                'result' => false,
                'message' => 'End date is null',
                'response' => []
            ]; 
        }

        $purpose = Purpose::findOne(1);

        if(!$purpose) {
            return [
                'result' => false,
                'message' => 'Purpose is invalid',
                'response' => []
            ]; 
        }        

        if(!isset($data['countries']) || empty($data['countries'])) {
            return [
                'result' => false,
                'message' => 'Countries is null',
                'response' => []
            ]; 
        }

        $countries = Country::find()->where(['code' => $data['countries']])->asArray()->all();
        $is_shengen = false;

        foreach($countries as $c) {
            if($c['shengen']) $is_shengen = true;
        }

        $parent_ids = array_unique(array_column($countries, 'parent_id'));


        $exp_countries = ExceptCountries::find()->select('program_id')->where(['country_id' => $parent_ids])->asArray()->all();

        $p_ids = array_unique(array_column($exp_countries, 'program_id'));

        $result_programs = [];

        foreach($p_ids as $pr) {
          $av = true;
          foreach($parent_ids as $par) {
            if(is_null(ExceptCountries::find()->where(['program_id' => $pr, 'country_id' => $par])->one()))
              $av = false;
          }
          if($av) $result_programs[] = $pr;
        }

        $program = Program::find()->where(['id' => $result_programs])->orderBy('id')->one();
        
        if($data['has_covid']) {
            $program = Program::findOne(6);
        }

        if(!$program) {
            return [
                'result' => false,
                'message' => 'Program is invalid',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_name']) || empty($data['insurer_name'])) {
            return [
                'result' => false,
                'message' => 'Insurer name is null',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_birthday']) || empty($data['insurer_birthday'])) {
            return [
                'result' => false,
                'message' => 'Insurer birthday is null',
                'response' => []
            ]; 
        }

        // if(!isset($data['insurer_pinfl']) || empty($data['insurer_pinfl'])) {
        //     return [
        //         'result' => false,
        //         'message' => 'Insurer pinfl is null',
        //         'response' => []
        //     ]; 
        // }

        if(!isset($data['insurer_passport_series']) || empty($data['insurer_passport_series'])) {
            return [
                'result' => false,
                'message' => 'Insurer passport_series is null',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_passport_number']) || empty($data['insurer_passport_number'])) {
            return [
                'result' => false,
                'message' => 'Insurer passport_number is null',
                'response' => []
            ]; 
        }

        if(!isset($data['insurer_phone']) || empty($data['insurer_phone'])) {
            return [
                'result' => false,
                'message' => 'Insurer phone is null',
                'response' => []
            ]; 
        }

        $model->isFamily = 0;

        if(!isset($data['travelers']) || empty($data['travelers'])) {
            return [
                'result' => false,
                'message' => 'Travelers is null',
                'response' => []
            ]; 
        }

        $model->begin_date = date('Y-m-d', strtotime($data['begin_date']));
        $model->end_date = date('Y-m-d', strtotime($data['end_date']));
        $model->days = abs(round((strtotime($data['begin_date']) - strtotime($data['end_date'])) / 86400)) + 1; 
        $model->purpose_id = $purpose->id;
        $model->program_id = $program->id;
        $model->create_date = date('Y-m-d');
        $model->status = 1;
        $model->created_at = time();

        $model->insurer_name = $data['insurer_name'];
        $model->insurer_pinfl = $data['insurer_pinfl'];
        $model->insurer_passport_series = $data['insurer_passport_series'];
        $model->insurer_passport_number = $data['insurer_passport_number'];
        $model->insurer_phone = $data['insurer_phone'];

        if(isset($data['source'])) {
            $model->source = $data["source"];
        }

        $period_pr = ProgramPeriod::find()
            ->where($model->days . " between from_day and to_day")
            ->andWhere(['program_id' => $model->program_id])
            ->one();

        $usd = Currency::getUsdRate();

        $total = 0;

        foreach($data['travelers'] as $tr) {
          $amount = $period_pr->amount * $model->days;

          $age = floor((time() - strtotime($tr['birthday'])) / 31556926);

          $age_group = AgeGroup::find()
            ->where($age . " between from_age and to_age")
            ->one();

          $total += $amount * $age_group->coeff;  
        }
        
        $model->amount_usd = round($total * $purpose->coeff, 2);
        $model->amount_uzs = self::ceiling($model->amount_usd * $usd, 1000);

        if($is_shengen && $model->days < 93) {
          $model->end_date = date('Y-m-d', strtotime($model->end_date. ' + 15 day'));
        }

        $model->save();

        foreach($countries as $c) {
            $tc = new \common\models\TravelCountry;
            $tc->travel_id = $model->getPrimaryKey();
            $tc->country_id = $c['id'];
            $tc->save();
        }

        foreach ($data['travelers'] as $tr) {
            $traveler = new Traveler();
            $traveler->travel_id = $model->getPrimaryKey();
            $traveler->name = $tr['name'];
            $traveler->passport_series = $tr['passport_series'];
            $traveler->passport_number = $tr['passport_number'];
            $traveler->phone = $tr['phone'];
            $timestamp = strtotime($tr['birthday']);
            $traveler->birthday = date('Y-m-d', $timestamp);
            $traveler->save();
        }

        return [
            'result' => true,
            'message' => 'success',
            'response' => [
                'travel_id' => $model->getPrimaryKey(),
            ]
        ];
    }

     public function actionPayments() {
         \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
         $data = json_decode(Yii::$app->request->getRawBody(), true);
         $header = Yii::$app->request->getHeaders();
      
         if(!$this->checkAccess($header->get('Authorization'))) {
             return [
                 "result" => false, 
                 "message" => "Access denied",
                 "response" => null
             ];
         }
     
         if(!isset($data["order_id"]) || empty($data["order_id"])) {
             return [
                 "result" => false, 
                 "message" => "Params incorrect",
                 "response" => null
             ];
         }

         $model = Travel::findOne([$data["order_id"]]);

         if(!$model) {
             return [
                 "result" => false, 
                 "message" => "Params incorrect",
                 "response" => null
             ];
         }
         if($model->amount_uzs * 100 != $data['amount']) {
             return [
                 "result" => false, 
                 "message" => "Amount is incorrect",
                 "response" => null
             ];
         }
         
        $return_url = "https%3A%2F%2Fa-travel.uz%2Fsite%2Fsuccess?order_id=".$data['order_id'];
        $click_url = 'https://my.click.uz/services/pay?service_id='.Click::SERVICE_ID.'&merchant_id='.Click::MERCHANT_ID.'&amount='.$model->amount_uzs.'&transaction_param=TRAVEL%2F'.$_id.'&return_url='.$return_url.'&merchant_user_id=8931';
        
        $merchant = Merchant::find()->where(['product_code' => 'travel'])->one();
        $payme_url = 'https://checkout.paycom.uz/'.base64_encode('m='.$merchant->merchant_id.';ac.order_id='.$model->id.';a='.$data['amount'].';c='.$return_url);
        
        return [
           "result" => true, 
           "message" => "success",
           "response" => [
              [
                "payment_type" => "click",
                "url" => $click_url
              ],
              [
                "payment_type" => "payme",
                "url" => $payme_url
              ]
           ]
        ];
    }



    // public function actionPayment() {
    //     \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //     $data = json_decode(Yii::$app->request->getRawBody(), true);
    //     $header = Yii::$app->request->getHeaders();
        
    //     if(!$this->checkAccess($header->get('Authorization'))) {
    //         return [
    //             "result" => false, 
    //             "message" => "Access denied",
    //             "response" => null
    //         ];
    //     }
       
    //     if(!isset($data["order_id"]) || empty($data["order_id"])) {
    //         return [
    //             "result" => false, 
    //             "message" => "Params incorrect",
    //             "response" => null
    //         ];
    //     }

    //     $model = Travel::findOne([$data["order_id"]]);

    //     if(!$model) {
    //         return [
    //             "result" => false, 
    //             "message" => "Params incorrect",
    //             "response" => null
    //         ];
    //     }
    //     if($model->amount_uzs * 100 != $data['amount']) {
    //         return [
    //             "result" => false, 
    //             "message" => "Amount is incorrect",
    //             "response" => null
    //         ];
    //     }

    //     $transaction = new Transactions();
    //     $transaction->trans_no = $data['transaction_id'];
    //     $transaction->amount_uzs = $model->amount_uzs;
    //     $transaction->trans_date = time();
    //     $transaction->perform_time = time();
    //     $transaction->cancel_time = 0;
    //     $transaction->status = 2;
    //     $transaction->payment_type = $data['source'];
    //     $transaction->save();
    //     $model->trans_id = $transaction->getPrimaryKey();
    //     $model->save();
    //     $model->setPolicyNumber();
    //     $model->sendIns();

    //     return [

    //         "result" => true, 
    //         "message" => "Success",
    //         "response" => [
    //             "order_id" => $data['order_id']
    //         ]
    //     ];

    // }

    public function actionPolicyPdf() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $header = Yii::$app->request->getHeaders();
        //return $data;
        
        if(!$this->checkAccess($header->get('Authorization'))) {
            return [
                "result" => false, 
                "message" => "Access denied",
                "response" => null
            ];
        }

        if(!isset($data["order_id"]) || empty($data["order_id"])) {
            return [
                "result" => false, 
                "message" => "Params incorrect",
                "response" => null
            ];
        }

        $model = Travel::findOne([$data["order_id"]]);

        if(!$model) {
            return [
                "result" => false, 
                "message" => "Params incorrect",
                "response" => null
            ];
        }

        if($model->trans && $model->trans->status == 2) {
            return [
                "result" => true, 
                "message" => "Success",
                "response" => [
                    "url" => Url::base('https') . '/generate-policy/travel-policy/' . $model->id
                                  ]
            ];
        }

    }
}