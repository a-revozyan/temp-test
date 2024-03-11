<?php

namespace saas\controllers;

use common\models\User;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\helpers\VarDumper;
use yii\web\Controller;

/**
 * @OA\Info(title="Saas",version="0.1")
 *
 * @OA\Parameter (
 *     parameter="language",
 *     in="header",
 *     name="Accept-Language",
 *     description="choose language of response",
 *     @OA\Schema (enum={"uz", "en", "ru"})
 * ),
 *
 * @OA\Parameter (
 *     parameter="page",
 *     in="query",
 *     name="page",
 *     example=2,
 *     description="number of page",
 *     @OA\Schema (type="integer")
 * ),
 *
 * @OA\Parameter (
 *      in="query",
 *      name="id",
 *      example=1,
 *      description="ID of model which you want to get",
 *      @OA\Schema (type="integer")
 * ),
 *
 * @OA\Schemas (
 *     @OA\Schema (
 *          schema="pages",
 *          type="object",
 *
 *          @OA\Property(property="pageParam", type="string", example="page"),
 *          @OA\Property(property="pageSizeParam", type="string", example="per-page"),
 *          @OA\Property(property="forcePageParam", type="boolean", example=true),
 *          @OA\Property(property="route", type="string|null", example=null),
 *          @OA\Property(property="params", type="string|null", example=null),
 *          @OA\Property(property="urlManager", type="string|null", example=null),
 *          @OA\Property(property="validatePage", type="boolean", example=true),
 *          @OA\Property(property="totalCount", type="integer", example=23),
 *          @OA\Property(property="defaultPageSize", type="integer", example=10),
 *          @OA\Property(property="pageSizeLimit", type="array", @OA\Items(type="integer"), example="[1,50]"),
 *     ),
 *
 *      @OA\Schema (
 *          schema="id_name",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="name", type="string", example="example name"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="auto_brand",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="name", type="string", example="chevrolet"),
 *     ),
 *
 *    @OA\Schema (
 *          schema="auto_model",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="name", type="string", example="matiz"),
 *          @OA\Property(property="auto_model", type="object", ref="#/components/schemas/auto_brand"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="client",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="name", type="string", example="Jobir Yusupov"),
 *          @OA\Property(property="phone", type="string", example="998946464400"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="car_inspection_file",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="url", type="string", example="http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4"),
 *          @OA\Property(property="type", type="string", example="0", description="0 => video, 1 => position1, 2 => position2"),
 *     ),
 *
 *      @OA\Schema (
 *          schema="car_inspection",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="uuid", type="string", example="sdf456fg-fghjk765-hgf456"),
 *          @OA\Property(property="autonumber", type="string", example="80U950JA"),
 *          @OA\Property(property="vin", type="string", example="12345644"),
 *          @OA\Property(property="auto_model", type="object", ref="#/components/schemas/auto_model"),
 *          @OA\Property(property="client", type="object", ref="#/components/schemas/client"),
 *          @OA\Property(property="status", type="string", example="998946464400"),
 *          @OA\Property(property="created_at", type="string", example="03/06/2023 01:23:37"),
 *          @OA\Property(property="send_invite_sms_date", type="string", example="03/06/2023 01:23:37"),
 *          @OA\Property(property="send_verification_sms_date", type="string", example="03/06/2023 01:23:37"),
 *          @OA\Property(property="car_inspection_files", type="array", @OA\Items(type="object", ref="#components/schemas/car_inspection_file")),
 *          @OA\Property(property="pdf_url", type="string", example="https://asdf/asdf/asdf.asdf"),
 *          @OA\Property(property="seconds_till_next_verification_sms", type="integer", example="300"),
 *          @OA\Property(property="longitude", type="float|null", example="256.452"),
 *          @OA\Property(property="latitude", type="float|null", example="300"),
 *          @OA\Property(property="monitoring_url", type="string|null", example="https://monitoring-staging.sugurtabozor.uz/uz/b97edccd-19e2-4b7d-a5b1-0ac745ac1eb9"),
 *     ),
 *
 *      @OA\Schema (
 *          schema="error_400",
 *          type="object",
 *
 *          @OA\Property(property="error", type="object",
 *              @OA\Property(property="message", type="string", example="some error message"),
 *              @OA\Property(property="code", type="integer", example=0),
 *          ),
 *     ),
 *
 *     @OA\Schema (
 *          schema="error_422",
 *          type="object",
 *
 *          @OA\Property(property="error", type="object", example="{'autonumber': [
 *                                                                       'Необходимо заполнить «autonumber».'
 *                                                                  ]}"),
 *     ),
 *
 * @OA\Response(response="error_400", description="If request do not meet the requirements",
 *      @OA\JsonContent(ref="#/components/schemas/error_400")
 * ),
 *
 * @OA\Response(response="error_422", description="Validation error",
 *      @OA\JsonContent(ref="#/components/schemas/error_422")
 * )
 *
 * @OA\Response(response="error_404", description="If some data not found from database",
 *      @OA\JsonContent(ref="#/components/schemas/error_400")
 * ),
 *
 * @OA\Response(response="error_401", description="Unauthorized error, use basic auth",
 *      @OA\JsonContent(ref="#/components/schemas/error_400")
 * ),
 *
 */

class BaseController extends Controller
{
    public $lang;
    public $request;
    public $post;
    public $put;
    public $get;
    public $put_or_post_or_get;
    public $delete;
    public $headers;
    public $enableCsrfValidation = false;
    public $serializer = 'yii\rest\Serializer';
    const LANG = [
        "en" => "en-US",
        "ru" => "ru-RU",
        "uz" => "uz-UZ",
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => [
                    'https://www.sugurtabozor.uz/',
                    'https://www.staging.sugurtabozor.uz/',
                    'https://www.sugurtabozori.uz/',
                    'https://www.staging.sugurtabozori.uz/',
                    'https://staging-saas.sugurtabozor.uz',
                    'https://saas.sugurtabozor.uz',
                    'http://localhost:3000'
                ],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => []
            ]
        ];

        $behaviors['basicAuth'] = [
            'class' => \yii\filters\auth\HttpBasicAuth::class,
            'auth' => function ($username, $password) {
                $user = User::find()
                    ->where(['phone' => $username])
                    ->andWhere(['role' => User::ROLES['partner']])
                    ->one();
                if ($user && $user->validatePassword($password)) {
                    return $user;
                }
                return null;
            },
        ];

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBasicAuth::class,
                HttpBearerAuth::class
            ],
        ];

        return $behaviors;
    }

    public function init()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->request = json_decode(file_get_contents('php://input'), true);
        $this->post = Yii::$app->request->post();
        $this->get = Yii::$app->request->get();
        $this->put = (array)json_decode(\Yii::$app->request->rawBody);

        $this->put_or_post_or_get = $this->put;
        if (empty($this->put_or_post_or_get))
            $this->put_or_post_or_get = $this->post;
        if (empty($this->put_or_post_or_get))
            $this->put_or_post_or_get = $this->get;

        if ($this->request && !is_array($this->request)) {
            Yii::$app->api->sendFailedResponse(['Invalid Json']);
        }

        $this->headers = Yii::$app->getRequest()->getHeaders();
        $accept_language = $this->headers->get('accept-language');
        $this->lang = "ru";
        if ($accept_language && in_array($accept_language, array_keys(self::LANG))) {
            Yii::$app->language = self::LANG[$accept_language];
            $this->lang = $accept_language;
        }
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        return $this->serializeData($result);
    }

    public function sendSuccessResponse($data = false, $additional_info = false, $status = 1)
    {
        $this->setHeader(200);

        $response = [];
        $response['status'] = $status;

        if ($data !== false)
            $response['data'] = $data;

        if ($additional_info) {
            $response = array_merge($response, $additional_info);
        }
        return $response;
    }

    public function sendFailedResponse($errors, $status_code = 400)
    {
        $this->setHeader($status_code);

        return [
            'errors' => $errors
        ];
    }

    protected function setHeader($status)
    {
        $text = $this->_getStatusCodeMessage($status);

        Yii::$app->response->setStatusCode($status, $text);

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $text;
        $content_type = "application/json; charset=utf-8";

        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . "sugurtabozor");
        header('Access-Control-Allow-Origin:*');
    }

    protected function _getStatusCodeMessage($status)
    {
        $codes = array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return $codes[$status] ?? '';
    }

    protected function serializeData($data)
    {
        return Yii::createObject($this->serializer)->serialize($data);
    }
}
