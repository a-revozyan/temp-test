<?php

namespace frontend\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\helpers\VarDumper;
use yii\web\Controller;

/**
 * @OA\Info(title="Frontend",version="0.1")
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Use userapi/login to get token",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     securityScheme="bearerAuth"
 * ),
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
 *    @OA\Schema (
 *            schema="story",
 *            type="object",
 *
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property (property="name", type="string", example="osago"),
 *             @OA\Property (property="type", type="integer", description="'story' => 0,'reel' => 1,", example="1"),
 *             @OA\Property (property="status", type="integer", description="'draft' => 0, 'ready' => 1,", example="1"),
 *             @OA\Property (property="priority", type="integer", example="2"),
 *             @OA\Property (property="begin_period", type="string", example="30.08.2024"),
 *             @OA\Property (property="end_period", type="string", example="30.09.2024"),
 *             @OA\Property (property="begin_time", type="string", example="17:24"),
 *             @OA\Property (property="end_time", type="string", example="19:50"),
 *             @OA\Property (property="weekdays", type="array", @OA\Items(type="integer", example="2")),
 *             @OA\Property (property="view_condition", type="integer", example="2"),
 *             @OA\Property (property="period_status", type="integer", example="1"),
 *             @OA\Property (property="files", type="array", @OA\Items(type="object", ref="#components/schemas/story_file")),
 *             @OA\Property (property="cover", type="object", ref="#components/schemas/story_file"),
 *       ),
 *
 *       @OA\Schema (
 *            schema="story_file",
 *            type="object",
 *
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="type", type="integer", example="'file' => 0, 'cover' => 1,"),
 *             @OA\Property (property="path", type="string", example="https://api.sugurtabozor.uz/admin/uploads/agent/files/17/1-ej2sN.jpg"),
 *       ),
 *
 *     @OA\Schema (
 *            schema="short_osago",
 *            type="object",
 *
 *            @OA\Property(property="id", type="integer", example=24),
 *            @OA\Property(property="uuid", type="string", example="e112bc5d-1da5-4f0f-8f15-65a26d9655f8"),
 *            @OA\Property(property="autonumber", type="string", example="80U950JA"),
 *            @OA\Property(property="amount_uzs", type="integer", example="40000"),
 *            @OA\Property(property="status", type="integer", example="3"),
 *            @OA\Property(property="payed_date", type="string|null", example="30.10.2023 15:20"),
 *            @OA\Property(property="policy_pdf_url", type="string|null", example="https://polis.e-osgo.uz/site/export-to-pdf?id=e7cf8b5f-f65a-4bd1-83d3-418f18121100"),
 *            @OA\Property(property="begin_date", type="string|null", example="30.10.2023"),
 *            @OA\Property(property="end_date", type="string|null", example="30.10.2024"),
 *       ),
 *
 *    @OA\Schema (
 *           schema="osago",
 *           type="object",
 *
 *           @OA\Property(property="id", type="integer", example=24),
 *           @OA\Property(property="uuid", type="string", example="e112bc5d-1da5-4f0f-8f15-65a26d9655f8"),
 *           @OA\Property(property="period", type="object", ref="#components/schemas/id_name"),
 *           @OA\Property(property="region", type="object", ref="#components/schemas/id_name"),
 *           @OA\Property(property="autonumber", type="string", example="80U950JA"),
 *           @OA\Property(property="amount_uzs", type="integer", example="40000"),
 *           @OA\Property(property="status", type="integer", example="3"),
 *           @OA\Property(property="f_user_is_owner", type="boolean|null", example="true"),
 *           @OA\Property(property="owner_with_accident", type="boolean|null", example="true"),
 *           @OA\Property(property="payed_date", type="string|null", example="2023-10-30 15:20:14"),
 *           @OA\Property(property="policy_number", type="string|null", example="QWER123345"),
 *           @OA\Property(property="policy_pdf_url", type="string|null", example="https://polis.e-osgo.uz/site/export-to-pdf?id=e7cf8b5f-f65a-4bd1-83d3-418f18121100"),
 *           @OA\Property(property="applicant_is_driver", type="boolean|null", example="false"),
 *           @OA\Property(property="begin_date", type="string|null", example="2023-10-30"),
 *           @OA\Property(property="end_date", type="string|null", example="2024-10-30"),
 *           @OA\Property(property="numberDrivers", type="object", ref="#components/schemas/number_driver"),
 *           @OA\Property(property="drivers", type="array", @OA\Items(type="object", ref="#components/schemas/osago_driver")),
 *           @OA\Property(property="partner", type="object", ref="#components/schemas/partner_with_accident"),
 *           @OA\Property(property="is_juridic", type="integer", example="0"),
 *           @OA\Property(property="accident_policy_pdf_url", type="string|null", example="https://polis.e-osgo.uz/site/export/ghj"),
 *           @OA\Property(property="accident_policy_number", type="string|null", example="EE 124587"),
 *           @OA\Property(property="accident_amount", type="integer|null", example="20000"),
 *           @OA\Property(property="insurer_name", type="string|null", example="Aliyev Vali"),
 *           @OA\Property(property="insurer_passport_series", type="string|null", example="AA"),
 *           @OA\Property(property="insurer_passport_number", type="string|null", example="1245877"),
 *           @OA\Property(property="insurer_license_series", type="string|null", example="AV"),
 *           @OA\Property(property="insurer_license_number", type="string|null", example="12478511"),
 *           @OA\Property(property="insurer_license_given_date", type="string|null", example="2022-01-06"),
 *           @OA\Property(property="insurer_tech_pass_series", type="string", example="AAF"),
 *           @OA\Property(property="insurer_tech_pass_number", type="string", example="1234564"),
 *           @OA\Property(property="insurer_birthday", type="string|null", example="1993-02-30"),
 *           @OA\Property(property="insurer_inn", type="string|null", example="12111111111111111"),
 *           @OA\Property(property="promo", type="object", ref="#/components/schemas/promo"),
 *           @OA\Property(property="used_unique_code", type="object",
 *                       @OA\Property(property="id", type="integer", example=1),
 *                       @OA\Property(property="code", type="string|null", example="salom"),
 *                       @OA\Property(property="discount_percent", type="integer|null", example=-5),
 *                   ),
 *           @OA\Property(property="partner_ability", type="integer", example="1"),
 *           @OA\Property(property="insurer_pinfl", type="string", example="12341243124"),
 *      ),
 *
 *      @OA\Schema (
 *           schema="osago_driver",
 *           type="object",
 *
 *            @OA\Property(property="id", type="integer", example=4),
 *            @OA\Property (property="passport_series", type="string", example="KA"),
 *            @OA\Property (property="passport_number", type="string", example=0829728),
 *            @OA\Property (property="license_series", type="string|null", example=null),
 *            @OA\Property (property="license_number", type="string|null", example=null),
 *            @OA\Property (property="relationship", type="object|null", ref="#components/schemas/id_name"),
 *            @OA\Property (property="relationship_id", type="integer|null", example=7),
 *            @OA\Property (property="birthday", type="string", example="23.07.1999"),
 *            @OA\Property (property="with_accident", type="boolean", example="true"),
 *            @OA\Property (property="pinfl", type="string", example="12341241234"),
 *      ),
 *
 *     @OA\Schema (
 *          schema="automodel_with_parent",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=41),
 *          @OA\Property(property="name", type="string", example="Elantra"),
 *          @OA\Property(property="autobrand", type="object", ref="#/components/schemas/id_name"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="autocomp_with_parent",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=24),
 *          @OA\Property(property="name", type="string", example="Style 2.0 MPI 6AT 150 л.с."),
 *          @OA\Property(property="automodel", type="object", ref="#/components/schemas/automodel_with_parent"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="partner",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=24),
 *          @OA\Property(property="name", type="string", example="APEX Insurance"),
 *          @OA\Property(property="image", type="string", example="partner1601399768.png"),
 *     ),
 *
 *     @OA\Schema (
 *           schema="partner_with_accident",
 *           type="object",
 *
 *           @OA\Property(property="id", type="integer", example=24),
 *           @OA\Property(property="name", type="string", example="APEX Insurance"),
 *           @OA\Property(property="image", type="string", example="partner1601399768.png"),
 *           @OA\Property(property="accident", type="object",
 *                 @OA\Property(property="id", type="integer", example="1"),
 *                 @OA\Property(property="name", type="string|null", example="Техпаспорт застрахован"),
 *                 @OA\Property(property="description", type="string|null", example="Страхование техпаспорта включено в услуги компании Kapital Insurance"),
 *                 @OA\Property(property="required", type="integer|null", example="1 => required, 0 => optional"),
 *           ),
 *      ),
 *
 *     @OA\Schema (
 *          schema="kasko_risk_for_calc",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=2),
 *          @OA\Property(property="name", type="string", example="YTH, Transport vositasining olib qochilishi va to‘liq yo‘q bo‘lishi"),
 *          @OA\Property(property="amount", type="integer|null", example=null),
 *          @OA\Property(property="category_id", type="integer|null", example=null),
 *          @OA\Property(property="description", type="string|null", example="some desc"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="kasko_tariff_short",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=7),
 *          @OA\Property(property="name", type="string", example="APEX KASKO 3"),
 *          @OA\Property(property="file", type="string|null", example="/uploads/kasko-tariff/7-APEX KASKO 3/APEX KASKO 3_ПРАВИЛА-bBtO6.pdf"),
 *          @OA\Property(property="franchise", type="string", example="1 000 000 сум – это часть ущерба, НЕ выплачиваемая страховой ко..."),
 *          @OA\Property(property="only_first_risk", type="string", example=""),
 *          @OA\Property(property="is_conditional", type="integer", example=1),
 *          @OA\Property(property="is_islomic", type="integer", example=0),
 *          @OA\Property(property="partner", type="object", ref="#/components/schemas/partner"),
 *          @OA\Property(property="kaskoRisks", type="array", @OA\Items()),
 *     ),
 *
 *     @OA\Schema (
 *          schema="kasko_tariff_with_partner_name",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=7),
 *          @OA\Property(property="name", type="string", example="APEX KASKO 3"),
 *          @OA\Property(property="partner", type="object", ref="#/components/schemas/partner"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="kasko",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=651),
 *          @OA\Property(property="uuid", type="string", example="7c06194e-3da7-430a-b0ad-eed280c4bc15"),
 *          @OA\Property(property="year", type="integer", example=2018),
 *          @OA\Property(property="price", type="float", example=30000000),
 *          @OA\Property(property="autonumber", type="string|null", example=null),
 *          @OA\Property(property="amount_uzs", type="float", example=240000),
 *          @OA\Property(property="begin_date", type="date|null", example=null),
 *          @OA\Property(property="end_date", type="date|null", example=null),
 *          @OA\Property(property="status", type="integer", example=1),
 *          @OA\Property(property="insurer_name", type="string|null", example=null),
 *          @OA\Property(property="insurer_address", type="string|null", example=null),
 *          @OA\Property(property="insurer_tech_pass_series", type="string|null", example=null),
 *          @OA\Property(property="insurer_tech_pass_number", type="string|null", example=null),
 *          @OA\Property(property="insurer_passport_number", type="string|null", example=null),
 *          @OA\Property(property="insurer_passport_series", type="string|null", example=null),
 *          @OA\Property(property="insurer_phone", type="string|null", example=null),
 *          @OA\Property(property="insurer_pinfl", type="string|null", example=null),
 *          @OA\Property(property="promo_amount", type="float|null", example=null),
 *          @OA\Property(property="promo_id", type="integer|null", example=null),
 *          @OA\Property(property="autocomp", type="object", ref="#/components/schemas/autocomp_with_parent"),
 *          @OA\Property(property="tariff", type="object", ref="#/components/schemas/kasko_tariff_with_partner_name"),
 *     ),
 *
 *      @OA\Schema (
 *          schema="zoodpay_configuration",
 *          type="object",
 *
 *          @OA\Property(property="min_limit", type="integer", example=50, description="zakazning summasi shundan katta bo'lsa zoodpay chiqishi kerak"),
 *          @OA\Property(property="max_limit", type="integer", example=146880890, description="zakazning summasi shundan kichik bo'lsa zoodpay chiqishi kerak"),
 *          @OA\Property(property="service_name", type="string", example="Instalment"),
 *          @OA\Property(property="description", type="string|null", example="<b>Условия обслуживания</b><br>\n• Услуга ZoodPay дает возможность оплатить покупку, "),
 *          @OA\Property(property="service_code", type="string", example="ZPI"),
 *          @OA\Property(property="instalments", type="integer", example=4, description="pulni nechiga bo'lib to'lashi"),
 *
 *     ),
 *
 *     @OA\Schema (
 *          schema="user_with_token",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=31),
 *          @OA\Property(property="phone", type="integer", example=998946464400),
 *          @OA\Property(property="first_name", type="string|null", example="Vali"),
 *          @OA\Property(property="last_name", type="string|null", example="Valiyev"),
 *          @OA\Property(property="email", type="string", example="jobiryusupov0@gmail.com"),
 *          @OA\Property(property="access_token", type="string", example="To3HPzvatU0A-RZxJzoddgAGB671pS0w"),
 *     ),
 *
 *     @OA\Schema (
 *           schema="full_user",
 *           type="object",
 *
 *           @OA\Property(property="id", type="integer", example=31),
 *           @OA\Property(property="phone", type="integer", example=998946464400),
 *           @OA\Property(property="first_name", type="string|null", example="Vali"),
 *           @OA\Property(property="last_name", type="string|null", example="Valiyev"),
 *           @OA\Property(property="email", type="string", example="jobiryusupov0@gmail.com"),
 *           @OA\Property (property="gender", type="integer", example="0 => female, 1 => male"),
 *           @OA\Property (property="birthday", type="string", example="25.11.1992"),
 *           @OA\Property (property="passport_seria", type="string", example="AA"),
 *           @OA\Property (property="passport_number", type="string", example="1234567"),
 *           @OA\Property (property="city", type="object", ref="#/components/schemas/id_name"),
 *      ),
 *
 *     @OA\Schema (
 *          schema="hamkorpay_transaction",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=31),
 *          @OA\Property(property="card_number", type="string", example="8600120412345678"),
 *          @OA\Property(property="card_expiry", type="string", example="0123"),
 *          @OA\Property(property="mobile", type="string", example="99894**5859"),
 *          @OA\Property(property="amount", type="integer", example=150000),
 *          @OA\Property(property="commission", type="integer", example=0),
 *     ),
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
 *           schema="error_400_with_driver_key",
 *           type="object",
 *
 *           @OA\Property(property="error", type="object",
 *               @OA\Property(property="message", type="string", example="some error message"),
 *               @OA\Property(property="code", type="integer", example=0),
 *               @OA\Property(property="additional", type="object",
 *                   @OA\Property(property="driver_key", type="integer", example=6),
 *               ),
 *           ),
 *      ),
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
 *          schema="number_driver",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=3),
 *          @OA\Property(property="name", type="string", example="example name"),
 *          @OA\Property(property="description", type="string|null", example="example description"),
 *     ),
 *
 *     @OA\Schema (
 *          schema="accident_in_profile",
 *          type="object",
 *
 *           @OA\Property(property="id", type="integer", example=3),
 *           @OA\Property(property="policy_pdf_url", type="string", example="https://gross.uz/ru/asdfasf"),
 *           @OA\Property(property="policy_number", type="string", example="NL 1245256"),
 *           @OA\Property(property="payed_date", type="string", example="29.12.2023 17:40"),
 *     ),
 *
 *      @OA\Schema (
 *          schema="kasko_by_subscription",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="uuid", type="string", example="hgfghj7656789-98767jhgfghj-876rfghjh"),
 *          @OA\Property(property="program_id", type="integer", example=1),
 *          @OA\Property(property="program_name", type="string", example="AVTO LIMIT 1"),
 *          @OA\Property(property="amount_uzs", type="integer", example=60000),
 *          @OA\Property(property="autonumber", type="string|null", example="01Y195DA"),
 *          @OA\Property(property="status", type="integer", example=2),
 *          @OA\Property(property="last_kasko_by_subscription_policy", type="object",
 *              @OA\Property(property="remaining_days", type="integer|null", example="21"),
 *          ),
 *     ),
 *
 *     @OA\Schema (
 *          schema="full_kasko_by_subscription",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="uuid", type="string", example="fghjk12345-jhgf65-3456h5j4k"),
 *          @OA\Property(property="program_id", type="integer", example=1),
 *          @OA\Property(property="applicant_name", type="string|null", example="Jobir Yusupov"),
 *          @OA\Property(property="applicant_pass_series", type="string|null", example="AA"),
 *          @OA\Property(property="applicant_pass_number", type="string|null", example="234526"),
 *          @OA\Property(property="applicant_birthday", type="string|null", example="1998-10-25"),
 *          @OA\Property(property="amount_uzs", type="integer", example=60000),
 *          @OA\Property(property="amount_avto", type="integer", example=10000000),
 *          @OA\Property(property="autonumber", type="string|null", example="01Y195DA"),
 *          @OA\Property(property="tech_pass_series", type="string|null", example="AAF"),
 *          @OA\Property(property="tech_pass_number", type="string|null", example="0390422"),
 *          @OA\Property(property="status", type="integer", example=2),
 *          @OA\Property(property="promo", type="object", ref="#/components/schemas/promo"),
 *          @OA\Property(property="saved_card", type="object", ref="#components/schemas/saved_card"),
 *          @OA\Property(property="last_kasko_by_subscription_policy", type="object",
 *              @OA\Property(property="payed_date", type="string", example="25.01.2023"),
 *              @OA\Property(property="end_date", type="string", example="24.02.2023"),
 *              @OA\Property(property="remaining_days", type="integer", example=21),
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
 *          schema="travel",
 *          type="object",
 *
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="uuid", type="string", example="234sdfg-324dfgv-sdfg2345"),
 *          @OA\Property(property="countries", type="array", @OA\Items(type="object",
 *              @OA\Property(property="code", type="string", example="BE"),
 *          )),
 *          @OA\Property(property="purpose_id", type="integer", example="4"),
 *          @OA\Property(property="program_id", type="integer", example="4"),
 *          @OA\Property(property="partner", type="object",
 *              @OA\Property(property="id", type="integer", example=1),
 *              @OA\Property(property="name", type="string", example="Gross Insurance"),
 *              @OA\Property(property="image", type="string", example="https://asdfew"),
 *          ),
 *          @OA\Property(property="begin_date", type="string", example="12.09.2022"),
 *          @OA\Property(property="end_date", type="string", example="26.09.2022"),
 *          @OA\Property(property="is_family", type="integer", example=0),
 *          @OA\Property(property="has_covid", type="integer", example=0),
 *          @OA\Property(property="amount_uzs", type="integer", example=1762000),
 *          @OA\Property(property="status", type="integer", example=1),
 *          @OA\Property(property="travel_members", type="array", @OA\Items(type="object",
 *              @OA\Property(property="id", type="integer", example=1),
 *              @OA\Property(property="name", type="string|null", example="Ali"),
 *              @OA\Property(property="passport_series", type="string|null", example="AA"),
 *              @OA\Property(property="passport_number", type="string|null", example="7923838"),
 *              @OA\Property(property="birthday", type="string", example="22.12.2021"),
 *              @OA\Property(property="age", type="integer", example=1),
 *          )),
 *          @OA\Property(property="promo", type="object", ref="#/components/schemas/promo"),
 *          @OA\Property(property="policy_pdf_url", type="string|null", example="https://gross.uz/bla-bla/qwerty"),
 *          @OA\Property(property="policy_number", type="string|null", example="12qertywer"),
 *          @OA\Property(property="payed_date", type="string|null", example="12.11.2023 15:30:12"),
 *     ),
 *
 *     @OA\Schema (
 *           schema="short_travel",
 *           type="object",
 *
 *           @OA\Property(property="id", type="integer", example=1),
 *           @OA\Property(property="uuid", type="string", example="234sdfg-324dfgv-sdfg2345"),
 *           @OA\Property(property="partner", type="object",
 *               @OA\Property(property="id", type="integer", example=1),
 *               @OA\Property(property="name", type="string", example="Gross Insurance"),
 *               @OA\Property(property="image", type="string", example="https://asdfew"),
 *           ),
 *           @OA\Property(property="begin_date", type="string", example="12.09.2022"),
 *           @OA\Property(property="end_date", type="string", example="26.09.2022"),
 *           @OA\Property(property="amount_uzs", type="integer", example=1762000),
 *           @OA\Property(property="status", type="integer", example=1),
 *           @OA\Property(property="policy_pdf_url", type="string|null", example="https://gross.uz/bla-bla/qwerty"),
 *           @OA\Property(property="policy_number", type="string|null", example="12qertywer"),
 *           @OA\Property(property="payed_date", type="string|null", example="12.11.2023 15:30:12"),
 *      ),
 *
 *      @OA\Schema (
 *            schema="promo",
 *            type="object",
 *
 *            @OA\Property(property="id", type="integer", example=1),
 *               @OA\Property(property="promo_code", type="string|null", example="salom"),
 *               @OA\Property(property="promo_percent", type="integer|null", example=-20),
 *               @OA\Property(property="promo_amount", type="integer|null", example=-30000),
 *     ),
 *
 *     @OA\Schema (
 *           schema="client_auto",
 *           type="object",
 *
 *           @OA\Property(property="id", type="integer", example=3),
 *           @OA\Property(property="manufacture_year", type="integer", example="2019"),
 *           @OA\Property(property="autonumber", type="string", example="80U950JA"),
 *           @OA\Property(property="tex_pass_series", type="string", example="AAF"),
 *           @OA\Property(property="tex_pass_number", type="string", example="1234567"),
 *           @OA\Property(property="autocomp", type="object", ref="#/components/schemas/autocomp_with_parent"),
 *           @OA\Property(property="created_at", type="string", example="12.05.2023 14:26:10"),
 *      ),
 *
 *      @OA\Schema (
 *            schema="news",
 *            type="object",
 *
 *            @OA\Property(property="id", type="integer", example=1),
 *            @OA\Property(property="title", type="string|null", example="text"),
 *            @OA\Property(property="image", type="string|null", example="url"),
 *            @OA\Property(property="short_info", type="string|null", example="text"),
 *            @OA\Property(property="body", type="string|null", example="text"),
 *            @OA\Property(property="updated_at", type="integer", example="30.05.2023 14:19:01"),
 *       ),
 *
 *     @OA\Schema (
 *             schema="qas",
 *             type="object",
 *
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="question", type="string", example="Senectus et netus et malesuada. Amet est placerat in egestas erat?"),
 *             @OA\Property(property="answer", type="string", example="Ac felis donec et odio pellentesque diam volutpat commodo sed."),
 *             @OA\Property(property="page", type="integer", example=1),
 *             @OA\Property(property="status", type="integer", example=1),
 *        ),
 * ),
 *
 * @OA\Response(response="error_400", description="If request do not meet the requirements",
 *      @OA\JsonContent(ref="#/components/schemas/error_400")
 * ),
 *
 * @OA\Response(response="error_400_with_driver_key", description="If request do not meet the requirements",
 *       @OA\JsonContent(ref="#/components/schemas/error_400_with_driver_key")
 *  ),
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
                $user = \backapi\models\User::find()
                    ->where(['username' => $username])
                    ->andWhere(['status' => \backapi\models\User::STATUS_ACTIVE])
                    ->one();

                if ($user && $user->validatePassword($password) && in_array('bridge_company', array_keys(Yii::$app->authManager->getRolesByUser($user->id)))) {
                    return $user;
                }
                return null;
            },
        ];
        $behaviors['basicAuth']['only'] = [''];

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
