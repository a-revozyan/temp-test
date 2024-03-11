<?php

namespace console\controllers\seeder;

class AuthItemSeederController extends BaseSeederController
{
    public function actionRun()
    {
        $data = [
            [
                "name" => "partner",
                "type" => 1,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1603452267,
                "updated_at" => 1603452267
            ],
            [
                "name" => "Promo",
                "type" => 1,
                "description" => "Promo",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1618383771,
                "updated_at" => 1618383771
            ],
            [
                "name" => "surveyer",
                "type" => 1,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1651035415,
                "updated_at" => 1651035415
            ],
            [
                "name" => "bridge_company",
                "type" => 1,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1653511373,
                "updated_at" => 1653511373
            ],
            [
                "name" => "admin",
                "type" => 1,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1599391168,
                "updated_at" => 1676898509
            ],
            [
                "name" => "callcenter",
                "type" => 1,
                "description" => "call center",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1679639183,
                "updated_at" => 1682577648
            ],
            [
                "name" => "statistic",
                "type" => 1,
                "description" => "statistic",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1684220053,
                "updated_at" => 1684220053
            ],

            [
                "name" => "kasko",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1653924000,
                "updated_at" => 1653924089
            ],
            [
                "name" => "get_osago_policy_from_gross",
                "type" => 2,
                "description" => "получить полис осаго от гросс",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1674796979,
                "updated_at" => 1674796979
            ],
            [
                "name" => "/gii/default/diff",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676869953,
                "updated_at" => 1676869953
            ],
            [
                "name" => "/gii/default/action",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676869953,
                "updated_at" => 1676869953
            ],
            [
                "name" => "/gii/default/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676869953,
                "updated_at" => 1676869953
            ],
            [
                "name" => "/gii/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676869953,
                "updated_at" => 1676869953
            ],
            [
                "name" => "/gii/default/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676869953,
                "updated_at" => 1676869953
            ],
            [
                "name" => "/gii/default/preview",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676869953,
                "updated_at" => 1676869953
            ],
            [
                "name" => "/gii/default/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676869953,
                "updated_at" => 1676869953
            ],
            [
                "name" => "/agent/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870001,
                "updated_at" => 1676870001
            ],
            [
                "name" => "/agent/set-agent-product-coeff",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870001,
                "updated_at" => 1676870001
            ],
            [
                "name" => "/agent/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870001,
                "updated_at" => 1676870001
            ],
            [
                "name" => "/agent/delete-file",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870001,
                "updated_at" => 1676870001
            ],
            [
                "name" => "/agent/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870001,
                "updated_at" => 1676870001
            ],
            [
                "name" => "/agent/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870001,
                "updated_at" => 1676870001
            ],
            [
                "name" => "/agent/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870001,
                "updated_at" => 1676870001
            ],
            [
                "name" => "/agent/statistics",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870001,
                "updated_at" => 1676870001
            ],
            [
                "name" => "/agent/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870001,
                "updated_at" => 1676870001
            ],
            [
                "name" => "/assignment/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870023,
                "updated_at" => 1676870023
            ],
            [
                "name" => "/assignment/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870023,
                "updated_at" => 1676870023
            ],
            [
                "name" => "/assignment/remove-role-to-user",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870023,
                "updated_at" => 1676870023
            ],
            [
                "name" => "/assignment/assign-role-to-user",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870023,
                "updated_at" => 1676870023
            ],
            [
                "name" => "/assignment/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870023,
                "updated_at" => 1676870023
            ],
            [
                "name" => "/auto-brand/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870093,
                "updated_at" => 1676870093
            ],
            [
                "name" => "/auto-brand/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870093,
                "updated_at" => 1676870093
            ],
            [
                "name" => "/auto-brand/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870093,
                "updated_at" => 1676870093
            ],
            [
                "name" => "/auto-brand/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870093,
                "updated_at" => 1676870093
            ],
            [
                "name" => "/auto-brand/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870093,
                "updated_at" => 1676870093
            ],
            [
                "name" => "/auto-brand/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870093,
                "updated_at" => 1676870093
            ],
            [
                "name" => "/auto-brand/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870093,
                "updated_at" => 1676870093
            ],
            [
                "name" => "/auto/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870105,
                "updated_at" => 1676870105
            ],
            [
                "name" => "/auto/auto-export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870105,
                "updated_at" => 1676870105
            ],
            [
                "name" => "/auto/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870105,
                "updated_at" => 1676870105
            ],
            [
                "name" => "/auto/attach-partner",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870105,
                "updated_at" => 1676870105
            ],
            [
                "name" => "/auto/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870105,
                "updated_at" => 1676870105
            ],
            [
                "name" => "/auto/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870105,
                "updated_at" => 1676870105
            ],
            [
                "name" => "/auto/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870105,
                "updated_at" => 1676870105
            ],
            [
                "name" => "/auto/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870105,
                "updated_at" => 1676870105
            ],
            [
                "name" => "/auto-model/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870115,
                "updated_at" => 1676870115
            ],
            [
                "name" => "/auto-model/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870115,
                "updated_at" => 1676870115
            ],
            [
                "name" => "/auto-model/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870115,
                "updated_at" => 1676870115
            ],
            [
                "name" => "/auto-model/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870115,
                "updated_at" => 1676870115
            ],
            [
                "name" => "/auto-model/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870115,
                "updated_at" => 1676870115
            ],
            [
                "name" => "/auto-model/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870115,
                "updated_at" => 1676870115
            ],
            [
                "name" => "/auto-model/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870115,
                "updated_at" => 1676870115
            ],
            [
                "name" => "/auto-risk-type/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870169,
                "updated_at" => 1676870169
            ],
            [
                "name" => "/auto-risk-type/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870169,
                "updated_at" => 1676870169
            ],
            [
                "name" => "/auto-risk-type/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870169,
                "updated_at" => 1676870169
            ],
            [
                "name" => "/auto-risk-type/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870169,
                "updated_at" => 1676870169
            ],
            [
                "name" => "/auto-risk-type/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870169,
                "updated_at" => 1676870169
            ],
            [
                "name" => "/auto-risk-type/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870169,
                "updated_at" => 1676870169
            ],
            [
                "name" => "/auto-risk-type/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870169,
                "updated_at" => 1676870169
            ],
            [
                "name" => "/bridge-company/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870177,
                "updated_at" => 1676870177
            ],
            [
                "name" => "/bridge-company/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870177,
                "updated_at" => 1676870177
            ],
            [
                "name" => "/bridge-company/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870177,
                "updated_at" => 1676870177
            ],
            [
                "name" => "/bridge-company/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870177,
                "updated_at" => 1676870177
            ],
            [
                "name" => "/bridge-company/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870177,
                "updated_at" => 1676870177
            ],
            [
                "name" => "/bridge-company/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870177,
                "updated_at" => 1676870177
            ],
            [
                "name" => "/bridge-company/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870177,
                "updated_at" => 1676870177
            ],
            [
                "name" => "/car-accessory/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870188,
                "updated_at" => 1676870188
            ],
            [
                "name" => "/car-accessory/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870188,
                "updated_at" => 1676870188
            ],
            [
                "name" => "/car-accessory/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870188,
                "updated_at" => 1676870188
            ],
            [
                "name" => "/car-accessory/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870188,
                "updated_at" => 1676870188
            ],
            [
                "name" => "/car-accessory/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870188,
                "updated_at" => 1676870188
            ],
            [
                "name" => "/car-accessory/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870188,
                "updated_at" => 1676870188
            ],
            [
                "name" => "/car-accessory/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870188,
                "updated_at" => 1676870188
            ],
            [
                "name" => "/home/generate-doc",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870226,
                "updated_at" => 1676870226
            ],
            [
                "name" => "/f-user/send-sms-or-telegram-message",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870226,
                "updated_at" => 1676870226
            ],
            [
                "name" => "/f-user/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870226,
                "updated_at" => 1676870226
            ],
            [
                "name" => "/f-user/statistics",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870226,
                "updated_at" => 1676870226
            ],
            [
                "name" => "/home/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870226,
                "updated_at" => 1676870226
            ],
            [
                "name" => "/home/sales-graph",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870226,
                "updated_at" => 1676870226
            ],
            [
                "name" => "/home/statistics",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870226,
                "updated_at" => 1676870226
            ],
            [
                "name" => "/f-user/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870226,
                "updated_at" => 1676870226
            ],
            [
                "name" => "/f-user/get-products",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870226,
                "updated_at" => 1676870226
            ],
            [
                "name" => "/f-user/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870226,
                "updated_at" => 1676870226
            ],
            [
                "name" => "/kasko/delete-file",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870277,
                "updated_at" => 1676870277
            ],
            [
                "name" => "/kasko/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870277,
                "updated_at" => 1676870277
            ],
            [
                "name" => "/kasko/change-status",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870277,
                "updated_at" => 1676870277
            ],
            [
                "name" => "/kasko/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870277,
                "updated_at" => 1676870277
            ],
            [
                "name" => "/kasko-risk/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870287,
                "updated_at" => 1676870287
            ],
            [
                "name" => "/kasko-risk-category/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870287,
                "updated_at" => 1676870287
            ],
            [
                "name" => "/kasko-risk-category/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870287,
                "updated_at" => 1676870287
            ],
            [
                "name" => "/kasko-risk/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870287,
                "updated_at" => 1676870287
            ],
            [
                "name" => "/kasko-risk/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870287,
                "updated_at" => 1676870287
            ],
            [
                "name" => "/kasko-risk/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870287,
                "updated_at" => 1676870287
            ],
            [
                "name" => "/kasko-risk/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870287,
                "updated_at" => 1676870287
            ],
            [
                "name" => "/kasko-risk/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870287,
                "updated_at" => 1676870287
            ],
            [
                "name" => "/kasko-risk/kasko-risk-export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870287,
                "updated_at" => 1676870287
            ],
            [
                "name" => "/kasko-tariff/attach-risks",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870299,
                "updated_at" => 1676870299
            ],
            [
                "name" => "/kasko-tariff/kasko-tariff-export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870299,
                "updated_at" => 1676870299
            ],
            [
                "name" => "/kasko-tariff/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870299,
                "updated_at" => 1676870299
            ],
            [
                "name" => "/kasko-tariff/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870299,
                "updated_at" => 1676870299
            ],
            [
                "name" => "/kasko-tariff/attach-auto-risk-types",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870299,
                "updated_at" => 1676870299
            ],
            [
                "name" => "/kasko-tariff/attach-car-accessories",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870299,
                "updated_at" => 1676870299
            ],
            [
                "name" => "/kasko-tariff/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870299,
                "updated_at" => 1676870299
            ],
            [
                "name" => "/kasko-tariff/attach-islomic-amount",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870299,
                "updated_at" => 1676870299
            ],
            [
                "name" => "/kasko-tariff/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870299,
                "updated_at" => 1676870299
            ],
            [
                "name" => "/kasko-tariff/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870299,
                "updated_at" => 1676870299
            ],
            [
                "name" => "/kasko-tariff/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870299,
                "updated_at" => 1676870299
            ],
            [
                "name" => "/partner/kasko-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/permission/remove",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/permission/assign",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/permission/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/permission/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/permission/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/permission/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/permission/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/period/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/period/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/period/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/kasko-by-subscription-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/kasko-by-subscription-export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/old-osago/import-file",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/old-osago/import-and-sync-with-gross",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/old-osago/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/opinion/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/opinion/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/opinion/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/opinion/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/opinion/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/osago/change-status",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/osago/send-request-to-get-policy",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/osago/send-request-to-get-policy-status",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/osago/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/osago/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/top-products",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/products",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/kaskos",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/kaskos-export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/osago",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/osago-export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/partner/kasko-by-subscription",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/region/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/region/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/region/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/permission/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870387,
                "updated_at" => 1676870387
            ],
            [
                "name" => "/role/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/role/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/relationship/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/relationship/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/role/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/role/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/role/assign",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/role/remove",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/role/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/route/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/route/assign",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/route/remove",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/route/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/sale/statistics",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/sale/products",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/sale/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/sale/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/relationship/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/role/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870443,
                "updated_at" => 1676870443
            ],
            [
                "name" => "/surveyer/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/surveyer/kaskos",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/surveyer/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/surveyer/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/surveyer/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/surveyer/set-service-amount",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/surveyer/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/surveyer/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/surveyer/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/surveyer/statistics",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/sms-template/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/sms-template/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/sms-template/pause",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/sms-template/run",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/sms-template/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/sms-template/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/sms-template/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/sms-template/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870659,
                "updated_at" => 1676870659
            ],
            [
                "name" => "/user/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870718,
                "updated_at" => 1676870718
            ],
            [
                "name" => "/user/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870718,
                "updated_at" => 1676870718
            ],
            [
                "name" => "/user/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870718,
                "updated_at" => 1676870718
            ],
            [
                "name" => "/user/update-profile",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870718,
                "updated_at" => 1676870718
            ],
            [
                "name" => "/user/update-password",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870718,
                "updated_at" => 1676870718
            ],
            [
                "name" => "/user/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870718,
                "updated_at" => 1676870718
            ],
            [
                "name" => "/zood-pay/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870876,
                "updated_at" => 1676870876
            ],
            [
                "name" => "/zood-pay/transaction-delivery",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676870876,
                "updated_at" => 1676870876
            ],
            [
                "name" => "/admin/assignment/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898475,
                "updated_at" => 1676898475
            ],
            [
                "name" => "/debug/user/set-identity",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/debug/default/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/debug/default/download-mail",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/debug/default/toolbar",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/debug/default/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/debug/default/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/debug/default/db-explain",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/user/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program-period/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/debug/user/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/debug/user/reset-identity",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program-period/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-purpose/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-purpose/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-purpose/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-purpose/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-purpose/partner-coeffs",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-purpose/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-purpose/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk-category/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk-category/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk-category/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk-category/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk-category/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk-category/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk/set-amounts",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-risk/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/url-counter/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/url-counter/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/url-counter/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/url-counter/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/url-counter/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/url-counter/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/user/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/user/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/user/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/warehouse/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/warehouse/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/warehouse/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/warehouse/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/warehouse/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/warehouse/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/assignment/assign",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/assignment/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/accident-partner-program/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/accident-partner-program/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/accident-partner-program/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/accident-partner-program/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/accident-partner-program/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/user/activate",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/accident-partner-program/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/auto-risk-type/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/auto-risk-type/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autobrand/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autobrand/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autobrand/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autobrand/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autobrand/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autobrand/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autocomp/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autocomp/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autocomp/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autocomp/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autocomp/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autocomp/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/automodel/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/automodel/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/automodel/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/automodel/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/automodel/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/automodel/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autotype/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autotype/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autotype/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autotype/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autotype/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/autotype/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/bridge-company/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/bridge-company/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/citizenship/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/citizenship/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/citizenship/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/citizenship/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/citizenship/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/citizenship/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/country/upload",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/country/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/country/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/country/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/country/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/country/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/country/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/currency/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/currency/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/currency/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/currency/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/currency/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/currency/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-risk-category/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-risk-category/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-risk-category/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-risk-category/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-risk-category/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-risk/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-risk/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-tariff/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-tariff/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-tariff-risk/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/user/change-password",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/user/reset-password",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/user/request-password-reset",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/user/signup",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/user/logout",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/user/login",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/user/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/user/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/user/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/rule/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/rule/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/rule/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/rule/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/rule/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/rule/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/route/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/route/refresh",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/route/remove",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/route/assign",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/route/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/route/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/role/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/role/remove",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/role/get-users",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/role/assign",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/role/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/role/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/role/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/role/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/role/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/permission/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/permission/remove",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/permission/get-users",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/permission/assign",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/permission/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/permission/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/permission/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/permission/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/permission/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/menu/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/menu/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/menu/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/menu/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/menu/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/menu/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/default/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/default/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-tariff-risk/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/assignment/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-tariff-risk/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-tariff-risk/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-tariff-risk/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/kasko-tariff-risk/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/news/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/news/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/news/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/news/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/news/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/admin/assignment/revoke",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/news/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/number-drivers/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/number-drivers/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/number-drivers/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/number-drivers/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/number-drivers/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/number-drivers/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/osago-amount/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/osago-amount/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/osago-amount/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/osago-amount/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/osago-partner-rating/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/osago-partner-rating/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/osago-partner-rating/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/osago-partner-rating/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/osago-partner-rating/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/osago-partner-rating/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/page/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/page/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/page/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/page/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/page/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/page/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/partner/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/partner/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/partner/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/partner-product/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/partner-product/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/partner-product/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/partner-product/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/partner-product/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/partner-product/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/period/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/period/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/period/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/period/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/period/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/product/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/product/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/product/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/product/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/product/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/product/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/promo/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/promo/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/promo/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/promo/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/promo/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/promo/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/qa/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/qa/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/qa/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/qa/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/qa/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/qa/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/region/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/region/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/region/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/region/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/region/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/relationship/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/relationship/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/relationship/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/relationship/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/relationship/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/error",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/login",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/logout",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/translates",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/translate-update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/osago",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/osago-delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/osago-view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/kasko",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/kasko-delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/kasko-view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/kasko-remove-from-surveyer",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/travel",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/travel-delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/travel-view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/accident",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/accident-delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/accident-view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/site/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/surveyer/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/surveyer/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-age-group/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-age-group/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-age-group/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-age-group/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-age-group/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-age-group/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-extra-insurance/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-extra-insurance/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-extra-insurance/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-extra-insurance/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-extra-insurance/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-extra-insurance/partner-coeffs",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-extra-insurance/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-family-koef/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-family-koef/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-family-koef/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-family-koef/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-family-koef/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-family-koef/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-group-type/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-group-type/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-group-type/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-group-type/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-group-type/partner-coeffs",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-group-type/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-group-type/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-multiple-period/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-multiple-period/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-multiple-period/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-multiple-period/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-multiple-period/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-multiple-period/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program/view",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program-period/index",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program-period/set-amounts",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program-period/set-travel-info",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/debug/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program-period/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program-period/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program-period/program-list",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/travel-program-period/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1676898476,
                "updated_at" => 1676898476
            ],
            [
                "name" => "/partner/osago-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1679639553,
                "updated_at" => 1679639553
            ],
            [
                "name" => "/partner/travel-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1680153769,
                "updated_at" => 1680153769
            ],
            [
                "name" => "/reason/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1680153851,
                "updated_at" => 1680153851
            ],
            [
                "name" => "/sales-for-call-center/statistics",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1680153851,
                "updated_at" => 1680153851
            ],
            [
                "name" => "/sales-for-call-center/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1680153851,
                "updated_at" => 1680153851
            ],
            [
                "name" => "/sales-for-call-center/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1680153851,
                "updated_at" => 1680153851
            ],
            [
                "name" => "/sales-for-call-center/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1680153851,
                "updated_at" => 1680153851
            ],
            [
                "name" => "/sales-for-call-center/products",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1680153851,
                "updated_at" => 1680153851
            ],
            [
                "name" => "/reason/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1680153851,
                "updated_at" => 1680153851
            ],
            [
                "name" => "/sales-for-call-center/call",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682587590,
                "updated_at" => 1682587590
            ],
            [
                "name" => "home-menu",
                "type" => 2,
                "description" => "Asosiy",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682588918,
                "updated_at" => 1683196889
            ],
            [
                "name" => "sales-menu",
                "type" => 2,
                "description" => "Sotuvlar",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682589130,
                "updated_at" => 1683196871
            ],
            [
                "name" => "marketing-menu",
                "type" => 2,
                "description" => "Marketing",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682589140,
                "updated_at" => 1683196853
            ],
            [
                "name" => "call-center",
                "type" => 2,
                "description" => "call-center",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682589152,
                "updated_at" => 1682589152
            ],
            [
                "name" => "products-menu",
                "type" => 2,
                "description" => "Mahsulotlar",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682589161,
                "updated_at" => 1683196833
            ],
            [
                "name" => "opinions-menu",
                "type" => 2,
                "description" => "Fikirlar",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682589174,
                "updated_at" => 1683196795
            ],
            [
                "name" => "messages-menu",
                "type" => 2,
                "description" => "xabarlar",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682589184,
                "updated_at" => 1683196776
            ],
            [
                "name" => "promo-menu",
                "type" => 2,
                "description" => "Kupon",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682589197,
                "updated_at" => 1683196733
            ],
            [
                "name" => "agents-menu",
                "type" => 2,
                "description" => "agentlar",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682589213,
                "updated_at" => 1683196694
            ],
            [
                "name" => "reports-menu",
                "type" => 2,
                "description" => "Hisobotlar",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682589221,
                "updated_at" => 1683196660
            ],
            [
                "name" => "surveyer-menu",
                "type" => 2,
                "description" => "anketor",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682589251,
                "updated_at" => 1683196609
            ],
            [
                "name" => "faq-menu",
                "type" => 2,
                "description" => "faq",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682589266,
                "updated_at" => 1683196589
            ],
            [
                "name" => "control-dostup-menu",
                "type" => 2,
                "description" => "control-dostup",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1682589327,
                "updated_at" => 1683196304
            ],
            [
                "name" => "/reason/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1683194971,
                "updated_at" => 1683194971
            ],
            [
                "name" => "/reason/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1683194971,
                "updated_at" => 1683194971
            ],
            [
                "name" => "/reason/create",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1683194971,
                "updated_at" => 1683194971
            ],
            [
                "name" => "/reason/delete",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1683194971,
                "updated_at" => 1683194971
            ],
            [
                "name" => "/reason/update",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1683194971,
                "updated_at" => 1683194971
            ],
            [
                "name" => "interface-menu",
                "type" => 2,
                "description" => "partner ning car inspection oynasi ",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1692078867,
                "updated_at" => 1692078867
            ],
            [
                "name" => "/car-inspection-for-partner/all",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1692171458,
                "updated_at" => 1692171458
            ],
            [
                "name" => "/car-inspection-for-partner/statistics",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1692171458,
                "updated_at" => 1692171458
            ],
            [
                "name" => "/car-inspection-for-partner/export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1692171458,
                "updated_at" => 1692171458
            ],
            [
                "name" => "/car-inspection-for-partner/get-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1692171458,
                "updated_at" => 1692171458
            ],
            [
                "name" => "/car-inspection-for-partner/create-car-inspection",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1692171458,
                "updated_at" => 1692171458
            ],
            [
                "name" => "/car-inspection-for-partner/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1692171458,
                "updated_at" => 1692171458
            ],
            [
                "name" => "/f-user/users-by-policy-end-date",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1696428817,
                "updated_at" => 1696428817
            ],
            [
                "name" => "/f-user/send-unique-link",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1696428817,
                "updated_at" => 1696428817
            ],
            [
                "name" => "/f-user/send-sms-by-policy-end-date",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1696428817,
                "updated_at" => 1696428817
            ],
            [
                "name" => "/f-user/product-counts-by-policy-end-date",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1696428817,
                "updated_at" => 1696428817
            ],
            [
                "name" => "/bridge-company-profile/osago",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1697619255,
                "updated_at" => 1697619255
            ],
            [
                "name" => "/bridge-company-profile/*",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1697619255,
                "updated_at" => 1697619255
            ],
            [
                "name" => "/bridge-company-profile/osago-export",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1697619255,
                "updated_at" => 1697619255
            ],
            [
                "name" => "/bridge-company-profile/osago-by-id",
                "type" => 2,
                "description" => null,
                "rule_name" => null,
                "data" => null,
                "created_at" => 1697619255,
                "updated_at" => 1697619255
            ],
            [
                "name" => "bridge-profile-menu",
                "type" => 2,
                "description" => "bridge-profile-menu",
                "rule_name" => null,
                "data" => null,
                "created_at" => 1697619638,
                "updated_at" => 1697619638
            ]
        ];

        $this->insertData('auth_item', ["name", "type", "description", "rule_name", "data", "created_at", "updated_at"], $data);
    }
}