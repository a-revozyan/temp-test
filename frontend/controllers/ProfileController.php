<?php

namespace frontend\controllers;

use common\models\Kasko;
use common\models\KaskoBySubscriptionPolicy;
use common\models\Osago;
use common\models\Product;
use common\models\Travel;
use frontend\models\ProfileForms\AddStrangerPolicy;
use frontend\models\ProfileForms\CreateUniqueCodeForm;
use frontend\models\ProfileForms\UpdateProfile;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;

class ProfileController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'update-profile' => ['PUT']
            ]
        ];

        return $behaviors;
    }

    /**
     * @OA\Put(
     *     path="/profile/update-profile",
     *     summary="update profile info",
     *     tags={"ProfileController"},
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={},
     *                 @OA\Property (property="first_name", type="string", example="Jobir"),
     *                 @OA\Property (property="last_name", type="string", example="Yusupov"),
     *                 @OA\Property (property="email", type="string", example="jy@gmail.com"),
     *                 @OA\Property (property="gender", type="integer", example="0 => female, 1 => male"),
     *                 @OA\Property (property="birthday", type="string", example="25.11.1992"),
     *                 @OA\Property (property="passport_seria", type="string", example="AA"),
     *                 @OA\Property (property="passport_number", type="string", example="1234567"),
     *                 @OA\Property (property="city_id", type="integer", example="1", description="get from general/cities"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated profile",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/full_user")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionUpdateProfile()
    {
        $model = new UpdateProfile();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->update()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/profile/get-profile-info",
     *     summary="profile info",
     *     tags={"ProfileController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Response(
     *         response="200", description="profile info",
     *         @OA\JsonContent(type="object", ref="#/components/schemas/full_user")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetProfileInfo()
    {
        return \Yii::$app->user->identity->getFullArr();
    }

    /**
     * @OA\Get(
     *     path="/profile/get-products",
     *     summary="get aktiv products",
     *     tags={"ProfileController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Response(
     *         response="200", description="products",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="id", type="integer", example="1234"),
     *              @OA\Property(property="autonumber", type="string|null", example="80U950JA"),
     *              @OA\Property(property="begin_date", type="string|null", example="2023-01-16"),
     *              @OA\Property(property="end_date", type="string|null", example="2023-03-16"),
     *              @OA\Property(property="autoname", type="string|null", example="Chevrolet Cobalt 2 позиция"),
     *              @OA\Property(property="product", type="integer|null", example=2),
     *         )
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetProducts()
    {
        $end_date_compare_sign = ">";
        if (array_key_exists('type', $this->get) and $this->get['type'] == Product::TYPE['old'])
            $end_date_compare_sign = "<";

        $osagos = Osago::find()
            ->select(['id', 'uuid', 'autonumber', 'begin_date', 'end_date', "concat('') as autoname"])
            ->where([
                'and',
                ['in', 'status', [Osago::STATUS['received_policy'], Osago::STATUS['stranger']]],
                ['=', 'f_user_id', \Yii::$app->user->id],
                [$end_date_compare_sign, 'end_date', date('Y-m-d')],
            ])
            ->asArray()
            ->all();
        $kaskos = Kasko::find()
            ->select(['kasko.id', 'kasko.uuid', 'autonumber', 'begin_date', 'end_date', "concat(autobrand.name, ' ' , automodel.name, ' ' , autocomp.name) as autoname"])
            ->where([
                'and',
                ['in', 'kasko.status', [Kasko::STATUS['policy_generated'], Kasko::STATUS['stranger']]],
                ['=', 'f_user_id', \Yii::$app->user->id],
                [$end_date_compare_sign, 'end_date', date('Y-m-d')],
            ])
            ->leftJoin('autocomp', "autocomp.id = kasko.autocomp_id")
            ->leftJoin('automodel', "automodel.id = autocomp.automodel_id")
            ->leftJoin('autobrand', "autobrand.id = automodel.autobrand_id")
            ->asArray()
            ->all();
        $kbsps = KaskoBySubscriptionPolicy::find()
            ->select(['kasko_by_subscription_policy.id', 'kasko_by_subscription.uuid', "concat('') as autonumber", "begin_date", "end_date", "concat('') as autoname"])
            ->leftJoin('kasko_by_subscription', 'kasko_by_subscription.id = kasko_by_subscription_id')
            ->where([
                'and',
                ['in', 'kasko_by_subscription_policy.status', [KaskoBySubscriptionPolicy::STATUS['received_policy']]],
                ['=', 'f_user_id', \Yii::$app->user->id],
                [$end_date_compare_sign, 'end_date', date('Y-m-d')],
            ])
            ->asArray()
            ->all();

        return array_merge(
            array_map(function ($osago){return array_merge($osago, ['product' => Product::products['osago']]);}, $osagos),
            array_map(function ($kasko){return array_merge($kasko, ['product' => Product::products['kasko']]);}, $kaskos),
            array_map(function ($kbsp){return array_merge($kbsp, ['product' => Product::products['kasko-by-subscription']]);}, $kbsps),
        );
    }

    /**
     * @OA\Post(
     *     path="/profile/add-stranger-policy",
     *     summary="create new stranger policy",
     *     tags={"ProfileController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"autonumber", "product", "tech_pass_series", "tech_pass_number", "end_date"},
     *                 @OA\Property (property="autonumber", type="string", example="80Q12445TR"),
     *                 @OA\Property (property="product", type="string", example="1", description="1 yoki 2"),
     *                 @OA\Property (property="tech_pass_series", type="string", example="aa"),
     *                 @OA\Property (property="tech_pass_number", type="integer", example="1234"),
     *                 @OA\Property (property="end_date", type="string", example="30.03.2023"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200", description="product",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="id", type="integer", example="1234"),
     *              @OA\Property(property="autonumber", type="string|null", example="80U950JA"),
     *              @OA\Property(property="begin_date", type="string|null", example="2023-01-16"),
     *              @OA\Property(property="end_date", type="string|null", example="2023-03-16"),
     *              @OA\Property(property="autoname", type="string|null", example="Chevrolet Cobalt 2 позиция"),
     *              @OA\Property(property="product", type="integer|null", example=2),
     *         )
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAddStrangerPolicy()
    {
        $model = new AddStrangerPolicy();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/profile/create-unique-code",
     *     summary="create new unique code",
     *     tags={"ProfileController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"osago_uuid"},
     *                 @OA\Property (property="osago_uuid", type="string", example="qwer123-asdf-22412sf"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200", description="unique-code",
     *         @OA\JsonContent(type="string", example="sdf4542")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreateUniqueCode()
    {
        $model = new CreateUniqueCodeForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}