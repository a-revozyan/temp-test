<?php

namespace backapi\controllers;

use mdm\admin\components\Helper;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * @OA\Info(title="Backapi", version="0.1")
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Use user/login to get token",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     securityScheme="bearerAuth"
 * )
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
 *      description="ID of model",
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
 *          schema="admin_panel_users",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="name", type="string", example="example name"),
 *          @OA\Property(property="username", type="string", example="admin"),
 *          @OA\Property(property="email", type="string", example="jobiryusupov0@gmail.com"),
 *          @OA\Property(property="status", type="integer", example=10),
 *          @OA\Property(property="created_at", type="string", example="27.08.2020"),
 *          @OA\Property(property="update_at", type="string", example="27.08.2020"),
 *          @OA\Property(property="phone_number", type="string|null", example=null),
 *     ),
 *
 *     @OA\Schema (
 *          schema="admin_panel_user_profile",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=1),
 *           @OA\Property (property="first_name", type="string|null", example=null),
 *           @OA\Property (property="last_name", type="string|null", example=null),
 *           @OA\Property (property="phone_number", type="string|null", example=null),
 *           @OA\Property (property="email", type="string", example="jobiryusupov0@gmail.com"),
 *           @OA\Property (property="address", type="string", example="Jondor"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="f_user",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=1),
 *           @OA\Property (property="first_name", type="string|null", example=null),
 *           @OA\Property (property="last_name", type="string|null", example=null),
 *           @OA\Property (property="phone_number", type="string|null", example=null),
 *           @OA\Property (property="email", type="string", example="jobiryusupov0@gmail.com"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="f_user_in_marketing",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=1),
 *           @OA\Property (property="name", type="string|null", example="Jobir Yusupov"),
 *           @OA\Property (property="phone", type="string|null", example=null),
 *           @OA\Property (property="age", type="integer", example=25),
 *           @OA\Property (property="total_amount_uzs", type="integer", example=250000),
 *           @OA\Property (property="createda_at", type="string", example="30.01.2023"),
 *           @OA\Property (property="last_payed_date", type="string", example="30.01.2023"),
 *           @OA\Property (property="total_products_count", type="integer", example=5),
 *           @OA\Property (property="products", type="string", example="1, 2", description="sotuvlar sahifasidagi kabi kasko, osago deb chiqarish kerak"),
 *           @OA\Property (property="comment", type="string", example="bu klient bilan o'zbekcha gaplashish kerak"),
 *           @OA\Property (property="is_telegram", type="integer", example="0"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="f_user_for_agent",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=1),
 *           @OA\Property(property="phone", type="string", example="998947777839"),
 *           @OA\Property (property="contract_number", type="string", example="RT123456"),
 *           @OA\Property (property="created_at", type="integer", example=1667639301),
 *           @OA\Property (property="first_name", type="string", example="Jobir"),
 *           @OA\Property (property="last_name", type="string", example="Yusupov"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="agent_file",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=1),
 *           @OA\Property (property="path", type="string", example="https://api.sugurtabozor.uz/admin/uploads/agent/files/17/1-ej2sN.jpg"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="agent_product_coeff",
 *          type="object",
 *
 *           @OA\Property(property="product_id", type="integer", example=1),
 *           @OA\Property (property="coeff", type="float", example=12.4),
 *     ),
 *
 *     @OA\Schema (
 *          schema="agent",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=1),
 *           @OA\Property (property="inn", type="string", example="12346jhvgbh6543"),
 *           @OA\Property (property="contract_number", type="string", example="RT123456"),
 *           @OA\Property (property="logo", type="string", example="https://api.sugurtabozor.uz/admin/uploads/agent/logo/11-User11/download (1)-HgVXL.png"),
 *           @OA\Property (property="user", type="object", ref="#components/schemas/f_user_for_agent"),
 *           @OA\Property (property="agentFiles", type="array", @OA\Items(type="object", ref="#components/schemas/agent_file")),
 *           @OA\Property (property="agentProductCoeffs", type="array", @OA\Items(type="object", ref="#components/schemas/agent_product_coeff")),
 *     ),
 *
 *     @OA\Schema (
 *           schema="story",
 *           type="object",
 *
 *            @OA\Property(property="id", type="integer", example=1),
 *            @OA\Property (property="name", type="string", example="osago"),
 *            @OA\Property (property="type", type="integer", description="'story' => 0,'reel' => 1,", example="1"),
 *            @OA\Property (property="status", type="integer", description="'draft' => 0, 'ready' => 1,", example="1"),
 *            @OA\Property (property="priority", type="integer", example="2"),
 *            @OA\Property (property="begin_period", type="string", example="30.08.2024"),
 *            @OA\Property (property="end_period", type="string", example="30.09.2024"),
 *            @OA\Property (property="begin_time", type="string", example="17:24"),
 *            @OA\Property (property="end_time", type="string", example="19:50"),
 *            @OA\Property (property="weekdays", type="array", @OA\Items(type="integer", example="2")),
 *            @OA\Property (property="view_condition", type="integer", example="2"),
 *            @OA\Property (property="period_status", type="integer", example="1"),
 *            @OA\Property (property="files", type="array", @OA\Items(type="object", ref="#components/schemas/story_file")),
 *            @OA\Property (property="cover", type="object", ref="#components/schemas/story_file"),
 *      ),
 *
 *      @OA\Schema (
 *           schema="story_file",
 *           type="object",
 *
 *            @OA\Property(property="id", type="integer", example=1),
 *            @OA\Property(property="type", type="integer", example="'file' => 0, 'cover' => 1,"),
 *            @OA\Property (property="path", type="string", example="https://api.sugurtabozor.uz/admin/uploads/agent/files/17/1-ej2sN.jpg"),
 *      ),
 *
 *     @OA\Schema (
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
 *     @OA\Schema (
 *          schema="agent_for_table",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=1),
 *           @OA\Property (property="first_name", type="string|null", example="Jobir"),
 *           @OA\Property (property="last_name", type="string|null", example="Yusupov"),
 *           @OA\Property (property="created_at", type="integer", example=1661611076),
 *           @OA\Property (property="inn", type="string|null", example=null),
 *           @OA\Property (property="product_ids", type="string|null", example="1,2,3"),
 *           @OA\Property (property="policy_count", type="integer", example=0),
 *           @OA\Property (property="policy_amount", type="integer", example=0),
 *           @OA\Property (property="product_policy_amount_uzs", type="integer", example=0),
 *           @OA\Property (property="status", type="integer", example=10),
 *           @OA\Property (property="contract_number", type="string", example="33edfdfdfd"),
 *           @OA\Property (property="logo", type="string", example="/uploads/agent/logo/11-User11/download (1)-HgVXL.png"),
 *           @OA\Property (property="phone", type="string|null", example="111111111"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="agent_get_by_id",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=1),
 *           @OA\Property (property="first_name", type="string|null", example="Jobir"),
 *           @OA\Property (property="last_name", type="string|null", example="Yusupov"),
 *           @OA\Property (property="created_at", type="integer", example=1661611076),
 *           @OA\Property (property="inn", type="string|null", example=null),
 *           @OA\Property (property="product_ids", type="string|null", example="1,2,3"),
 *           @OA\Property (property="policy_count", type="integer", example=0),
 *           @OA\Property (property="policy_amount", type="integer", example=0),
 *           @OA\Property (property="product_policy_amount_uzs", type="integer", example=0),
 *           @OA\Property (property="status", type="integer", example=10),
 *           @OA\Property (property="contract_number", type="string", example="33edfdfdfd"),
 *           @OA\Property (property="logo", type="string", example="/uploads/agent/logo/11-User11/download (1)-HgVXL.png"),
 *           @OA\Property (property="phone", type="string|null", example="111111111"),
 *
 *           @OA\Property (property="agentFiles", type="array", @OA\Items(type="object", ref="#components/schemas/agent_file")),
 *           @OA\Property (property="agentProductCoeffs", type="array", @OA\Items(type="object", ref="#components/schemas/agent_product_coeff"))
 *     ),
 *
 *     @OA\Schema (
 *          schema="product",
 *          type="object",
 *
 *           @OA\Property(property="product_id", type="integer", example=270, description="kasko, osago yoki travel id si"),
 *           @OA\Property (property="policy_generated_date", type="integer|null", example=1667479390),
 *           @OA\Property (property="status", type="integer|null", example=4),
 *           @OA\Property (property="product", type="integer", example=1, description="osago => 1, kasko => 2, travel => 3"),
 *           @OA\Property (property="policy_number", type="string|null", example=null),
 *           @OA\Property (property="amount_uzs", type="integer|null", example=168000),
 *           @OA\Property (property="payment_type", type="string|null", example="payze", description="payze, click, payze, ..."),
 *           @OA\Property (property="partner_name", type="string", example="Gross Insurance"),
 *           @OA\Property (property="partner_id", type="integer", example=1),
 *           @OA\Property (property="f_user_id", type="integer", example=31),
 *           @OA\Property (property="f_user_name", type="string", example="Jobir"),
 *           @OA\Property (property="insurer_phone", type="string|null", example=null),
 *           @OA\Property (property="insurer_name", type="string|null", example=null),
 *           @OA\Property (property="region", type="string|null", example="01"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="product_for_call_center",
 *          type="object",
 *
 *           @OA\Property(property="product_id", type="integer", example=270, description="kasko, osago yoki travel id si"),
 *           @OA\Property (property="created_at", type="integer|null", example=1667479390),
 *           @OA\Property (property="status", type="integer|null", example=4),
 *           @OA\Property (property="product", type="integer", example=1, description="osago => 1, kasko => 2, travel => 3"),
 *           @OA\Property (property="f_user_id", type="integer", example=31),
 *           @OA\Property (property="f_user_phone", type="string", example="Jobir"),
 *           @OA\Property (property="reason", type="string", example="puli qimmat ekan"),
 *           @OA\Property (property="comment", type="string", example="taksis ekan"),
 *           @OA\Property (property="autonumber", type="string", example="80U950Ja"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="region_short",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=4),
 *           @OA\Property (property="name", type="string", example="Tashkent"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="osago_driver",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=4),
 *           @OA\Property (property="passport_series", type="string", example="KA"),
 *           @OA\Property (property="passport_number", type="string", example=0829728),
 *           @OA\Property (property="license_series", type="string|null", example=null),
 *           @OA\Property (property="license_number", type="string|null", example=null),
 *           @OA\Property (property="relationship", type="string|null", example="Сын"),
 *           @OA\Property (property="relationship_id", type="integer|null", example=7),
 *           @OA\Property (property="birthday", type="integer", example=932708849),
 *     ),
 *
 *     @OA\Schema (
 *          schema="number_drivers",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=4),
 *           @OA\Property (property="name", type="string", example="cheklanmagan"),
 *           @OA\Property (property="description", type="string|null", example=null),
 *     ),
 *
 *     @OA\Schema (
 *           schema="setting",
 *           type="object",
 *
 *            @OA\Property(property="id", type="integer", example=4),
 *            @OA\Property (property="name", type="string", example="car price limit"),
 *            @OA\Property (property="description", type="string|null", example=null),
 *            @OA\Property (property="updated_at", type="string|null", example="2023-09-12 12:55:00"),
 *      ),
 *
 *     @OA\Schema (
 *          schema="region",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=4),
 *           @OA\Property (property="name_ru", type="string", example="Tashkent"),
 *           @OA\Property (property="name_en", type="string", example="Tashkent"),
 *           @OA\Property (property="name_uz", type="string", example="Toshkent"),
 *           @OA\Property (property="coeff", type="string", example="1.23"),
 *     ),
 *
 *    @OA\Schema (
 *          schema="partner",
 *          type="object",
 *
 *           @OA\Property (property="id", type="integer", example=4),
 *           @OA\Property (property="name", type="string", example="partner name"),
 *           @OA\Property (property="contract_number", type="string", example="dfgh54"),
 *           @OA\Property (property="created_at", type="integer", example=1667891840),
 *           @OA\Property (property="updated_at", type="integer", example=1667891840),
 *           @OA\Property (property="status", type="string", example=1),
 *           @OA\Property (property="image", type="string", example="http://127.0.0.1:20080/uploads/partners/partner1667891840.jpg"),
 *           @OA\Property (property="travel_offer_file", type="string", example="http://127.0.0.1:20080/uploads/partners/partner1667891840.jpg"),
 *     ),
 *
 *      @OA\Schema (
 *          schema="autobrand",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name", type="string", example="Chevrolet"),
 *          @OA\Property(property="order", type="integer|null", example=3),
 *          @OA\Property(property="status", type="integer", example=1),
 *     ),
 *
 *     @OA\Schema (
 *          schema="automodel",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name", type="string", example="Chevrolet"),
 *          @OA\Property(property="order", type="integer|null", example=3),
 *          @OA\Property(property="status", type="integer", example=1),
 *          @OA\Property(property="autobrand", type="object", ref="#components/schemas/id_name"),
 *          @OA\Property(property="autoRiskType", type="object", ref="#components/schemas/id_name"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="warehouse",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="series", type="string", example="GSS"),
 *          @OA\Property(property="number", type="string", example="2345235"),
 *          @OA\Property(property="status", type="integer", example=1, description="new => 0, reserve => 1, paid => 2, cancel => 3"),
 *          @OA\Property(property="partner", type="object", ref="#components/schemas/id_name"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="automodel_short",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name", type="string", example="Chevrolet"),
 *          @OA\Property(property="autobrand", type="object", ref="#components/schemas/id_name"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="auto",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name", type="string", example="X-Line 2.5 GDI / 190 л.с."),
 *          @OA\Property(property="status", type="integer", example=1),
 *          @OA\Property(property="automodel", type="object", ref="#components/schemas/automodel_short"),
 *          @OA\Property(property="partners", type="object", ref="#components/schemas/id_name"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="kasko_risk",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name_en", type="string", example="Railway accidents"),
 *          @OA\Property(property="name_uz", type="string", example="Railway accidents"),
 *          @OA\Property(property="name_ru", type="string", example="Railway accidents"),
 *          @OA\Property(property="description_en", type="string", example="ДТП в результате проезда железнодорожных переездов"),
 *          @OA\Property(property="description_ru", type="string", example="ДТП в результате проезда железнодорожных переездов"),
 *          @OA\Property(property="description_uz", type="string", example="ДТП в результате проезда железнодорожных переездов"),
 *          @OA\Property(property="amount", type="float|null", example=null),
 *          @OA\Property(property="show_desc", type="integer", example=0),
 *          @OA\Property(property="category", type="object", ref="#components/schemas/id_name"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="osago",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="period", type="object", ref="#components/schemas/id_name"),
 *          @OA\Property(property="region", type="object", ref="#components/schemas/id_name"),
 *          @OA\Property(property="autonumber", type="string", example="01Y195DA"),
 *          @OA\Property(property="amount_uzs", type="integer", example=168000),
 *          @OA\Property(property="status", type="integer", example=1),
 *          @OA\Property(property="promo_id", type="integer|null", example=null),
 *          @OA\Property(property="promo_percent", type="float|null", example=null),
 *          @OA\Property(property="promo_amount", type="integer|null", example=null),
 *          @OA\Property(property="f_user_is_owner", type="boolean", example=true),
 *          @OA\Property(property="payed_date", type="integer|null", example=null),
 *          @OA\Property(property="policy_pdf_url", type="string|null", example="https://gross.uz/bla-bla/qwerty"),
 *          @OA\Property(property="policy_number", type="string|null", example="12qertywer"),
 *          @OA\Property(property="applicant_is_driver", type="boolean|null", example=null),
 *          @OA\Property(property="begin_date", type="integer|null", example=null),
 *          @OA\Property(property="end_date", type="integer|null", example=null),
 *          @OA\Property(property="numberDrivers", type="object|null", ref="#components/schemas/number_drivers"),
 *          @OA\Property(property="drivers", type="object|null", ref="#components/schemas/osago_driver"),
 *          @OA\Property(property="partner", type="object|null", ref="#components/schemas/id_name"),
 *          @OA\Property(property="created_at", type="integer", example=1668232018),
 *          @OA\Property(property="user", type="object|null", ref="#components/schemas/f_user"),
 *          @OA\Property(property="created_in_telegram", type="boolean", example=1),
 *          @OA\Property(property="accident_policy_pdf_url", type="string", example="https://gross.uz/generate-policy/health-policy/MTQ1Ng=="),
 *          @OA\Property(property="accident_policy_number", type="string", example="HJ 124585"),
 *          @OA\Property(property="insurer_passport_series", type="string", example="AA"),
 *          @OA\Property(property="insurer_passport_number", type="string", example="1234567"),
 *          @OA\Property(property="insurer_tech_pass_series", type="string", example="AA"),
 *          @OA\Property(property="insurer_tech_pass_number", type="string", example="4567878"),
 *     ),
 *
 *      @OA\Schema (
 *          schema="kasko_tariff",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name", type="string", example="test tariff"),
 *          @OA\Property(property="amount", type="float", example=2.3),
 *          @OA\Property(property="file", type="string", example="http://staging.netkost.uz/admin/uploads/kasko-tariff/28-test tariff backapi/response-R-Hqx.xlsx"),
 *          @OA\Property(property="franchise_ru", type="string", example="test ru"),
 *          @OA\Property(property="franchise_uz", type="string", example="test uz"),
 *          @OA\Property(property="franchise_en", type="string", example="test en"),
 *          @OA\Property(property="only_first_risk_en", type="string", example="first risk en"),
 *          @OA\Property(property="only_first_risk_ru", type="string", example="first risk ru"),
 *          @OA\Property(property="only_first_risk_uz", type="string", example="first risk uz"),
 *          @OA\Property(property="is_conditional", type="integer", example=1, description="o yoki 1"),
 *          @OA\Property(property="is_islomic", type="integer", example=1, description="o yoki 1"),
 *          @OA\Property(property="kasko_risks", type="array", @OA\Items(type="object", ref="#components/schemas/id_name")),
 *          @OA\Property(property="partner", type="object", ref="#components/schemas/id_name"),
 *          @OA\Property(property="auto_risk_types", type="array", @OA\Items(type="object",
 *              @OA\Property(property="id", type="integer", example=3),
 *              @OA\Property(property="name", type="string", example="example name"),
 *              @OA\Property(property="amount", type="integer", example="30", description="agar tariff da is_islomic = 1 bo'lsagina keladi. 0 bo'lsa kelmaydi"),
 *          )),
 *          @OA\Property(property="car_accessories", type="array", @OA\Items(
 *               @OA\Property(property="id", type="integer", example=3),
 *               @OA\Property(property="name", type="string", example="example name"),
 *               @OA\Property(property="coeff", type="float", example=3.2),
 *          )),
 *     ),
 *
 *     @OA\Schema (
 *          schema="id_name_status",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name", type="string", example="example text"),
 *          @OA\Property(property="status", type="integer", example=1),
 *     ),
 *
 *     @OA\Schema (
 *          schema="auto_risk_type",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name", type="string", example="X-Line 2.5 GDI / 190 л.с."),
 *          @OA\Property(property="status", type="integer", example=1),
 *     ),
 *
 *     @OA\Schema (
 *          schema="role",
 *          type="object",
 *
 *          @OA\Property(property="name", type="string", example="bridge_company"),
 *          @OA\Property(property="description", type="string", example="description description"),
 *          @OA\Property(property="created_at", type="integer", example=1653511373),
 *     ),
 *
 *     @OA\Schema (
 *          schema="car_accessory",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name_en", type="string", example="Metan gaz"),
 *          @OA\Property(property="name_uz", type="string", example="Metan gaz"),
 *          @OA\Property(property="name_ru", type="string", example="Metan gaz"),
 *          @OA\Property(property="description_en", type="string", example="Metan gaz description"),
 *          @OA\Property(property="description_ru", type="string", example="Metan gaz description"),
 *          @OA\Property(property="description_uz", type="string", example="Metan gaz description"),
 *          @OA\Property(property="amount_min", type="float", example=12.58),
 *          @OA\Property(property="amount_max", type="float", example=78.25),
 *     ),
 *
 *     @OA\Schema (
 *          schema="admin_panel_user_with_roles_in_assignment",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="name", type="string", example="example name"),
 *          @OA\Property(property="username", type="string", example="admin"),
 *          @OA\Property(property="role", type="array", @OA\Items(type="object", ref="#/components/schemas/role")),
 *     ),
 *     
 *     @OA\Schema (
 *          schema="admin_panel_user_in_assignment",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="name", type="string", example="example name"),
 *          @OA\Property(property="username", type="string", example="admin"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="bridge_company",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name", type="string", example="bridge company"),
 *          @OA\Property(property="code", type="string", example="aa12244"),
 *          @OA\Property(property="created_at", type="integer", example=34261346),
 *          @OA\Property(property="updated_at", type="integer", example=12354134),
 *          @OA\Property(property="status", type="integer", example=1),
 *          @OA\Property(property="username", type="string", example="username_company"),
 *          @OA\Property(property="phone_number", type="string", example="998912343434"),
 *          @OA\Property(property="email", type="string", example="company@gmail.com"),
 *          @OA\Property(property="first_name", type="string", example="Jobir"),
 *          @OA\Property(property="last_name", type="string", example="Yusupov"),
 *     ),
 *
 *     @OA\Schema (
 *           schema="bridge_company_with_divvies",
 *           type="object",
 *
 *           @OA\Property(property="id", type="integer", example=1),
 *           @OA\Property(property="name", type="string", example="bridge company"),
 *           @OA\Property(property="code", type="string", example="aa12244"),
 *           @OA\Property(property="created_at", type="integer", example=34261346),
 *           @OA\Property(property="updated_at", type="integer", example=12354134),
 *           @OA\Property(property="status", type="integer", example=1),
 *           @OA\Property(property="username", type="string", example="username_company"),
 *           @OA\Property(property="phone_number", type="string", example="998912343434"),
 *           @OA\Property(property="email", type="string", example="company@gmail.com"),
 *           @OA\Property(property="first_name", type="string", example="Jobir"),
 *           @OA\Property(property="last_name", type="string", example="Yusupov"),
 *           @OA\Property(property="divvies", type="array", @OA\Items(type="object", ref="#/components/schemas/bridge_company_divvy")),
 *      ),
 *
 *      @OA\Schema (
 *            schema="bridge_company_divvy",
 *            type="object",
 *
 *            @OA\Property(property="id", type="integer", example=3),
 *            @OA\Property(property="partner", type="object", ref="#/components/schemas/id_name"),
 *            @OA\Property(property="product", type="object", ref="#/components/schemas/id_name"),
 *            @OA\Property(property="number_drivers", type="object", ref="#/components/schemas/id_name"),
 *            @OA\Property(property="month", type="string", ref="2023-10"),
 *            @OA\Property(property="percent", type="float", ref="10.22"),
 *      ),
 *
 *     @OA\Schema (
 *          schema="full_sms_template",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="text", type="string", example="bridge company"),
 *          @OA\Property(property="method", type="string", example="sendPhoto", description = "sendMessage, sendPhoto, sendDocument, sendVideo"),
 *          @OA\Property(property="file_url", type="string", example="http://asdf/asdf/asf.png"),
 *          @OA\Property(property="region_car_numbers", type="array", @OA\Items(type="integer", example=20)),
 *          @OA\Property(property="number_drivers", type="integer", example=1),
 *          @OA\Property(property="registered_from_date", type="string", example="2022-12-24 15:20:01"),
 *          @OA\Property(property="registered_till_date", type="string", example="2022-12-24 15:20:01"),
 *          @OA\Property(property="bought_from_date", type="string", example="2022-12-24 15:20:01"),
 *          @OA\Property(property="bought_till_date", type="string", example="2022-12-24 15:20:01"),
 *          @OA\Property(property="type", type="string", example="2", description="'first_telegram_else_sms' => 1,'users_which_have_telegram_via_telegram' => 2,'users_which_have_not_telegram_via_sms' => 3,'all_users_via_sms' => 4,"),
 *          @OA\Property(property="begin_date", type="string", example="2022-12-24 15:20:01"),
 *          @OA\Property(property="end_date", type="string", example="2022-12-24 15:20:01"),
 *          @OA\Property(property="created_at", type="string", example="2022-12-24 15:20:01"),
 *          @OA\Property(property="all_users_count", type="integer", example="200"),
 *          @OA\Property(property="sms_count", type="integer", example="198"),
 *          @OA\Property(property="status", type="integer", example="2", description="'created' => 0,'started' => 1,'paused' => 2,'ended' => 3,'archived' => 4,"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="sms_template",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="text", type="string", example="bridge company"),
 *          @OA\Property(property="method", type="string", example="sendPhoto", description = "sendMessage, sendPhoto, sendDocument, sendVideo"),
 *          @OA\Property(property="file_url", type="string", example="http://asdf/asdf/asf.png"),
 *          @OA\Property(property="type", type="string", example="2", description="'first_telegram_else_sms' => 1,'users_which_have_telegram_via_telegram' => 2,'users_which_have_not_telegram_via_sms' => 3,'all_users_via_sms' => 4,"),
 *          @OA\Property(property="begin_date", type="string", example="2022-12-24 15:20:01"),
 *          @OA\Property(property="end_date", type="string", example="2022-12-24 15:20:01"),
 *          @OA\Property(property="created_at", type="string", example="2022-12-24 15:20:01"),
 *          @OA\Property(property="all_users_count", type="integer", example="200"),
 *          @OA\Property(property="sms_count", type="integer", example="198"),
 *          @OA\Property(property="status", type="integer", example="2", description="'created' => 0,'started' => 1,'paused' => 2,'ended' => 3,'archived' => 4,"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="opinion",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="phone", type="string", example="998946464400"),
 *          @OA\Property(property="name", type="string", example="Jobir"),
 *          @OA\Property(property="message", type="string", example="zo'r sayt ekan"),
 *          @OA\Property(property="created_at", type="string", example="25.01.2023 15:20:50"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="kasko_by_subscription",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="applicant_name", type="string|null", example="Jobir Yusupov"),
 *          @OA\Property(property="phone", type="string", example="998946464400"),
 *          @OA\Property(property="amount_uzs", type="integer", example=60000),
 *          @OA\Property(property="autonumber", type="string|null", example="01Y195DA"),
 *          @OA\Property(property="tech_pass_series", type="string|null", example="AAF"),
 *          @OA\Property(property="tech_pass_number", type="string|null", example="0390422"),
 *          @OA\Property(property="payment_type", type="string|null", example="payme"),
 *          @OA\Property(property="status", type="integer", example=2),
 *          @OA\Property(property="policies_count", type="integer", example=2),
 *          @OA\Property(property="saved_card", type="object", ref="#components/schemas/saved_card"),
 *          @OA\Property(property="last_kasko_by_subscription_policy", type="object",
 *              @OA\Property(property="id", type="integer", example="15"),
 *              @OA\Property(property="begin_date", type="string", example="25.01.2023"),
 *              @OA\Property(property="end_date", type="string", example="24.02.2023"),
 *              @OA\Property(property="policy_pdf_url", type="string", example="https://gross.uz/asdf/asdf"),
 *          ),
 *     ),
 *
 *     @OA\Schema (
 *          schema="saved_card",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="card_mask", type="string", example="860049******6478"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="qa",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="question_uz", type="string", example="kasko bu nima?"),
 *          @OA\Property(property="question_ru", type="string", example="shto takoy kasko?"),
 *          @OA\Property(property="answer_uz", type="string", example="ixtiyoriy sug'urta"),
 *          @OA\Property(property="answer_ru", type="string", example="ixtiyoriy sug'urta"),
 *          @OA\Property(property="status", type="integer", example="1",  description="1 => active, 0 => inactive"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="promo",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="code", type="string", example="123456"),
 *          @OA\Property(property="amount", type="string", example=-35000),
 *          @OA\Property(property="begin_date", type="string", example="21.10.2033"),
 *          @OA\Property(property="end_date", type="string", example="21.11.2033"),
 *          @OA\Property(property="amount_type", type="integer", example="1",  description="1 => fixed, 0 => percent"),
 *          @OA\Property(property="status", type="integer", example="1",  description="1 => active, 0 => inactive"),
 *          @OA\Property(property="number", type="integer", example="10",  description="nechta promocode borligi"),
 *          @OA\Property (property="products", type="object", ref="#components/schemas/id_name"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="surveyer",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="first_name", type="string", example="Jobir"),
 *          @OA\Property(property="last_name", type="string", example="Yusupov"),
 *          @OA\Property(property="region_name", type="string", example="Buxoro"),
 *          @OA\Property(property="created_at", type="integer", example="1658840738"),
 *          @OA\Property(property="kasko_count", type="integer", example="10"),
 *          @OA\Property(property="average_processed_time", type="integer|null", example="1658840738"),
 *          @OA\Property(property="phone_number", type="string", example="998946464400"),
 *          @OA\Property (property="status", type="integer", example="1"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="status_history",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="from_status", type="integer|null", example="1"),
 *          @OA\Property(property="to_status", type="integer|null", example="2"),
 *          @OA\Property(property="created_at", type="string|null", example="19.05.2023 11:53:50"),
 *          @OA\Property(property="user", type="object|null", ref="#components/schemas/admin_panel_users_short"),
 *          @OA\Property(property="comment", type="string|null", example="klient cancel qilishni so'radi"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="admin_panel_users_short",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="name", type="string", example="example name"),
 *          @OA\Property(property="username", type="string", example="admin"),
 *          @OA\Property(property="phone_number", type="string|null", example=null),
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
 *          @OA\Property(property="auto_brand", type="object", ref="#/components/schemas/auto_brand"),
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
 *          @OA\Property(property="type", type="string", example="0", description="0 => video, 1 => image"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="car_inspection",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="uuid", type="string", example="sdf456fg-fghjk765-hgf456"),
 *          @OA\Property(property="autonumber", type="string", example="80U950JA"),
 *          @OA\Property(property="vin", type="string", example="12345644"),
 *          @OA\Property(property="runway", type="integer", example="12345644"),
 *          @OA\Property(property="auto_model", type="object", ref="#/components/schemas/auto_model"),
 *          @OA\Property(property="client", type="object", ref="#/components/schemas/client"),
 *          @OA\Property(property="status", type="string", example="998946464400"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="full_car_inspection",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="uuid", type="string", example="sdf456fg-fghjk765-hgf456"),
 *          @OA\Property(property="autonumber", type="string", example="80U950JA"),
 *          @OA\Property(property="vin", type="string", example="12345644"),
 *          @OA\Property(property="runway", type="integer", example="12345644"),
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
 *     ),
 *
 *     @OA\Schema (
 *          schema="partner_account",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="partner", type="object", ref="#components/schemas/id_name"),
 *          @OA\Property(property="amount", type="integer", example="2000000"),
 *          @OA\Property(property="note", type="string", example="no3 to'lov"),
 *          @OA\Property(property="user", type="object", ref="#/components/schemas/auto_model"),
 *          @OA\Property(property="client", type="object", ref="#/components/schemas/client"),
 *          @OA\Property(property="created_at", type="string", example="25.12.2022"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="car_price_excel",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="path", type="string", example="https://asdfasf.com/asdf"),
 *          @OA\Property(property="created_at", type="string", example="30.05.2023 14:20:01"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="translate",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="ru", type="string", example="ru text"),
 *          @OA\Property(property="uz", type="string", example="uz text"),
 *          @OA\Property(property="en", type="string", example="en text"),
 *     ),
 *
 *     @OA\Schema (
 *           schema="news",
 *           type="object",
 *
 *           @OA\Property(property="id", type="integer", example=1),
 *           @OA\Property(property="title_uz", type="string|null", example="text"),
 *           @OA\Property(property="title_ru", type="string|null", example="text"),
 *           @OA\Property(property="title_en", type="string|null", example="text"),
 *           @OA\Property(property="image_uz", type="string|null", example="url"),
 *           @OA\Property(property="image_ru", type="string|null", example="url"),
 *           @OA\Property(property="image_en", type="string|null", example="url"),
 *           @OA\Property(property="short_info_uz", type="string|null", example="text"),
 *           @OA\Property(property="short_info_ru", type="string|null", example="text"),
 *           @OA\Property(property="short_info_en", type="string|null", example="text"),
 *           @OA\Property(property="body_uz", type="string|null", example="text"),
 *           @OA\Property(property="body_ru", type="string|null", example="text"),
 *           @OA\Property(property="body_en", type="string|null", example="text"),
 *           @OA\Property(property="created_at", type="string", example="30.05.2023 14:19:01"),
 *           @OA\Property(property="updated_at", type="integer", example="30.05.2023 14:19:01"),
 *           @OA\Property(property="status", type="integer", example=1),
 *      ),
 *
 *      @OA\Schema (
 *             schema="tag",
 *             type="object",
 *
 *             @OA\Property(property="id", type="integer", example=3),
 *             @OA\Property(property="name_uz", type="string", example="salom"),
 *             @OA\Property(property="name_ru", type="string", example="salom"),
 *             @OA\Property(property="name_en", type="string", example="salom"),
 *      ),
 * ),
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
 * @OA\Response(response="error_401", description="Unauthorized error, set token by to click 'Authorize' button in the top of the page",
 *      @OA\JsonContent(ref="#/components/schemas/error_400")
 * ),
 */
class BaseController extends Controller
{
    public $row_respons_action_ids = ['export'];
    public $request;
    public $post;
    public $put;
    public $get;
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
                    'https://staging.sugurtabozor.uz/',
                    'http://localhost:3000',
                    //                    'https://www.staging.sugurtabozori.uz/',
                ],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => []
            ]
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

        if ($this->request && !is_array($this->request)) {
            Yii::$app->api->sendFailedResponse(['Invalid Json']);
        }

        $this->headers = Yii::$app->getRequest()->getHeaders();
        $language = $this->headers->get('accept-language');
        if ($language && in_array($language, array_keys(self::LANG))) {
            Yii::$app->language = self::LANG[$language];
        }
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        return $this->serializeData($result);
    }

    public function beforeAction($action)
    {
        $result = parent::beforeAction($action);
        if (in_array($action->id, $this->row_respons_action_ids)) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            Yii::$app->response->headers->set('Content-Disposition',  "attachment; filename=Report.xlsx");
            Yii::$app->response->headers->set('Pragma',  "no-cache");
            Yii::$app->response->headers->set('Expires',  "0");
            // Access-Control-Expose-Headers: Content-Disposition
            Yii::$app->response->headers->set('Access-Control-Expose-Headers',  "Content-Disposition");
        }

        //        $roles = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
        //        $url = Yii::$app->controller->id . "/" . Yii::$app->controller->action->id;
        //        if (!Helper::checkRoute($url) and $url != 'user/login' and !in_array('admin', $roles))
        //            throw new ForbiddenHttpException();

        return $result;
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
        header('X-Powered-By: ' . "Sug'urta");
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
