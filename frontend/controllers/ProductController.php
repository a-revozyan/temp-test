<?php
namespace frontend\controllers;

use common\helpers\GeneralHelper;
use common\helpers\PdfHelper;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use kartik\mpdf\Pdf;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\httpclient\Client;
use yii\base\Model;
use yii\web\UploadedFile;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\models\Osago;
use common\models\OsagoPartnerRating;
use common\models\OsagoAmount;
use common\models\OsagoDriver;
use common\models\PartnerProduct;
use common\models\Travel;
use common\models\Traveler;
use common\models\TravelProgramPeriod;
use common\models\Partner;
use common\models\Currency;
use common\models\Country;
use common\models\TravelProgram;
use common\models\TravelProgramCountry;
use common\models\TravelPurpose;
use common\models\TravelGroupType;
use common\models\TravelAgeGroup;
use common\models\TravelPartnerGroupType;
use common\models\TravelPartnerPurpose;
use common\models\TravelProgramRisk;
use common\models\TravelPartnerExtraInsurance;
use common\models\TravelPartnerInfo;
use common\models\TravelExtraInsuranceBind;
use common\models\Kasko;
use common\models\KaskoTariff;
use common\models\KaskoRisk;
use common\models\KaskoTariffRisk;
use common\models\Autobrand;
use common\models\Automodel;
use common\models\Autocomp;
use common\models\TravelCountry;
use common\models\Transaction;
use frontend\models\TravelParent;
use frontend\models\Child;
use common\models\AccidentPartnerProgram;
use common\models\Accident;
use common\models\AccidentInsurer;
use common\models\Promo;

/**
 * Site controller
 */
class ProductController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */

    public function actionOsagoCalc()
    {
        $this->layout = 'osago';

        $model = new Osago();

        if ($model->load(Yii::$app->request->post())) {
            $session = Yii::$app->session;
            $session->open();

            if($model->citizenship_id == 3) $model->region_id = 2;

            $session->set('osago-autotype_id', $model->autotype_id);
            $session->set('osago-period_id', $model->period_id);
            $session->set('osago-region_id', $model->region_id);
            $session->set('osago-citizenship_id', $model->citizenship_id);
            $session->set('osago-number_drivers_id', $model->number_drivers_id);
            $session->set('osago-partner_id', $model->partner_id);
            $session->set('osago-promo_code', $model->promo_code);

            $session->close();

            return $this->redirect(['osago-form']);
        }
        
        return $this->render('osago-calc', [
            'model' => $model
        ]);
    }  

    public function actionCheckOsagoSession() {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

      $session = Yii::$app->session;
      $session->open();

      return [
        'autotype_id' => $session->get('osago-autotype_id'),
        'period_id' => $session->get('osago-period_id'),
        'region_id' => $session->get('osago-region_id'),
        'citizenship_id' => $session->get('osago-citizenship_id'),
        'number_drivers_id' => $session->get('osago-number_drivers_id')
      ];
    }

    public function actionOsagoForm()
    {
        $this->layout = 'osago';

        $model = new Osago();
        
        $drivers = [new OsagoDriver()];

        $session = Yii::$app->session;

        if($session->isActive && $session->has('osago-autotype_id')) {
            $model->autotype_id = $session->get('osago-autotype_id');
            $model->period_id = $session->get('osago-period_id');
            $model->region_id = $session->get('osago-region_id');
            $model->citizenship_id = $session->get('osago-citizenship_id');
            $model->number_drivers_id = $session->get('osago-number_drivers_id');
            $model->partner_id = $session->get('osago-partner_id');
            $model->calc();

            $promo = Promo::find()->where(['code' => $session->get('osago-promo_code')])->one();

            if($promo) {
              $model->promo_id = $promo->id;
              $model->promo_percent = $promo->percent;
            } else {
              $model->promo_percent = 0;
            }


            $usd = Currency::getUsdRate();

            $percent = PartnerProduct::find()->where(['partner_id' => $model->partner_id, 'product_id' => 1])->one();
            $model->promo_amount = $model->amount_uzs * $model->promo_percent/100;
            $model->amount_uzs = ((100 + $model->promo_percent) / 100) * $model->amount_uzs;
            $model->amount_usd = round($model->amount_uzs/$usd, 2);
        } else {
            return $this->redirect(['osago-calc']);
        }


        if ($model->load(Yii::$app->request->post())) {
            $model->status = 1;
            $model->created_at = time();

            $model->passFile = UploadedFile::getInstance($model, 'passFile');
            $model->techPassFileFront = UploadedFile::getInstance($model, 'techPassFileFront');
            $model->techPassFileBack = UploadedFile::getInstance($model, 'techPassFileBack');

            $model->save();

            if($model->passFile) {
                $model->passport_file = 'passport_'.$model->id.'.'.$model->passFile->extension;
                $model->save();
                $model->uploadPass();
            }


            $model->passFile = null;
            $model->techPassFileFront = UploadedFile::getInstance($model, 'techPassFileFront');

            if($model->techPassFileFront) {
                $model->tech_passport_file_front = 'tech_passport_front_'.$model->id.'.'.$model->techPassFileFront->extension;
                $model->save();
                $model->uploadTechPassFront();
            }

            $model->techPassFileFront = null;
            $model->techPassFileBack = UploadedFile::getInstance($model, 'techPassFileBack');

            if($model->techPassFileBack) {
                $model->tech_passport_file_back = 'tech_passport_back_'.$model->id.'.'.$model->techPassFileBack->extension;
                $model->save();
                $model->uploadTechPassBack();
            }

            $count = count(Yii::$app->request->post('OsagoDriver'));
            $drivers = [];

            for($i = 0; $i < $count; $i++) {
              $drivers[] = new OsagoDriver();
            }

            Model::loadMultiple($drivers, Yii::$app->request->post());

            foreach($drivers as $i => $n) {
              $n->osago_id = $model->getPrimaryKey();
              $n->save();

              $n->licenseFile = UploadedFile::getInstance($n, "[$i]licenseFile");

              if($n->licenseFile) {
                $n->license_file = 'license_'.$n->id.'.'.$n->licenseFile->extension;
                $n->save();
                $n->uploadLicense();
              }
            }

            //var_dump($model);
            $session->destroy();
            $session->close();
            
            return $this->redirect(['osago-payment', 'id' => base64_encode($model->getPrimaryKey())]);
        }
        
        return $this->render('osago-form', [
            'model' => $model,
            'drivers' => $drivers
        ]);
    }  

    public function actionOsagoPayment($id)
    {
        $this->layout = 'osago';

        $_id = base64_decode($id);

        $model = Osago::findOne([$_id]);
        $amount = $model->amount_uzs * 100;
        
        $payme = new \frontend\models\PaymeForm();
        $verify = false;
        $sent = false;
        $phone = '';
        $verifyPhone = '';
        $verifyWait = 0;
        $token = "";
        
        if (Yii::$app->request->isPost && $payme->load(Yii::$app->request->post())) {
            $client = new Client();
            if(!$payme->phone && ($payme->number && $payme->expiry)) :
                if(Yii::$app->request->post('verify') == 1 && !is_null($payme->verifyCode)) {
                    $response2 = $client->createRequest()
                                   ->setFormat(Client::FORMAT_JSON)
                                   ->setMethod('POST')
                                   ->setUrl('https://checkout.paycom.uz/api')
                                   ->addHeaders(['X-Auth' => '6076e20ae409214cf720b105', "content-type" => "application/json"])
                                   ->setData(["jsonrpc" => "2.0", "method" => "cards.verify", "id" => $_id, "params" => [
                                          "token" => Yii::$app->request->post('token'),
                                          "code" => $payme->verifyCode
                                      ],
                                   ])
                                   ->send();
                        if ($response2->isOk) {
                            $data2 = json_decode($response2->getContent(), true);
                            if(isset($data2['error'])) {
                              Yii::$app->session->setFlash('danger', $data2['error']['message'] . " code=" . $data2['error']['code']);
                            }
                            if(isset($data2['result']['card']['verify']) && $data2['result']['card']['verify']) {
                                  $token = $data2['result']['card']['token'];

                                  $response3 = $client->createRequest()
                                       ->setFormat(Client::FORMAT_JSON)
                                       ->setMethod('POST')
                                       ->setUrl('https://checkout.paycom.uz/api')
                                       ->addHeaders(['X-Auth' => '6076e20ae409214cf720b105:xtfu?nmWNIJ4IccVxA5oj7vcs2P?002TzP9z', "content-type" => "application/json"])
                                       ->setData(["jsonrpc" => "2.0", "method" => "receipts.create", "id" => $_id, "params" => [
                                              "amount" => $amount,
                                              "account" => ["order_id" => $_id],
                                          ],
                                       ])
                                       ->send();
                                  if ($response3->isOk) {
                                      $data3 = json_decode($response3->getContent(), true);
                                      //var_dump($data3);
                                      if(isset($data3['error'])) {
                                        Yii::$app->session->setFlash('danger', $data3['error']['message'] . " code=" . $data3['error']['code']);
                                      }
                                            //var_dump($data3);
                                      if(isset($data3['result']['receipt']['_id'])) {
                                        /*$trans = Transactions::find()->where(['trans_no' => $data3['result']['receipt']['_id']]);
                                        $trans->token = $token;
                                        $trans->save();*/
                                        $response4 = $client->createRequest()
                                         ->setFormat(Client::FORMAT_JSON)
                                         ->setMethod('POST')
                                         ->setUrl('https://checkout.paycom.uz/api')
                                         ->addHeaders(['X-Auth' => '6076e20ae409214cf720b105:xtfu?nmWNIJ4IccVxA5oj7vcs2P?002TzP9z', "content-type" => "application/json"])
                                         ->setData(["jsonrpc" => "2.0", "method" => "receipts.pay", "id" => $_id, "params" => [
                                                "id" => $data3['result']['receipt']['_id'],
                                                "token" => $token
                                            ],
                                         ])
                                         ->send();

                                         if ($response4->isOk) {
                                          $data4 = json_decode($response4->getContent(), true);
                                            if(isset($data4['error'])) {
                                              Yii::$app->session->setFlash('danger', $data4['error']['message'] . " code=" . $data4['error']['code']);
                                            }
                                            if(isset($data4['receipt']['state']) && $data4['receipt']['state'] == 4) {
                                                return $this->redirect(['osago-complete', 'id' => $id]);
                                            }
                                         }
                                      }

                                  }
                                }
                        }
                        else {
                            $data = array();
                        }
                } else {
                    $response1 = $client->createRequest()
                                       ->setFormat(Client::FORMAT_JSON)
                                       ->setMethod('POST')
                                       ->setUrl('https://checkout.paycom.uz/api')
                                       ->addHeaders(['X-Auth' => '6076e20ae409214cf720b105', "content-type" => "application/json"])
                                       ->setData(["jsonrpc" => "2.0", "method" => "cards.create", "id" => $_id, "params" => [
                                              "card" => ["number" => $payme->number, "expire" => $payme->expiry],
                                              "amount" => $amount,  
                                              //"account" => ["order_id" => $id],
                                              "save" => true
                                          ],
                                       ])
                                       ->send();
                    if ($response1->isOk) {
                        $data = json_decode($response1->getContent(), true);
                        if(isset($data['error'])) {
                          Yii::$app->session->setFlash('danger', $data['error']['message'] . " code=" . $data['error']['code']);
                        }
                        //var_dump($data);
                        if(isset($data['result']) && isset($data['result']['card']) && isset($data['result']['card']['recurrent'])) {
                            $response2 = $client->createRequest()
                                       ->setFormat(Client::FORMAT_JSON)
                                       ->setMethod('POST')
                                       ->setUrl('https://checkout.paycom.uz/api')
                                       ->addHeaders(['X-Auth' => '6076e20ae409214cf720b105', "content-type" => "application/json"])
                                       ->setData(["jsonrpc" => "2.0", "method" => "cards.get_verify_code", "id" => $_id, "params" => [
                                              "token" => $data['result']['card']['token']
                                          ],
                                       ])
                                       ->send();
                            if ($response2->isOk) {
                                $data2 = json_decode($response2->getContent(), true);
                                if(isset($data2['error'])) {
                                  Yii::$app->session->setFlash('danger', $data2['error']['message'] . " code=" . $data2['error']['code']);
                                }
                                //var_dump($data2);

                                if(isset($data['result']) && $data2['result']['sent']) {
                                    $verify = true;
                                    $verifyPhone = $data2['result']['phone'];
                                    $verifyWait = $data2['result']['wait'];
                                    $token = $data['result']['card']['token'];
                                }
                            }
                            else {
                                $data = array();
                            }
                        } else {
                            $data = array();
                        }
                    }
                }
            elseif($payme->phone && (!$payme->number && !$payme->expiry)):

                $response3 = $client->createRequest()
                    ->setFormat(Client::FORMAT_JSON)
                    ->setMethod('POST')
                    ->setUrl('https://checkout.paycom.uz/api')
                    ->addHeaders(['X-Auth' => '6076e20ae409214cf720b105:xtfu?nmWNIJ4IccVxA5oj7vcs2P?002TzP9z', "content-type" => "application/json"])
                    ->setData(["jsonrpc" => "2.0", "method" => "receipts.create", "id" => $_id, "params" => [
                          "amount" => $amount,
                          "account" => ["order_id" => $_id],
                      ],
                    ])
                    ->send();
                if ($response3->isOk) {
                    $data3 = json_decode($response3->getContent(), true);
                    if(isset($data3['error'])) {
                      Yii::$app->session->setFlash('danger', $data3['error']['message'] . " code=" . $data3['error']['code']);
                    }
                    if(isset($data3['result']['receipt']['_id'])) {
                        $response4 = $client->createRequest()
                            ->setFormat(Client::FORMAT_JSON)
                            ->setMethod('POST')
                            ->setUrl('https://checkout.paycom.uz/api')
                            ->addHeaders(['X-Auth' => '6076e20ae409214cf720b105:xtfu?nmWNIJ4IccVxA5oj7vcs2P?002TzP9z', "content-type" => "application/json"])
                            ->setData(["jsonrpc" => "2.0", "method" => "receipts.send", "id" => $_id, "params" => [
                                "id" => $data3['result']['receipt']['_id'],
                                "phone" => $payme->phone
                                ],
                            ])
                            ->send();
                        if ($response4->isOk) {

                            $data4 = json_decode($response4->getContent(), true);
                            if(isset($data4['error'])) {
                                Yii::$app->session->setFlash('danger', $data4['error']['message'] . " code=" . $data4['error']['code']);
                            }
                            if(isset($data4['result']['success'])) {
                                $sent = true;
                                $phone = $payme->phone;
                            }
                        } else {
                            var_dump($data3);
                        }
                    }
                }
            endif;
        }

        $model = Osago::findOne([$_id]);
        $trans = Transaction::findOne([$model->trans_id]);

        if($trans && $trans->status == 2) {
            return $this->redirect(['osago-complete', 'id' => $id]);
        }

        return $this->render('payme', [
            'amount' => $model->amount_uzs,
            'id' => $id,
            'payme' => $payme,
            'verify' => $verify,
            'verifyPhone' => $verifyPhone,
            'verifyWait' => $verifyWait,
            'token' => $token,
            'sent' => $sent,
            'phone' => $phone,
            'url' => 'product/osago-complete',
            'product' => 'osago'
        ]);
    }

    public function actionOsagoComplete($id)
    {
        $this->layout = 'osago';

        $_id = base64_decode($id);

        $model = Osago::findOne([$_id]);

        if(!$model) {
            Yii::$app->session->setFlash('danger', 'URL is incorrect');
            return $this->redirect(['osago-calc']);
        }

        $status = 0;

        $trans = Transaction::findOne([$model->trans_id]);

        if(is_null($trans) || $trans->status != 2) {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Policy is not paid'));
            return $this->redirect(['osago-payment', 'id' => $id]);
        } else {
            $status = $trans->status;
        }

        return $this->render('complete', [
            'id' => $id,
            'model' => $model,
            'status' => $status,
            'product' => 'osago'
        ]);
    }

    public function actionGetOsagoAmounts() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $osago = new Osago();

        $osago->autotype_id = Yii::$app->request->get('autotype');
        $osago->region_id = Yii::$app->request->get('region');
        $osago->period_id = Yii::$app->request->get('period');
        $osago->citizenship_id = Yii::$app->request->get('citizenship');
        $osago->number_drivers_id = Yii::$app->request->get('number');
        $osago->promo_code = Yii::$app->request->get('promo');

        $osago->calc();

        $result = '';

        $amount = OsagoAmount::find()->one();
        $partner_products = PartnerProduct::find()->where(['product_id' => 1])->all();

        $partners = [];

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
                'partner_id' => $p->partner_id,
                'partner_img' => $p->partner->image,
                'percent' => $p->percent,
                'rating' => $rating,
                'order_no' => $order_no,
                'star' => $p->star
              ];
              $partners[] = $n;
            }           

        }

        usort($partners, function ($item1, $item2) {
            return $item1['order_no'] <=> $item2['order_no'];
        });

        $promo = Promo::find()->where(['code' => $osago->promo_code])->one();

        if($promo) {
            $osago->promo_id = $promo->id;
            $osago->promo_percent = $promo->percent;
        } else {
            $osago->promo_percent = 0;
        }

        if($osago->amount_uzs) {
            foreach($partners as $p) {
                $price = (($osago->promo_percent + 100) / 100) * $osago->amount_uzs;

                $result .= "<div class='partner-results box bg-white shadow-sm p-2 border-bottom'>
                              <div class='row'>
                                <div class='col text-right pr-4'><span title='".Yii::t('app', 'yozuv')."'>";

                for($i = 0; $i < $p['star']; $i++) {
                  $result .= "<img width='15px' src='/img/star.png' />";
                }

                $result .= " </span></div></div><div class='row'>
                                    <div class='col-md-3 col-6 p-4'>
                                      <img src='/uploads/partners/".$p['partner_img']."' class='w-75' />
                                    </div>";

                if($osago->promo_percent < 0 && $osago->amount_uzs != $price) {
                  $result .= "<div class='col-md-2 col-6 pt-2 pl-lg-4'><h5 class='pt-0'><del>".number_format($osago->amount_uzs,0,","," ") .' '. Yii::t('app', 'сум')."</del></h5>";
                } else {
                  $result .= "<div class='col-md-2 col-6 pt-2 p-lg-4'>";
                }

                $result .= "
                                        <h5 class='pt-3'>".number_format($price,0,","," ") .' '. Yii::t('app', 'сум')."</h5>
                                    </div>
                                    <div class='col-md-2 col-6 p-0 pl-4 p-lg-4'>
                                        <h5 class='pt-3'>".$p['rating']."</h5>
                                    </div>
                                    <div class='col-md-3 col-6 p-lg-4'>
                                        <h5 class='pt-3'>".number_format($amount->insurance_amount,0,","," ") .' '. Yii::t('app', 'сум')."</h5>
                                    </div>
                                    <div class='col-md-2 col-12 text-center' style='margin: auto;'>
                                        ". Html::submitButton(Yii::t('app', 'Купить'), ['name' => 'Osago[partner_id]', 'value' => $p['partner_id'], 'class' => 'mybtn page-btn'])."
                                    </div>
                                </div>
                            </div>";
            }
        } else {
            return null;
        }
        

        return [
            'html' => $result,
        ];
    }

    public function actionGetTechPassData() {
        $tech_series = Yii::$app->request->get('tech_series');
        $tech_number = Yii::$app->request->get('tech_number');
        $autonumber = Yii::$app->request->get('autonumber');

        return 1;

        $client = new Client();
        $url = 'http://osago.gross.uz/api_e_osgo_uz.php';

        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('POST')
            ->setUrl($url)
            ->addHeaders(["content-type" => "application/json"])
            ->setData(["send_id" => 1, "request" => ["techPassportSeria" => strtoupper($tech_series), "techPassportNumber" => $tech_number, "govNumber" => strtoupper($autonumber)]])
            ->send();
        

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($response->isOk) {
            $data = json_decode($response->getContent(), true);
            $result = [];
            if(isset($data['result']) && $data['error'] == 0) {
                if(isset($data['result']['owner'])) $result['name'] = $data['result']['owner'];
                if(isset($data['result']['pinfl'])) $result['pinfl'] = $data['result']['pinfl'];

                return $result;
            }
        }

        return 1;
    }

    public function actionGetPinfl() {
      return null;

        $pass_series = Yii::$app->request->get('pass_series');
        $pass_number = Yii::$app->request->get('pass_number');

        $client = new Client();
        $url = 'http://osago.gross.uz/api_e_osgo_uz.php';

        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('POST')
            ->setUrl($url)
            ->setData(["send_id" => 2, "request" => ["passportSeries" => strtoupper($pass_series), "passportNumber" => $pass_number]])
            ->send();
        

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($response->isOk) {
            $data = json_decode($response->getContent(), true);
            if(isset($data['result']) && $data['error'] == 0) {
                $result = [];
                if(isset($data['result']['DriverPersonInfo'])) {
                  $result['pinfl'] = '';
                  $result['name'] = '';
                  $result['first_name'] = '';
                  $result['last_name'] = '';
                  $result['middle_name'] = '';
                  $result['address'] = '';
                  $result['birthday'] = '';

                  if(isset($data['result']['DriverPersonInfo']['pinfl'])) $result['pinfl'] = $data['result']['DriverPersonInfo']['pinfl'];

                  if(isset($data['result']['DriverPersonInfo']['lastNameLatin'])) {
                    $result['name'] .= $data['result']['DriverPersonInfo']['lastNameLatin'];
                    $result['last_name'] = $data['result']['DriverPersonInfo']['lastNameLatin'];
                  }

                  if(isset($data['result']['DriverPersonInfo']['firstNameLatin'])) {
                    $result['name'] .= ' ' . $data['result']['DriverPersonInfo']['firstNameLatin'];
                    $result['first_name'] = $data['result']['DriverPersonInfo']['firstNameLatin'];
                  } 

                  if(isset($data['result']['DriverPersonInfo']['middleNameLatin'])) {
                    $result['name'] .= ' ' . $data['result']['DriverPersonInfo']['middleNameLatin'];
                    $result['middle_name'] = $data['result']['DriverPersonInfo']['middleNameLatin'];
                  } 

                  if(isset($data['result']['DriverPersonInfo']['address'])) $result['address'] = $data['result']['DriverPersonInfo']['address'];

                  if(isset($data['result']['DriverPersonInfo']['birthDate'])) $result['birthday'] = date('d.m.Y', strtotime($data['result']['DriverPersonInfo']['birthDate']));
                }

                if(isset($data['result']['DriverInfo'])) {
                  $result['licenseNumber'] = '';
                  $result['licenseSeria'] = '';
                  $result['owner_name'] = '';
                  if(isset($data['result']['DriverInfo']['licenseNumber'])) $result['licenseNumber'] = $data['result']['DriverInfo']['licenseNumber'];
                  if(isset($data['result']['DriverInfo']['licenseSeria'])) $result['licenseSeria'] = $data['result']['DriverInfo']['licenseSeria'];
                  if(isset($data['result']['DriverInfo']['pOwner'])) $result['owner_name'] = $data['result']['DriverInfo']['pOwner'];
                }

                return $result;
            }
        }

        return null;
    }

    public function actionGetPassData() {
      return null;
      
        $pass_series = Yii::$app->request->get('pass_series');
        $pass_number = Yii::$app->request->get('pass_number');
        $pinfl = Yii::$app->request->get('pinfl');

        $client = new Client();
        $url = 'http://osago.gross.uz/api_e_osgo_uz.php';

        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('POST')
            ->setUrl($url)
            ->setData(["send_id" => 2, "request" => ["pinfl" => $pinfl, "passportSeries" => strtoupper($pass_series), "passportNumber" => $pass_number]])
            ->send();        

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($response->isOk) {
            $data = json_decode($response->getContent(), true);
            if(isset($data['result']) && $data['error'] == 0) {
                $result = [];
                if(isset($data['result']['DriverPersonInfo'])) {
                  $result['name'] = '';
                  $result['first_name'] = '';
                  $result['last_name'] = '';
                  $result['middle_name'] = '';
                  $result['address'] = '';
                  $result['birthday'] = '';
                  if(isset($data['result']['DriverPersonInfo']['lastNameLatin'])) {
                    $result['name'] .= $data['result']['DriverPersonInfo']['lastNameLatin'];
                    $result['last_name'] = $data['result']['DriverPersonInfo']['lastNameLatin'];
                  }

                  if(isset($data['result']['DriverPersonInfo']['firstNameLatin'])) {
                    $result['name'] .= ' ' . $data['result']['DriverPersonInfo']['firstNameLatin'];
                    $result['first_name'] = $data['result']['DriverPersonInfo']['firstNameLatin'];
                  } 

                  if(isset($data['result']['DriverPersonInfo']['middleNameLatin'])) {
                    $result['name'] .= ' ' . $data['result']['DriverPersonInfo']['middleNameLatin'];
                    $result['middle_name'] = $data['result']['DriverPersonInfo']['middleNameLatin'];
                  } 

                  if(isset($data['result']['DriverPersonInfo']['address'])) $result['address'] = $data['result']['DriverPersonInfo']['address'];
                  if(isset($data['result']['DriverPersonInfo']['birthDate'])) $result['birthday'] = date('d.m.Y', strtotime($data['result']['DriverPersonInfo']['birthDate']));
                }

                if(isset($data['result']['DriverInfo'])) {
                  $result['licenseNumber'] = '';
                  $result['licenseSeria'] = '';
                  $result['owner_name'] = '';
                  if(isset($data['result']['DriverInfo']['licenseNumber'])) $result['licenseNumber'] = $data['result']['DriverInfo']['licenseNumber'];
                  if(isset($data['result']['DriverInfo']['licenseSeria'])) $result['licenseSeria'] = $data['result']['DriverInfo']['licenseSeria'];
                  if(isset($data['result']['DriverInfo']['pOwner'])) $result['owner_name'] = $data['result']['DriverInfo']['pOwner'];
                }

                return $result;
            }
        }

        return null;
    }

    public function actionKaskoCalc()
    {
        $this->layout = 'kasko';

        $model = new Kasko();

        if ($model->load(Yii::$app->request->post())) {
            $session = Yii::$app->session;
            $session->open();

            $session->set('kasko-autobrand_id', $model->autobrand_id);
            $session->set('kasko-auto', $model->auto);
            $session->set('kasko-tariff_id', $model->tariff_id);
            $session->set('kasko-price_coeff', Yii::$app->request->post('price_coeff'));
            $session->set('kasko-promo_code', $model->promo_code);

            $session->close();

            return $this->redirect(['kasko-form']);
        }
        
        return $this->render('kasko-calc', [
            'model' => $model
        ]);
    }  

    public function actionKaskoForm()
    {
        $this->layout = 'kasko';

        $model = new Kasko();

        $session = Yii::$app->session;

        if($session->isActive && $session->has('kasko-tariff_id')) {
            $model->autobrand_id = $session->get('kasko-autobrand_id');
            $model->promo_code = $session->get('kasko-promo_code');

            if($model->autobrand_id != 0) {
                $ids = explode(',', $session->get('kasko-auto'));
                $model->autocomp_id = $ids[1];
                $model->year = $ids[2];
            }

            $model->tariff_id = $session->get('kasko-tariff_id');
            $model->partner_id = $model->tariff->partner_id;
            $model->price_coeff = $session->get('kasko-price_coeff');

            $partners = $model->calc();

            if(!empty($partners)) {
                $model->amount_uzs = $partners[0]['amount'];
                $model->amount_usd = $partners[0]['amount_usd'];
                $model->promo_amount = $partners[0]['without_margin']*$model->promo_percent/100;
            }


        } else {
            return $this->redirect(['kasko-calc']);
        }


        if ($model->load(Yii::$app->request->post())) {
            $model->begin_date = date('Y-m-d', strtotime($model->begin_date));
            $model->end_date = date('Y-m-d', strtotime("+364 day", strtotime($model->begin_date)));

            $model->status = 1;
            $model->created_at = time();
            $model->save();

            //var_dump($model);
    
            return $this->redirect(['kasko-payment', 'id' => base64_encode($model->getPrimaryKey())]);
        }
        
        return $this->render('kasko-form', [
            'model' => $model,
        ]);
    } 

    public function actionKaskoPayment($id)
    {
        $this->layout = 'kasko';

        $_id = base64_decode($id);

        $model = Kasko::findOne([$_id]);
        $amount = $model->amount_uzs * 100;
        
        $payme = new \frontend\models\PaymeForm();
        $verify = false;
        $sent = false;
        $phone = '';
        $verifyPhone = '';
        $verifyWait = 0;
        $token = "";
        
        if (Yii::$app->request->isPost && $payme->load(Yii::$app->request->post())) {
            $client = new Client();
            if(!$payme->phone && ($payme->number && $payme->expiry)) :
                if(Yii::$app->request->post('verify') == 1 && !is_null($payme->verifyCode)) {
                    $response2 = $client->createRequest()
                                   ->setFormat(Client::FORMAT_JSON)
                                   ->setMethod('POST')
                                   ->setUrl('https://checkout.paycom.uz/api')
                                   ->addHeaders(['X-Auth' => '6076e236e409214cf720b106', "content-type" => "application/json"])
                                   ->setData(["jsonrpc" => "2.0", "method" => "cards.verify", "id" => $_id, "params" => [
                                          "token" => Yii::$app->request->post('token'),
                                          "code" => $payme->verifyCode
                                      ],
                                   ])
                                   ->send();
                        if ($response2->isOk) {
                            $data2 = json_decode($response2->getContent(), true);
                            if(isset($data2['error'])) {
                              Yii::$app->session->setFlash('danger', $data2['error']['message'] . " code=" . $data2['error']['code']);
                            }
                            if(isset($data2['result']['card']['verify']) && $data2['result']['card']['verify']) {
                                  $token = $data2['result']['card']['token'];

                                  $response3 = $client->createRequest()
                                       ->setFormat(Client::FORMAT_JSON)
                                       ->setMethod('POST')
                                       ->setUrl('https://checkout.paycom.uz/api')
                                       ->addHeaders(['X-Auth' => '6076e236e409214cf720b106:SFTU7O@vthSX3jxKgBpkIP%dQpYQAFvurNEQ', "content-type" => "application/json"])
                                       ->setData(["jsonrpc" => "2.0", "method" => "receipts.create", "id" => $_id, "params" => [
                                              "amount" => $amount,
                                              "account" => ["order_id" => $_id],
                                          ],
                                       ])
                                       ->send();
                                  if ($response3->isOk) {
                                      $data3 = json_decode($response3->getContent(), true);
                                      //var_dump($data3);
                                      if(isset($data3['error'])) {
                                        Yii::$app->session->setFlash('danger', $data3['error']['message'] . " code=" . $data3['error']['code']);
                                      }
                                            //var_dump($data3);
                                      if(isset($data3['result']['receipt']['_id'])) {
                                        /*$trans = Transactions::find()->where(['trans_no' => $data3['result']['receipt']['_id']]);
                                        $trans->token = $token;
                                        $trans->save();*/
                                        $response4 = $client->createRequest()
                                         ->setFormat(Client::FORMAT_JSON)
                                         ->setMethod('POST')
                                         ->setUrl('https://checkout.paycom.uz/api')
                                         ->addHeaders(['X-Auth' => '6076e236e409214cf720b106:SFTU7O@vthSX3jxKgBpkIP%dQpYQAFvurNEQ', "content-type" => "application/json"])
                                         ->setData(["jsonrpc" => "2.0", "method" => "receipts.pay", "id" => $_id, "params" => [
                                                "id" => $data3['result']['receipt']['_id'],
                                                "token" => $token
                                            ],
                                         ])
                                         ->send();

                                         if ($response4->isOk) {
                                          $data4 = json_decode($response4->getContent(), true);
                                            if(isset($data4['error'])) {
                                              Yii::$app->session->setFlash('danger', $data4['error']['message'] . " code=" . $data4['error']['code']);
                                            }
                                            if(isset($data4['receipt']['state']) && $data4['receipt']['state'] == 4) {
                                                return $this->redirect(['kasko-complete', 'id' => $id]);
                                            }
                                         }
                                      }

                                  }
                                }
                        }
                        else {
                            $data = array();
                        }
                } else {
                    $response1 = $client->createRequest()
                                       ->setFormat(Client::FORMAT_JSON)
                                       ->setMethod('POST')
                                       ->setUrl('https://checkout.paycom.uz/api')
                                       ->addHeaders(['X-Auth' => '6076e236e409214cf720b106', "content-type" => "application/json"])
                                       ->setData(["jsonrpc" => "2.0", "method" => "cards.create", "id" => $_id, "params" => [
                                              "card" => ["number" => $payme->number, "expire" => $payme->expiry],
                                              "amount" => $amount,  
                                              //"account" => ["order_id" => $id],
                                              "save" => true
                                          ],
                                       ])
                                       ->send();
                    if ($response1->isOk) {
                        $data = json_decode($response1->getContent(), true);
                        if(isset($data2['error'])) {
                          Yii::$app->session->setFlash('danger', $data2['error']['message'] . " code=" . $data2['error']['code']);
                        }
                        //var_dump($data);
                        if(isset($data['result']) && isset($data['result']['card']) && isset($data['result']['card']['recurrent'])) {
                            $response2 = $client->createRequest()
                                       ->setFormat(Client::FORMAT_JSON)
                                       ->setMethod('POST')
                                       ->setUrl('https://checkout.paycom.uz/api')
                                       ->addHeaders(['X-Auth' => '6076e236e409214cf720b106', "content-type" => "application/json"])
                                       ->setData(["jsonrpc" => "2.0", "method" => "cards.get_verify_code", "id" => $_id, "params" => [
                                              "token" => $data['result']['card']['token']
                                          ],
                                       ])
                                       ->send();
                            if ($response2->isOk) {
                                $data2 = json_decode($response2->getContent(), true);
                                if(isset($data2['error'])) {
                                  Yii::$app->session->setFlash('danger', $data2['error']['message'] . " code=" . $data2['error']['code']);
                                }
                                //var_dump($data2);

                                if(isset($data['result']) && $data2['result']['sent']) {
                                    $verify = true;
                                    $verifyPhone = $data2['result']['phone'];
                                    $verifyWait = $data2['result']['wait'];
                                    $token = $data['result']['card']['token'];
                                }
                            }
                            else {
                                $data = array();
                            }
                        } else {
                            $data = array();
                        }
                    }
                }
            elseif($payme->phone && (!$payme->number && !$payme->expiry)):

                $response3 = $client->createRequest()
                    ->setFormat(Client::FORMAT_JSON)
                    ->setMethod('POST')
                    ->setUrl('https://checkout.paycom.uz/api')
                    ->addHeaders(['X-Auth' => '6076e236e409214cf720b106:SFTU7O@vthSX3jxKgBpkIP%dQpYQAFvurNEQ', "content-type" => "application/json"])
                    ->setData(["jsonrpc" => "2.0", "method" => "receipts.create", "id" => $_id, "params" => [
                          "amount" => $amount,
                          "account" => ["order_id" => $_id],
                      ],
                    ])
                    ->send();
                if ($response3->isOk) {
                    $data3 = json_decode($response3->getContent(), true);
                    if(isset($data3['error'])) {
                      Yii::$app->session->setFlash('danger', $data3['error']['message'] . " code=" . $data3['error']['code']);
                    }
                    if(isset($data3['result']['receipt']['_id'])) {
                        $response4 = $client->createRequest()
                            ->setFormat(Client::FORMAT_JSON)
                            ->setMethod('POST')
                            ->setUrl('https://checkout.paycom.uz/api')
                            ->addHeaders(['X-Auth' => '6076e236e409214cf720b106:SFTU7O@vthSX3jxKgBpkIP%dQpYQAFvurNEQ', "content-type" => "application/json"])
                            ->setData(["jsonrpc" => "2.0", "method" => "receipts.send", "id" => $_id, "params" => [
                                "id" => $data3['result']['receipt']['_id'],
                                "phone" => $payme->phone
                                ],
                            ])
                            ->send();
                        if ($response4->isOk) {

                            $data4 = json_decode($response4->getContent(), true);
                            if(isset($data4['error'])) {
                                Yii::$app->session->setFlash('danger', $data4['error']['message'] . " code=" . $data4['error']['code']);
                            }
                            if(isset($data4['result']['success'])) {
                                $sent = true;
                                $phone = $payme->phone;
                            }
                        } else {
                            var_dump($data3);
                        }
                    }
                }
            endif;
        }

        $model = Kasko::findOne([$_id]);
        $trans = Transaction::findOne([$model->trans_id]);

        if($trans && $trans->status == 2) {
            return $this->redirect(['kasko-complete', 'id' => $id]);
        }

        return $this->render('payme', [
            'amount' => $model->amount_uzs,
            'id' => $id,
            'payme' => $payme,
            'verify' => $verify,
            'verifyPhone' => $verifyPhone,
            'verifyWait' => $verifyWait,
            'token' => $token,
            'sent' => $sent,
            'phone' => $phone,
            'url' => 'product/kasko-complete',
            'product' => 'kasko'
        ]);
    }

    public function actionKaskoComplete($id)
    {
        $this->layout = 'kasko';

        $_id = base64_decode($id);

        $model = Kasko::findOne([$_id]);

        if(!$model) {
            Yii::$app->session->setFlash('danger', 'URL is incorrect');
            return $this->redirect(['kasko-calc']);
        }

        $status = 0;

        $trans = Transaction::findOne([$model->trans_id]);

        if(is_null($trans) || $trans->status != 2) {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Policy is not paid'));
            return $this->redirect(['kasko-payment', 'id' => $id]);
        } else {
            $status = $trans->status;
        }

        return $this->render('complete', [
            'id' => $id,
            'model' => $model,
            'status' => $status,
            'product' => 'kasko'
        ]);
    }

    public function actionKaskoAutoPrice() {
        $ids = explode(',', Yii::$app->request->get('auto'));

        $autocomp = Autocomp::findOne($ids[1]);
        $tariffs = KaskoTariff::find()->all();

        $price = $autocomp->price;

        $year = $ids[2];

        $price = Kasko::getAutoRealPrice($price, $year);

        $amounts = [];

        foreach($tariffs as $t) {
            if($t->amount_kind == 'P'){
                $a = round($price * $t->amount / 100, 2);
            } else {
                $a = $t->amount;
            }

            $amounts[] = [
                'tariff_id' => $t->id, 
                'kasko_amount' => $a, 
            ];
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return ['price' => $price, 'amounts' => $amounts];
    }

    public function actionAutomodelList($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $count = Automodel::find()
            ->where(['autobrand_id' => $id])
            ->count();

        $automodels = Automodel::find()
            ->where(['autobrand_id' => $id])
            ->all();

        $s = "<option value=''>" . Yii::t('app', 'Выберите') . "</option>";

        $n = '<li data-value="" class="option selected">' . Yii::t('app', 'Выберите') . '</li>';

        if($count > 0) {
            foreach($automodels as $a) {
                $s .= "<option value='" . $a->id . "'>" . $a->name . "</option>";
                $n .= "<li class='option' data-value='" . $a->id . "'>" . $a->name . "</li>";
            }
        } else {
            $s = '';
            $n = '';
        }

        return [
            's' => $s,
            'n' => $n
        ];

    }

    public function actionAutocompList($autobrand_id, $id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $ids = explode(',', $id);


        if($autobrand_id) {
            if(count($ids) == 0 || $id == 'null' || $id == '' || is_null($id)) {
                $automodels = Automodel::find()
                    ->where(['autobrand_id' => $autobrand_id])
                    ->all();

                $s = "<option value=''>" . Yii::t('app', '- Выберите -') . "</option>";

                $n = '<li data-value="" class="option selected">' . Yii::t('app', '- Выберите -') . '</li>';

                foreach($automodels as $a) {
                    $s .= "<option value='" . $a->id . "'>" . $a->name . "</option>";
                    $n .= "<li class='option' data-value='" . $a->id . "'>" . $a->name . "</li>";
                }

                $change = true;
            } elseif(count($ids) == 1) {
                $count = Autocomp::find()
                ->where(['automodel_id' => $ids[0]])
                ->count();

                $autocomps = Autocomp::find()
                    ->where(['automodel_id' => $ids[0]])
                    ->all();

                $automodel = Automodel::findOne($ids[0]);

                $s = "<option value=''>" . Yii::t('app', 'Назад') . "</option>";

                $n = '<li data-value="" class="option selected">' . Yii::t('app', 'Назад') . '</li>';

                if($count > 0) {
                    foreach($autocomps as $a) {
                        $s .= "<option value='" . $a->automodel_id . "," . $a->id . "'>" . $a->automodel->name . ", " . $a->name . "</option>";
                        $n .= "<li class='option' data-value='" . $a->automodel_id . "," . $a->id . "'>" . $a->automodel->name . ", " . $a->name . "</li>";
                    }
                }

                $change = true;
            } elseif(count($ids) == 2) {
                $autocomp = Autocomp::findOne($ids[1]);

                $s = "";

                $s = "<option value='".$autocomp->automodel_id."'>" . Yii::t('app', '- Выберите -') . "</option>";
                $n = '<li data-value="'.$autocomp->automodel_id.'" class="option selected">' . Yii::t('app', 'Назад') . '</li>';
                $years = Kasko::getYearsList();
                
                foreach($years as $a) {
                    $s .= "<option value='" . $autocomp->automodel_id . "," . $autocomp->id . "," . $a . "'>" . $autocomp->automodel->name . ", " . $autocomp->name . ", " . $a . "</option>";
                    $n .= "<li class='option' data-value='" . $autocomp->automodel_id . "," . $autocomp->id . "," . $a . "'>" . $autocomp->automodel->name . ", " . $autocomp->name . ", " . $a . "</li>";
                }
                
                $change = true;
            } else {
                $s = "";
                $n = "";
                $change = false;
            }
        } else {
            $s = "";
            $n = "";
            $change = true;
        }        

        return [
            's' => $s,
            'n' => $n,
            'change' => $change
        ];
    }

    public function actionGetKaskoAmounts() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new Kasko();
        $model->autobrand_id = Yii::$app->request->get('autobrand_id');
        $model->promo_code = Yii::$app->request->get('promo');

        if($model->autobrand_id != 0) {
            $ids = explode(',', Yii::$app->request->get('auto'));

            $model->autocomp_id = $ids[1];
            $model->year = $ids[2];
            $price = $model->autocomp->price;
        }
        
        $model->price_coeff = Yii::$app->request->get('price_coeff');

        $tariffs = $model->calc();

        usort($tariffs, function ($item1, $item2) {
            return $item2['amount'] <=> $item1['amount'];
        });

        $result = '';

        foreach($tariffs as $tariff) {
            $result .= "<div class='box bg-white shadow-sm p-2 border-bottom'>
                            <div class='row'>
                             
                             <div class='col text-right pr-4'><span title='".Yii::t('app', 'yozuv')."'>";
                             for($i = 0; $i < $tariff['star']; $i++) {
                  $result .= "<img width='15px' src='/img/star.png' />";
                } 
                $result.=" </span></div> </div><div class='row'>
                                <div class='col-md-3 col-6 p-4'>
                                    <img src='/uploads/partners/".$tariff['partner']->image."' class='w-75' />
                                </div>";

                if($tariff['margin'] < 0 && $tariff['without_margin'] != $tariff['amount']) {
                  $result .= "<div class='col-md-3 col-6 pt-2 pl-lg-4'><h5 class='pt-0'><del>".number_format($tariff['without_margin'],0,","," ") .' '. Yii::t('app', 'сум')."</del></h5>";
                } else {
                  $result .= "<div class='col-md-3 col-6 pt-2 p-lg-4'>";
                }

                $result .= "<h5 class='pt-3'>".number_format($tariff['amount'],0,","," ") .' '. Yii::t('app', 'сум')."</h5>
                                </div>
                                <div class='col-md-3 col-6 p-0 pl-4 p-lg-4'>
                                    <p class='read-more pt-3'>".Yii::t('app', 'Подробнее')."</p>
                                </div>
                                <div class='col-md-3 col-6 pt-lg-4 pt-0'>
                                    ". Html::submitButton(Yii::t('app', 'Купить'), ['name' => 'Kasko[tariff_id]', 'value' => $tariff['tariff_id'], 'class' => 'mybtn page-btn'])."
                                </div>
                            </div>
                        <div class='risks pl-4 pr-4'>";

                foreach($tariff['risks'] as $risk) {
                    if(Yii::$app->language == 'ru') {
                        $risk_name = $risk->risk->name_ru;
                    } elseif(Yii::$app->language == 'uz') {
                        $risk_name = $risk->risk->name_uz;
                    } elseif(Yii::$app->language == 'en') {
                        $risk_name = $risk->risk->name_en;
                    }

                    $result .= "<p><i class='fa fa-check'></i>&nbsp;" . $risk_name . "</p>";
                }

                $result .= "</div></div>";


            }

            
        
        

        return [
            'html' => $result,
        ];
    }

    public function actionTravelCalc()
    {
        $this->layout = 'travel';

        $model = new Travel();
        $travelers = [new Traveler()];
        $results = [];
        $calc = false;


        if ($model->load(Yii::$app->request->post())) {
            // $model->purpose_id = 3;
            // $model->group_type_id = 3;
            
            $country_ids = Yii::$app->request->post('Travel')['countries'];
            
            $session = Yii::$app->session;
            $session->open();

            $session->set('travel-program_id', $model->program_id);
            $session->set('travel-begin_date', $model->begin_date);
            $session->set('travel-end_date', $model->end_date);
            $session->set('travel-countries', $country_ids);
            $session->set('travel-purpose_id', $model->purpose_id);
            $session->set('travel-promo_code', $model->promo_code);
            
            $session->set('travel-extra_insurances', Yii::$app->request->post('Travel')['travelExtraInsuranceBinds']);
            $session->set('travel-traveler_birthdays', array_column(Yii::$app->request->post('Traveler'), 'birthday'));

            if(isset(Yii::$app->request->post('Travel')['isFamily']) && Yii::$app->request->post('Travel')['isFamily'] == 1) {
                $session->set('travel-isFamily', 1);
            } else {
                $session->set('travel-isFamily', 0);
            }

            $session->close();

            return $this->redirect(['travel-form']);
            
        }
        
        return $this->render('travel-calc', [
            'model' => $model,
            'travelers' => $travelers,
            'calc' => $calc,
            'results' => $results
        ]);
    }

    public function actionTravelForm()
    {
        $this->layout = 'travel';

        $model = new Travel();
        $travelers = [];
        $isNewInsurer = false;
        $parents = [new TravelParent()];
        $children = [new Child()];

        $session = Yii::$app->session;

        if($session->isActive && $session->has('travel-program_id')) {
            $model->program_id = $session->get('travel-program_id');
            $model->begin_date = $session->get('travel-begin_date');
            $model->end_date = $session->get('travel-end_date');
            $model->countries = $session->get('travel-countries');
            $model->purpose_id = $session->get('travel-purpose_id');
            $model->promo_code = $session->get('travel-promo_code');
            $model->isFamily = $session->get('travel-isFamily');
            $model->extraInsurances = $session->get('travel-extra_insurances');
            $model->travelerBirthdays = $session->get('travel-traveler_birthdays');

            $model->partner_id = $model->program->partner_id;
            $partners = $model->calc();

            if(!empty($partners)) {
                $model->amount_uzs = $partners[0]['amount'];
                $model->amount_usd = $partners[0]['amount_usd'];
                $model->promo_amount = $partners[0]['without_margin']*$model->promo_percent/100;
            }

            $traveler_birthdays = [];

            foreach($model->travelerBirthdays as $p) {
                $traveler_birthdays[] = strtotime($p);
            }

            sort($traveler_birthdays);
            //var_dump($traveler_birthdays);
            if(count($traveler_birthdays) > 0) {
                $age = floor((time() - $traveler_birthdays[0]) / 31556926);
                if($age < 18) {
                    $isNewInsurer = true;
                }
            }

            foreach($traveler_birthdays as $birthday) {
                $travelerform = new Traveler();
                $travelerform->birthday = date('d.m.Y', $birthday);
                $travelers[] = $travelerform;
            }

        } else {
            return $this->redirect(['travel-calc']);
        }


        if ($model->load(Yii::$app->request->post())) {
            $model->begin_date = date('Y-m-d', strtotime($model->begin_date));
            $model->end_date = date('Y-m-d', strtotime($model->end_date));
            $model->days = abs(round((strtotime($model->begin_date) - strtotime($model->end_date)) / 86400)) + 1; 
            $model->insurer_birthday = date('Y-m-d', strtotime($model->insurer_birthday));

            if($model->isFamily == 1) {
                $model->group_type_id = 2;
            } else {
                $model->group_type_id = 3;
            }

            $model->status = 1;
            $model->created_at = time();
            $model->save();

            if(!empty($model->extraInsurances)) {
                foreach($model->extraInsurances as $ei) {
                    $tr_extra_ins = new TravelExtraInsuranceBind;
                    $tr_extra_ins->travel_id = $model->getPrimaryKey();
                    $tr_extra_ins->extra_insurance_id = $ei;
                    $tr_extra_ins->save();
                }
            }

            foreach($model->countries as $c) {
                $tc = new TravelCountry;
                $tc->travel_id = $model->getPrimaryKey();
                $tc->country_id = $c;
                $tc->save();
            }

            Model::loadMultiple($travelers, Yii::$app->request->post());

            foreach ($travelers as $item) {
                $item->travel_id = $model->getPrimaryKey();     
                $item->birthday = date('Y-m-d', strtotime($item->birthday));
                $item->passport_series = strtoupper($item->passport_series);
                $item->save();
            }

            Model::loadMultiple($parents, Yii::$app->request->post());

            foreach ($parents as $item) {
                $item->travel_id = $model->getPrimaryKey();     
                $item->birthday = date('Y-m-d', strtotime($item->birthday));
                $item->passport_series = strtoupper($item->passport_series);
                $item->save();
            }

            Model::loadMultiple($children, Yii::$app->request->post());

            foreach ($children as $item) {
                $item->travel_id = $model->getPrimaryKey();     
                $item->birthday = date('Y-m-d', strtotime($item->birthday));
                $item->passport_series = strtoupper($item->passport_series);
                $item->save();
            }
    
            return $this->redirect(['travel-payment', 'id' => base64_encode($model->getPrimaryKey())]);
        }

        // $drivers = [new OsagoDriver()];
        
        return $this->render('travel-form', [
            'model' => $model,
            'travelers' => $travelers,
            'isNewInsurer' => $isNewInsurer,
            'parents' => $parents,
            'children' => $children
        ]);
    } 

    public function actionTravelPayment($id)
    {
        $this->layout = 'travel';

        $_id = base64_decode($id);

        $model = Travel::findOne([$_id]);
        $amount = $model->amount_uzs * 100;
        
        $payme = new \frontend\models\PaymeForm();
        $verify = false;
        $sent = false;
        $phone = '';
        $verifyPhone = '';
        $verifyWait = 0;
        $token = "";
        
        if (Yii::$app->request->isPost && $payme->load(Yii::$app->request->post())) {
            $client = new Client();
            if(!$payme->phone && ($payme->number && $payme->expiry)) :
                if(Yii::$app->request->post('verify') == 1 && !is_null($payme->verifyCode)) {
                    $response2 = $client->createRequest()
                                   ->setFormat(Client::FORMAT_JSON)
                                   ->setMethod('POST')
                                   ->setUrl('https://checkout.paycom.uz/api')
                                   ->addHeaders(['X-Auth' => '6076e1dae409214cf720b104', "content-type" => "application/json"])
                                   ->setData(["jsonrpc" => "2.0", "method" => "cards.verify", "id" => $_id, "params" => [
                                          "token" => Yii::$app->request->post('token'),
                                          "code" => $payme->verifyCode
                                      ],
                                   ])
                                   ->send();
                        if ($response2->isOk) {
                            $data2 = json_decode($response2->getContent(), true);
                            if(isset($data2['error'])) {
                              Yii::$app->session->setFlash('danger', $data2['error']['message'] . " code=" . $data2['error']['code']);
                            }
                            if(isset($data2['result']['card']['verify']) && $data2['result']['card']['verify']) {
                                  $token = $data2['result']['card']['token'];

                                  $response3 = $client->createRequest()
                                       ->setFormat(Client::FORMAT_JSON)
                                       ->setMethod('POST')
                                       ->setUrl('https://checkout.paycom.uz/api')
                                       ->addHeaders(['X-Auth' => '6076e1dae409214cf720b104:cFHzGeIEu1XYsq3xXSJS?yigqxPk?3pVczzg', "content-type" => "application/json"])
                                       ->setData(["jsonrpc" => "2.0", "method" => "receipts.create", "id" => $_id, "params" => [
                                              "amount" => $amount,
                                              "account" => ["order_id" => $_id],
                                          ],
                                       ])
                                       ->send();
                                  if ($response3->isOk) {
                                      $data3 = json_decode($response3->getContent(), true);
                                      //var_dump($data3);
                                      if(isset($data3['error'])) {
                                        Yii::$app->session->setFlash('danger', $data3['error']['message'] . " code=" . $data3['error']['code']);
                                      }
                                            //var_dump($data3);
                                      if(isset($data3['result']['receipt']['_id'])) {
                                        /*$trans = Transactions::find()->where(['trans_no' => $data3['result']['receipt']['_id']]);
                                        $trans->token = $token;
                                        $trans->save();*/
                                        $response4 = $client->createRequest()
                                         ->setFormat(Client::FORMAT_JSON)
                                         ->setMethod('POST')
                                         ->setUrl('https://checkout.paycom.uz/api')
                                         ->addHeaders(['X-Auth' => '6076e1dae409214cf720b104:cFHzGeIEu1XYsq3xXSJS?yigqxPk?3pVczzg', "content-type" => "application/json"])
                                         ->setData(["jsonrpc" => "2.0", "method" => "receipts.pay", "id" => $_id, "params" => [
                                                "id" => $data3['result']['receipt']['_id'],
                                                "token" => $token
                                            ],
                                         ])
                                         ->send();

                                         if ($response4->isOk) {
                                          $data4 = json_decode($response4->getContent(), true);
                                            if(isset($data4['error'])) {
                                              Yii::$app->session->setFlash('danger', $data4['error']['message'] . " code=" . $data4['error']['code']);
                                            }
                                            if(isset($data4['receipt']['state']) && $data4['receipt']['state'] == 4) {
                                                return $this->redirect(['travel-complete', 'id' => $id]);
                                            }
                                         }
                                      }

                                  }
                                }
                        }
                        else {
                            $data = array();
                        }
                } else {
                    $response1 = $client->createRequest()
                                       ->setFormat(Client::FORMAT_JSON)
                                       ->setMethod('POST')
                                       ->setUrl('https://checkout.paycom.uz/api')
                                       ->addHeaders(['X-Auth' => '6076e1dae409214cf720b104', "content-type" => "application/json"])
                                       ->setData(["jsonrpc" => "2.0", "method" => "cards.create", "id" => $_id, "params" => [
                                              "card" => ["number" => $payme->number, "expire" => $payme->expiry],
                                              "amount" => $amount,  
                                              //"account" => ["order_id" => $id],
                                              "save" => true
                                          ],
                                       ])
                                       ->send();
                    if ($response1->isOk) {
                        $data = json_decode($response1->getContent(), true);
                        if(isset($data2['error'])) {
                          Yii::$app->session->setFlash('danger', $data2['error']['message'] . " code=" . $data2['error']['code']);
                        }
                        //var_dump($data);
                        if(isset($data['result']) && isset($data['result']['card']) && isset($data['result']['card']['recurrent'])) {
                            $response2 = $client->createRequest()
                                       ->setFormat(Client::FORMAT_JSON)
                                       ->setMethod('POST')
                                       ->setUrl('https://checkout.paycom.uz/api')
                                       ->addHeaders(['X-Auth' => '6076e1dae409214cf720b104', "content-type" => "application/json"])
                                       ->setData(["jsonrpc" => "2.0", "method" => "cards.get_verify_code", "id" => $_id, "params" => [
                                              "token" => $data['result']['card']['token']
                                          ],
                                       ])
                                       ->send();
                            if ($response2->isOk) {
                                $data2 = json_decode($response2->getContent(), true);
                                if(isset($data2['error'])) {
                                  Yii::$app->session->setFlash('danger', $data2['error']['message'] . " code=" . $data2['error']['code']);
                                }
                                //var_dump($data2);

                                if(isset($data['result']) && $data2['result']['sent']) {
                                    $verify = true;
                                    $verifyPhone = $data2['result']['phone'];
                                    $verifyWait = $data2['result']['wait'];
                                    $token = $data['result']['card']['token'];
                                }
                            }
                            else {
                                $data = array();
                            }
                        } else {
                            $data = array();
                        }
                    }
                }
            elseif($payme->phone && (!$payme->number && !$payme->expiry)):

                $response3 = $client->createRequest()
                    ->setFormat(Client::FORMAT_JSON)
                    ->setMethod('POST')
                    ->setUrl('https://checkout.paycom.uz/api')
                    ->addHeaders(['X-Auth' => '6076e1dae409214cf720b104:cFHzGeIEu1XYsq3xXSJS?yigqxPk?3pVczzg', "content-type" => "application/json"])
                    ->setData(["jsonrpc" => "2.0", "method" => "receipts.create", "id" => $_id, "params" => [
                          "amount" => $amount,
                          "account" => ["order_id" => $_id],
                      ],
                    ])
                    ->send();
                if ($response3->isOk) {
                    $data3 = json_decode($response3->getContent(), true);
                    if(isset($data3['error'])) {
                      Yii::$app->session->setFlash('danger', $data3['error']['message'] . " code=" . $data3['error']['code']);
                    }
                    if(isset($data3['result']['receipt']['_id'])) {
                        $response4 = $client->createRequest()
                            ->setFormat(Client::FORMAT_JSON)
                            ->setMethod('POST')
                            ->setUrl('https://checkout.paycom.uz/api')
                            ->addHeaders(['X-Auth' => '6076e1dae409214cf720b104:cFHzGeIEu1XYsq3xXSJS?yigqxPk?3pVczzg', "content-type" => "application/json"])
                            ->setData(["jsonrpc" => "2.0", "method" => "receipts.send", "id" => $_id, "params" => [
                                "id" => $data3['result']['receipt']['_id'],
                                "phone" => $payme->phone
                                ],
                            ])
                            ->send();
                        if ($response4->isOk) {

                            $data4 = json_decode($response4->getContent(), true);
                            if(isset($data4['error'])) {
                                Yii::$app->session->setFlash('danger', $data4['error']['message'] . " code=" . $data4['error']['code']);
                            }
                            if(isset($data4['result']['success'])) {
                                $sent = true;
                                $phone = $payme->phone;
                            }
                        } else {
                            var_dump($data3);
                        }
                    }
                }
            endif;
        }

        $model = Travel::findOne([$_id]);
        $trans = Transaction::findOne([$model->trans_id]);

        if($trans && $trans->status == 2) {
            return $this->redirect(['travel-complete', 'id' => $id]);
        }

        return $this->render('payme', [
            'amount' => $model->amount_uzs,
            'id' => $id,
            'payme' => $payme,
            'verify' => $verify,
            'verifyPhone' => $verifyPhone,
            'verifyWait' => $verifyWait,
            'token' => $token,
            'sent' => $sent,
            'phone' => $phone,
            'url' => 'product/travel-complete',
            'product' => 'travel'
        ]);
    }

    public function actionTravelComplete($id)
    {
        $this->layout = 'travel';

        $_id = base64_decode($id);

        $model = Travel::findOne([$_id]);

        if(!$model) {
            Yii::$app->session->setFlash('danger', 'URL is incorrect');
            return $this->redirect(['travel-calc']);
        }

        $status = 0;

        $trans = Transaction::findOne([$model->trans_id]);

        if(is_null($trans) || $trans->status != 2) {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Policy is not paid'));
            return $this->redirect(['travel-payment', 'id' => $id]);
        } else {
            $status = $trans->status;
        }

        return $this->render('complete', [
            'id' => $id,
            'model' => $model,
            'status' => $status,
            'product' => 'travel'
        ]);
    }

    public function actionCheckShengen() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;   

        $countries = Country::find()->where(['id' => Yii::$app->request->get('countries')])->asArray()->all();
        $is_shengen = false;

        foreach($countries as $c) {
            if($c['schengen']) $is_shengen = true;
        }
        
        return [
            'shengen' => $is_shengen
        ];

    }

    public function actionGetTravelAmounts() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;   
        $model = new Travel();

        $model->purpose_id = Yii::$app->request->get('purpose');
        $model->isFamily = Yii::$app->request->get('isFamily');
        $model->begin_date = Yii::$app->request->get('begin_date');
        $model->end_date = Yii::$app->request->get('end_date');
        $model->extraInsurances = Yii::$app->request->get('extraInsurances');
        $model->travelerBirthdays = Yii::$app->request->get('travelers');
        $model->promo_code = Yii::$app->request->get('promo');
        
        $model->countries = Yii::$app->request->get('countries');

        $partners = $model->calc();

        // $country_ids = Yii::$app->request->get('countries');

        // $countries = Country::find()->where(['id' => $country_ids])->asArray()->all();
        // $is_shengen = false;

        // foreach($countries as $c) {
        //     if($c['schengen']) $is_shengen = true;
        // }

        // $parent_ids = array_unique(array_column($countries, 'parent_id'));

        // $extra_insurance_ids = Yii::$app->request->get('extraInsurances');

        // $partners = Partner::find()->all();

        // $purpose = TravelPurpose::findOne([$model->purpose_id]);

        // $model->days = abs(round((strtotime($model->begin_date) - strtotime($model->end_date)) / 86400)) + 1; 

        // $birthdays = Yii::$app->request->get('travelers');
        
        // $usd = Currency::getUsdRate();

        usort($partners, function ($item1, $item2) {
            return $item2['amount'] <=> $item1['amount'];
        });

        $result = '';
        

        foreach($partners as $i => $item) :
            $result .= "<div class='box bg-white shadow-sm p-2 border-bottom mt-2' style='padding-top: 0 !important;'>";

            if($item['program']->has_covid) {
              $result .= '<div class="row" style="padding: 0 7px;">
                <div class="col bg-success text-center text-white" style="padding: 2px;">'.Yii::t('app','Покрывается лечение коронавируса (COVID-19)').'</div>
              </div>';
            } else {              
              $result .= '<div class="row" style="padding: 0 7px;">
                <div class="col bg-danger text-center text-white" style="padding: 2px;">'.Yii::t('app','Не покрывается лечение коронавируса (COVID-19)').'</div>
              </div>';
            }

            $result .= "<div class='row'>

                             <div class='col text-right pr-4'> <span title='".Yii::t('app', 'yozuv')."'>";
                             for($j = 0; $j < $item['star']; $j++) {
                  $result .= "<img width='15px' src='/img/star.png' />";
                } 
                $result .= " </span> </div> </div><div class='row'>
                        <div class='col-md-3 col-6 p-2 pl-4 pt-lg-4'>
                            <img src='/uploads/partners/".$item['partner']->image."' class='w-75' />
                        </div>
                        ";

                if($item['margin'] < 0 && $item['without_margin'] != $item['amount']) {
                  $result .= "<div class='col-md-3 col-6 pt-2 pl-lg-4'><h5 class='pt-0'><del>".number_format($item['without_margin'],0,","," ") .' '. Yii::t('app', 'сум')."</del></h5>";
                } else {
                  $result .= "<div class='col-md-3 col-6 pt-2 p-lg-4'>";
                }

                $result .= "<h5 class='pt-0'>".number_format($item['amount'],0,","," ") .' '. Yii::t('app', 'сум')."</h5>
                        </div>
                        <div class='col-md-4 col-6 p-0 pl-4 p-lg-4 risks'>
                            <p class='read-more pt-2'>".Yii::t('app', 'Подробнее')."</p>";
                
                $result .= "</div>
                    
                        <div class='col-md-2 col-6' style='margin: auto;'>
                            ". Html::submitButton(Yii::t('app', 'Купить'), ['name' => 'Travel[program_id]', 'value' => $item['program']->id, 'class' => 'mybtn page-btn mini-btn'])."
                        </div>
                    </div>

                    <div class='travel-info row'>
                        <div class='nav flex-column nav-tabs col-4' id='v-pills-tab".$i."' role='tablist' aria-orientation='vertical'>
                          <a class='nav-link active' id='assistance-tab".$i."' data-toggle='tab' href='#assistance".$i."' role='tab' aria-controls='assistance".$i."' aria-selected='true'>".Yii::t('app','Assistance')."</a>
                          <a class='nav-link' id='franchise-tab".$i."' data-toggle='tab' href='#franchise".$i."' role='tab' aria-controls='franchise".$i."' aria-selected='true'>".Yii::t('app','franchise')."</a>
                          <a class='nav-link' id='limitation-tab".$i."' data-toggle='tab' href='#limitation".$i."' role='tab' aria-controls='limitation".$i."' aria-selected='true'>".Yii::t('app','limitation')."</a>
                          <a class='nav-link' id='rules-tab".$i."' data-toggle='tab' href='#rules".$i."' role='tab' aria-controls='rules".$i."' aria-selected='true'>".Yii::t('app','rules')."</a>
                          <a class='nav-link' id='policy_example-tab".$i."' data-toggle='tab' href='#policy_example".$i."' role='tab' aria-controls='policy_example".$i."' aria-selected='true'>".Yii::t('app','policy_example')."</a>
                        </div>
                        <div class='tab-content col-8' id='v-pills-tabContent" . $i . "'>
                          <div class='tab-pane fade show active' id='assistance".$i."' role='tabpanel' aria-labelledby='assistance-tab".$i."'><h5>".$item['info']->assistance."</h5></div>
                          <div class='tab-pane fade' id='franchise".$i."' role='tabpanel' aria-labelledby='franchise-tab".$i."'>".$item['info']->franchise."</div>
                          <div class='tab-pane fade' id='limitation".$i."' role='tabpanel' aria-labelledby='limitation-tab".$i."'>".$item['info']->limitation."</div>
                          <div class='tab-pane fade' id='rules".$i."' role='tabpanel' aria-labelledby='rules-tab".$i."'><a target='_blank' href='/uploads/travel_info/".$item['info']->rules."'><img src='/img/pdf.png'> ".Yii::t('app', 'Скачать правила страхования')."</a></div>
                          <div class='tab-pane fade' id='policy_example".$i."' role='tabpanel' aria-labelledby='policy_example-tab".$i."'><a target='_blank' href='/uploads/travel_info/".$item['info']->policy_example."'><img src='/img/pdf.png'> ".Yii::t('app', 'Образец полиса')."</a></div>
                        </div>
                    </div>
                </div>";
        endforeach;
        
        if(empty($partners)) {
          $result .= "<h5 class='text-center mt-3'>". Yii::t('app', 'Нет вариантов страхования по Вашему запросу') ."</h5>";
        }

        return [
            'html' => $result,
            'shengen' => $model->is_shengen
        ];
    }

    public function actionAccidentCalc()
    {
        $this->layout = 'accident';

        $model = new Accident();

        if ($model->load(Yii::$app->request->post())) {
            $session = Yii::$app->session;
            $session->open();

            $session->set('accident-program_id', $model->program_id);
            $session->set('accident-begin_date', $model->begin_date);
            $session->set('accident-end_date', $model->end_date);
            $session->set('accident-insurer_count', $model->insurer_count + 1);
            $session->set('accident-insurance_amount', $model->insurance_amount);

            $session->close();

            return $this->redirect(['accident-form']);
        }
        
        return $this->render('accident-calc', [
            'model' => $model
        ]);
    }  

    public function actionAccidentForm()
    {
        $this->layout = 'accident';

        $model = new Accident();
        $insurers = [];

        $session = Yii::$app->session;

        if($session->isActive && $session->has('accident-program_id')) {
            $model->program_id = $session->get('accident-program_id');
            $model->partner_id = $model->program->partner_id;
            $model->begin_date = $session->get('accident-begin_date');
            $model->end_date = $session->get('accident-end_date');
            $model->insurer_count = $session->get('accident-insurer_count');
            $model->insurance_amount = $session->get('accident-insurance_amount');

            
            $program = AccidentPartnerProgram::findOne($model->program_id);

            $model->amount_uzs = $program->percent * $model->insurance_amount * $model->insurer_count / 100;
            $usd = Currency::getUsdRate();
            $model->amount_usd = round($model->amount_uzs/$usd, 2);


            for($i = 0; $i < $model->insurer_count; $i++) {
                $insurers[] = new AccidentInsurer();
            }

        } else {
            return $this->redirect(['accident-calc']);
        }


        if ($model->load(Yii::$app->request->post())) {
            $model->begin_date = date('Y-m-d', strtotime($model->begin_date));
            $model->end_date = date('Y-m-d', strtotime($model->end_date));
            $model->insurer_birthday = date('Y-m-d', strtotime($model->insurer_birthday));

            $model->status = 1;
            $model->created_at = time();


            $model->passFile = UploadedFile::getInstance($model, 'passFile');
            $model->viewed = false;
            $model->save();

            if($model->passFile) {
                $model->insurer_passport_file = 'passport_'.$model->id.'.'.$model->passFile->extension;
                $model->save();
                $model->uploadPass();
            }

            Model::loadMultiple($insurers, Yii::$app->request->post());

            foreach($insurers as $i => $item) {
              $item->accident_id = $model->getPrimaryKey();     
              $item->birthday = date('Y-m-d', strtotime($item->birthday));
              
              $item->passFile = UploadedFile::getInstance($item, "[$i]passFile");

              $item->save();

              if($item->passFile) {
                  $item->passport_file = 'insurer_passport_'.$item->id.'.'.$item->passFile->extension;
                  $item->save();
                  $item->uploadPass();
              }
            }

            $session->destroy();
            $session->close();
    
            return $this->redirect(['accident-payment', 'id' => base64_encode($model->getPrimaryKey())]);
        }
        
        return $this->render('accident-form', [
            'model' => $model,
            'insurers' => $insurers
        ]);
    } 
    public function actionAccidentPayment($id)
    {
        $this->layout = 'accident';

        $_id = base64_decode($id);

        $model = Accident::findOne([$_id]);
        $amount = $model->amount_uzs * 100;
        
        $payme = new \frontend\models\PaymeForm();
        $verify = false;
        $sent = false;
        $phone = '';
        $verifyPhone = '';
        $verifyWait = 0;
        $token = "";
        
        if (Yii::$app->request->isPost && $payme->load(Yii::$app->request->post())) {
            $client = new Client();
            if(!$payme->phone && ($payme->number && $payme->expiry)) :
                if(Yii::$app->request->post('verify') == 1 && !is_null($payme->verifyCode)) {
                    $response2 = $client->createRequest()
                                   ->setFormat(Client::FORMAT_JSON)
                                   ->setMethod('POST')
                                   ->setUrl('https://checkout.paycom.uz/api')
                                   ->addHeaders(['X-Auth' => '6076e290e409214cf720b108', "content-type" => "application/json"])
                                   ->setData(["jsonrpc" => "2.0", "method" => "cards.verify", "id" => $_id, "params" => [
                                          "token" => Yii::$app->request->post('token'),
                                          "code" => $payme->verifyCode
                                      ],
                                   ])
                                   ->send();
                        if ($response2->isOk) {
                            $data2 = json_decode($response2->getContent(), true);
                            if(isset($data2['error'])) {
                              Yii::$app->session->setFlash('danger', $data2['error']['message'] . " 1code=" . $data2['error']['code']);
                            }
                            if(isset($data2['result']['card']['verify']) && $data2['result']['card']['verify']) {
                                  $token = $data2['result']['card']['token'];

                                  $response3 = $client->createRequest()
                                       ->setFormat(Client::FORMAT_JSON)
                                       ->setMethod('POST')
                                       ->setUrl('https://checkout.paycom.uz/api')
                                       ->addHeaders(['X-Auth' => '6076e290e409214cf720b108:aJNEpkQnJcuNfXGZ?Nb8hHinA6mgwhVymrUA', "content-type" => "application/json"])
                                       ->setData(["jsonrpc" => "2.0", "method" => "receipts.create", "id" => $_id, "params" => [
                                              "amount" => $amount,
                                              "account" => ["order_id" => $_id],
                                          ],
                                       ])
                                       ->send();
                                  if ($response3->isOk) {
                                      $data3 = json_decode($response3->getContent(), true);
                                      //var_dump($data3);
                                      if(isset($data3['error'])) {
                                        Yii::$app->session->setFlash('danger', $data3['error']['message'] . " 2code=" . $data3['error']['code']);
                                      }
                                            //var_dump($data3);
                                      if(isset($data3['result']['receipt']['_id'])) {
                                        /*$trans = Transactions::find()->where(['trans_no' => $data3['result']['receipt']['_id']]);
                                        $trans->token = $token;
                                        $trans->save();*/
                                        $response4 = $client->createRequest()
                                         ->setFormat(Client::FORMAT_JSON)
                                         ->setMethod('POST')
                                         ->setUrl('https://checkout.paycom.uz/api')
                                         ->addHeaders(['X-Auth' => '6076e290e409214cf720b108:aJNEpkQnJcuNfXGZ?Nb8hHinA6mgwhVymrUA', "content-type" => "application/json"])
                                         ->setData(["jsonrpc" => "2.0", "method" => "receipts.pay", "id" => $_id, "params" => [
                                                "id" => $data3['result']['receipt']['_id'],
                                                "token" => $token
                                            ],
                                         ])
                                         ->send();

                                         if ($response4->isOk) {
                                          $data4 = json_decode($response4->getContent(), true);
                                            if(isset($data4['error'])) {
                                              Yii::$app->session->setFlash('danger', $data4['error']['message'] . " 3code=" . $data4['error']['code']);
                                            }
                                            if(isset($data4['receipt']['state']) && $data4['receipt']['state'] == 4) {
                                                return $this->redirect(['accident-complete', 'id' => $id]);
                                            }
                                         }
                                      }

                                  }
                                }
                        }
                        else {
                            $data = array();
                        }
                } else {
                    $response1 = $client->createRequest()
                                       ->setFormat(Client::FORMAT_JSON)
                                       ->setMethod('POST')
                                       ->setUrl('https://checkout.paycom.uz/api')
                                       ->addHeaders(['X-Auth' => '6076e290e409214cf720b108', "content-type" => "application/json"])
                                       ->setData(["jsonrpc" => "2.0", "method" => "cards.create", "id" => $_id, "params" => [
                                              "card" => ["number" => $payme->number, "expire" => $payme->expiry],
                                              "amount" => $amount,  
                                              //"account" => ["order_id" => $id],
                                              "save" => true
                                          ],
                                       ])
                                       ->send();
                    if ($response1->isOk) {
                        $data = json_decode($response1->getContent(), true);
                        if(isset($data2['error'])) {
                          Yii::$app->session->setFlash('danger', $data2['error']['message'] . " 4code=" . $data2['error']['code']);
                        }
                        //var_dump($data);
                        if(isset($data['result']) && isset($data['result']['card']) && isset($data['result']['card']['recurrent'])) {
                            $response2 = $client->createRequest()
                                       ->setFormat(Client::FORMAT_JSON)
                                       ->setMethod('POST')
                                       ->setUrl('https://checkout.paycom.uz/api')
                                       ->addHeaders(['X-Auth' => '6076e290e409214cf720b108', "content-type" => "application/json"])
                                       ->setData(["jsonrpc" => "2.0", "method" => "cards.get_verify_code", "id" => $_id, "params" => [
                                              "token" => $data['result']['card']['token']
                                          ],
                                       ])
                                       ->send();
                            if ($response2->isOk) {
                                $data2 = json_decode($response2->getContent(), true);
                                if(isset($data2['error'])) {
                                  Yii::$app->session->setFlash('danger', $data2['error']['message'] . " 5code=" . $data2['error']['code']);
                                }
                                //var_dump($data2);

                                if(isset($data['result']) && $data2['result']['sent']) {
                                    $verify = true;
                                    $verifyPhone = $data2['result']['phone'];
                                    $verifyWait = $data2['result']['wait'];
                                    $token = $data['result']['card']['token'];
                                }
                            }
                            else {
                                $data = array();
                            }
                        } else {
                            $data = array();
                        }
                    }
                }
            elseif($payme->phone && (!$payme->number && !$payme->expiry)):

                $response3 = $client->createRequest()
                    ->setFormat(Client::FORMAT_JSON)
                    ->setMethod('POST')
                    ->setUrl('https://checkout.paycom.uz/api')
                    ->addHeaders(['X-Auth' => '6076e290e409214cf720b108:aJNEpkQnJcuNfXGZ?Nb8hHinA6mgwhVymrUA', "content-type" => "application/json"])
                    ->setData(["jsonrpc" => "2.0", "method" => "receipts.create", "id" => $_id, "params" => [
                          "amount" => $amount,
                          "account" => ["order_id" => $_id],
                      ],
                    ])
                    ->send();
                if ($response3->isOk) {
                    $data3 = json_decode($response3->getContent(), true);
                    if(isset($data3['error'])) {
                      Yii::$app->session->setFlash('danger', $data3['error']['message'] . " 6code=" . $data3['error']['code']);
                    }
                    if(isset($data3['result']['receipt']['_id'])) {
                        $response4 = $client->createRequest()
                            ->setFormat(Client::FORMAT_JSON)
                            ->setMethod('POST')
                            ->setUrl('https://checkout.paycom.uz/api')
                            ->addHeaders(['X-Auth' => '6076e290e409214cf720b108:aJNEpkQnJcuNfXGZ?Nb8hHinA6mgwhVymrUA', "content-type" => "application/json"])
                            ->setData(["jsonrpc" => "2.0", "method" => "receipts.send", "id" => $_id, "params" => [
                                "id" => $data3['result']['receipt']['_id'],
                                "phone" => $payme->phone
                                ],
                            ])
                            ->send();
                        if ($response4->isOk) {

                            $data4 = json_decode($response4->getContent(), true);
                            if(isset($data4['error'])) {
                                Yii::$app->session->setFlash('danger', $data4['error']['message'] . " 7code=" . $data4['error']['code']);
                            }
                            if(isset($data4['result']['success'])) {
                                $sent = true;
                                $phone = $payme->phone;
                            }
                        } else {
                            var_dump($data3);
                        }
                    }
                }
            endif;
        }

        $model = Accident::findOne([$_id]);
        $trans = Transaction::findOne([$model->trans_id]);

        if($trans && $trans->status == 2) {
            return $this->redirect(['accident-complete', 'id' => $id]);
        }

        return $this->render('payme', [
            'amount' => $model->amount_uzs,
            'id' => $id,
            'payme' => $payme,
            'verify' => $verify,
            'verifyPhone' => $verifyPhone,
            'verifyWait' => $verifyWait,
            'token' => $token,
            'sent' => $sent,
            'phone' => $phone,
            'url' => 'product/accident-complete',
            'product' => 'accident'
        ]);
    }

    public function actionAccidentComplete($id)
    {
        $this->layout = 'accident';

        $_id = base64_decode($id);

        $model = Accident::findOne([$_id]);

        if(!$model) {
            Yii::$app->session->setFlash('danger', 'URL is incorrect');
            return $this->redirect(['accident-calc']);
        }

        $status = 0;

        $trans = Transaction::findOne([$model->trans_id]);

        if(is_null($trans) || $trans->status != 2) {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Policy is not paid'));
            return $this->redirect(['accident-payment', 'id' => $id]);
        } else {
            $status = $trans->status;
        }

        return $this->render('complete', [
            'id' => $id,
            'model' => $model,
            'status' => $status,
            'product' => 'accident'
        ]);
    }

    public function actionGetAccidentAmounts() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = new Accident();
        $model->insurance_amount = Yii::$app->request->get('insurance_amount');
        $model->insurer_count = Yii::$app->request->get('insurer_count') + 1;

        $programs = AccidentPartnerProgram::find()
          ->where($model->insurance_amount . ' between insurance_amount_from and insurance_amount_to')
          ->all();

        $results = [];

        foreach($programs as $p) {
          $amount = $p->percent * $model->insurance_amount * $model->insurer_count / 100;

          $partner_product = PartnerProduct::find()
            ->where(['partner_id' => $p->partner_id, 'product_id' => 4])
            ->one();


          $amount_with_margin = (($partner_product->percent + 100) / 100) * $amount;

          $results[] = [
            'program_id' => $p->id,
            'partner' => $p->partner,
            'without_margin' => $amount,
            'amount' => $amount_with_margin,
            'star' => $partner_product->star,
            'margin' => $partner_product->percent,
          ];
        }

        usort($results, function ($item1, $item2) {
            return $item2['amount'] <=> $item1['amount'];
        });

        $result = '';

        foreach($results as $tariff) {
            $result .= "<div class='box bg-white shadow-sm p-2 border-bottom'>
                            <div class='row'>
                             
                             <div class='col text-right pr-4'><span title='".Yii::t('app', 'yozuv')."'>";
                             for($i = 0; $i < $tariff['star']; $i++) {
                  $result .= "<img width='15px' src='/img/star.png' />";
                } 
                $result.=" </span></div> </div><div class='row'>
                                <div class='col-md-4 col-6 p-4'>
                                    <img src='/uploads/partners/".$tariff['partner']->image."' class='w-75' />
                                </div>";

                if($tariff['margin'] < 0 && $tariff['without_margin'] != $tariff['amount']) {
                  $result .= "<div class='col-md-4 col-6 pt-2 pl-lg-4'><h5 class='pt-0'><del>".number_format($tariff['without_margin'],0,","," ") .' '. Yii::t('app', 'сум')."</del></h5>";
                } else {
                  $result .= "<div class='col-md-4 col-6 pt-2 p-lg-4'>";
                }

                $result .= "<h5 class='pt-lg-1 pt-3'>".number_format($tariff['amount'],0,","," ") .' '. Yii::t('app', 'сум')."</h5>
                                </div>
                                <div class='col-md-4 col-6 pt-lg-4 pt-0'>
                                    ". Html::submitButton(Yii::t('app', 'Купить'), ['name' => 'Accident[program_id]', 'value' => $tariff['program_id'], 'class' => 'mybtn page-btn'])."
                                </div>
                            </div>
                          </div>";


            }

            
        
        

        return [
            'html' => $result,
        ];
    }

    public function actionIndex()
    {
        $this->layout = 'home';
        
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(GeneralHelper::env('adminEmail'))) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    public function actionTravelCheckout4($id)
    {
        $this->layout = 'travel';
        $status = 0;

        $_id = base64_decode($id);
        $model = Travel::findOne([$_id]);
        $trans = Transactions::findOne([$model->trans_id]);

        if(is_null($trans) || $trans->status != 2) {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Policy is not paid'));
        } else {
            $status = $trans->status;
        }

        return $this->render('travel-checkout4', [
            'id' => $_id,
            'model' => $model,
            'status' => $status
        ]);
    }

    public function actionGenTravelPdf($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

        $model = Travel::findOne([$id]);
//        $trans = Transactions::findOne([$model->trans_id]);

//        if(is_null($trans) || $trans->status != 2) {
//            Yii::$app->session->setFlash('danger', Yii::t('app', 'Policy is not paid'));
//            return $this->redirect(['travel-checkout4', 'id' => $id]);
//        }

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
            'destination' => Pdf::DEST_BROWSER,
            'filename' => $model->policy_number . '.pdf',
            'content' => $this->renderPartial('travelpdf', ['travel_id' => $id]),
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '@font-face {
  font-family: "SFProDisplay";
  src: url("/fonts/SFProDisplay-Regular.ttf");
  font-style: normal;
  font-weight: normal;
}
body  {
  font-family: SFProDisplay;
 /* background-color: #fff;*/
}
.pdf .header {
    color: #003574;
    border-left: 4px solid #003574;
    font-size: 20px;
}
td, th {
    font-size: 12px;
}
.pdf .upper {
    text-transform: uppercase;
}
.pdf th {
    background-color: #efefef;
}
.divtable .divcell {
    border: 1px solid #dee2e6;
    padding: 0.75rem !important;
}
.divtable .brn {
    border-right: none;
}
.divtable .btopn {
    border-top: none;
}
td,th {
    padding: 8px;
}
.pdf h3 {
    color: #003574;
}
.pechat {
    margin-top: -110px;
    width: 140px;
    margin-left: 50px;
    margin-right: -10px;
}
.podpis {
    margin-top: -30px;
    margin-left: -100px;
    width: 100%;
}
.round-flag {
    width: 10px;
}
.assistance {
    font-size: 14px;
    font-weight: bold;
    border-bottom: 1px solid #dee2e6;
}
.assistance .round-flag{
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 10px;
    margin-right: 5px;
}
.assistance h5 {
    color: #212529;
}
.assistance .contacts .c-icon{
    display: inline-block;
    width: 24px;
    height: 24px;
    border-radius: 12px;
    margin-right: 5px;
    border: 1px solid #212529;
    text-align: center;
    padding-top: 2px;
}',
            // any css to be embedded if required
            'options' => [
                // any mpdf options you wish to set
            ],
            'methods' => [
                /*'SetTitle' => 'Privacy Policy - Krajee.com',
                'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => ['Krajee Privacy Policy||Generated On: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
                'SetAuthor' => 'Kartik Visweswaran',
                'SetCreator' => 'Kartik Visweswaran',
                'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',*/
            ]
        ]);
        return $pdf->render();
    }

    public function actionTravelpdf()
    {
        return $this->render('travelpdf');
    }
    private function getPdf($id)
    {

    }

    public function actionGenKaskoPdfForView($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;

        $pdf = PdfHelper::genKaskoPolicyPdf($id);
        $pdf->destination = Pdf::DEST_BROWSER;
        return $pdf->render();
    }

    public function actionKaskopdf()
    {
        return $this->render('kaskopdf2', ['id' => $this->request->get("id")]);
    }
}
