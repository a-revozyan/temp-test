<?php

namespace console\controllers\seeder;

class AuthItemChildSeederController extends BaseSeederController
{
    public function actionRun()
    {
        $data = [
            [
                "parent" => "admin",
                "child" => "/home/*"
            ],
            [
                "parent" => "admin",
                "child" => "/home/statistics"
            ],
            [
                "parent" => "admin",
                "child" => "/role/*"
            ],
            [
                "parent" => "admin",
                "child" => "/role/all"
            ],
            [
                "parent" => "admin",
                "child" => "/role/assign"
            ],
            [
                "parent" => "admin",
                "child" => "/role/create"
            ],
            [
                "parent" => "admin",
                "child" => "/role/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/role/remove"
            ],
            [
                "parent" => "admin",
                "child" => "/role/update"
            ],
            [
                "parent" => "admin",
                "child" => "/route/*"
            ],
            [
                "parent" => "admin",
                "child" => "/route/all"
            ],
            [
                "parent" => "admin",
                "child" => "/route/assign"
            ],
            [
                "parent" => "admin",
                "child" => "/route/remove"
            ],
            [
                "parent" => "admin",
                "child" => "Promo"
            ],
            [
                "parent" => "admin",
                "child" => "bridge_company"
            ],
            [
                "parent" => "admin",
                "child" => "partner"
            ],
            [
                "parent" => "admin",
                "child" => "surveyer"
            ],
            [
                "parent" => "admin",
                "child" => "get_osago_policy_from_gross"
            ],
            [
                "parent" => "admin",
                "child" => "kasko"
            ],
            [
                "parent" => "admin",
                "child" => "/*"
            ],
            [
                "parent" => "admin",
                "child" => "/accident-partner-program/*"
            ],
            [
                "parent" => "admin",
                "child" => "/accident-partner-program/create"
            ],
            [
                "parent" => "admin",
                "child" => "/accident-partner-program/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/accident-partner-program/index"
            ],
            [
                "parent" => "admin",
                "child" => "/accident-partner-program/update"
            ],
            [
                "parent" => "admin",
                "child" => "/accident-partner-program/view"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/*"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/assignment/*"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/assignment/assign"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/assignment/index"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/assignment/revoke"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/assignment/view"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/default/*"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/default/index"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/menu/*"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/menu/create"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/menu/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/menu/index"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/menu/update"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/menu/view"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/permission/*"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/permission/assign"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/permission/create"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/permission/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/permission/get-users"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/permission/index"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/permission/remove"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/permission/update"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/permission/view"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/role/*"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/role/assign"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/role/create"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/role/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/role/get-users"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/role/index"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/role/remove"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/role/update"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/role/view"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/route/*"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/route/assign"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/route/create"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/route/index"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/route/refresh"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/route/remove"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/rule/*"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/rule/create"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/rule/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/rule/index"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/rule/update"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/rule/view"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/user/*"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/user/activate"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/user/change-password"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/user/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/user/index"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/user/login"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/user/logout"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/user/request-password-reset"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/user/reset-password"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/user/signup"
            ],
            [
                "parent" => "admin",
                "child" => "/admin/user/view"
            ],
            [
                "parent" => "admin",
                "child" => "/agent/*"
            ],
            [
                "parent" => "admin",
                "child" => "/agent/all"
            ],
            [
                "parent" => "admin",
                "child" => "/agent/create"
            ],
            [
                "parent" => "admin",
                "child" => "/agent/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/agent/delete-file"
            ],
            [
                "parent" => "admin",
                "child" => "/agent/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/agent/set-agent-product-coeff"
            ],
            [
                "parent" => "admin",
                "child" => "/agent/statistics"
            ],
            [
                "parent" => "admin",
                "child" => "/agent/update"
            ],
            [
                "parent" => "admin",
                "child" => "/assignment/*"
            ],
            [
                "parent" => "admin",
                "child" => "/assignment/all"
            ],
            [
                "parent" => "admin",
                "child" => "/assignment/assign-role-to-user"
            ],
            [
                "parent" => "admin",
                "child" => "/assignment/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/assignment/remove-role-to-user"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-brand/*"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-brand/all"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-brand/create"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-brand/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-brand/export"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-brand/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-brand/update"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-model/*"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-model/all"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-model/create"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-model/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-model/export"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-model/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-model/update"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-risk-type/*"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-risk-type/all"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-risk-type/create"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-risk-type/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-risk-type/export"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-risk-type/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-risk-type/index"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-risk-type/update"
            ],
            [
                "parent" => "admin",
                "child" => "/auto-risk-type/view"
            ],
            [
                "parent" => "admin",
                "child" => "/auto/*"
            ],
            [
                "parent" => "admin",
                "child" => "/auto/all"
            ],
            [
                "parent" => "admin",
                "child" => "/auto/attach-partner"
            ],
            [
                "parent" => "admin",
                "child" => "/auto/auto-export"
            ],
            [
                "parent" => "admin",
                "child" => "/auto/create"
            ],
            [
                "parent" => "admin",
                "child" => "/auto/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/auto/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/auto/update"
            ],
            [
                "parent" => "admin",
                "child" => "/autobrand/*"
            ],
            [
                "parent" => "admin",
                "child" => "/autobrand/create"
            ],
            [
                "parent" => "admin",
                "child" => "/autobrand/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/autobrand/index"
            ],
            [
                "parent" => "admin",
                "child" => "/autobrand/update"
            ],
            [
                "parent" => "admin",
                "child" => "/autobrand/view"
            ],
            [
                "parent" => "admin",
                "child" => "/autocomp/*"
            ],
            [
                "parent" => "admin",
                "child" => "/autocomp/create"
            ],
            [
                "parent" => "admin",
                "child" => "/autocomp/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/autocomp/index"
            ],
            [
                "parent" => "admin",
                "child" => "/autocomp/update"
            ],
            [
                "parent" => "admin",
                "child" => "/autocomp/view"
            ],
            [
                "parent" => "admin",
                "child" => "/automodel/*"
            ],
            [
                "parent" => "admin",
                "child" => "/automodel/create"
            ],
            [
                "parent" => "admin",
                "child" => "/automodel/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/automodel/index"
            ],
            [
                "parent" => "admin",
                "child" => "/automodel/update"
            ],
            [
                "parent" => "admin",
                "child" => "/automodel/view"
            ],
            [
                "parent" => "admin",
                "child" => "/autotype/*"
            ],
            [
                "parent" => "admin",
                "child" => "/autotype/create"
            ],
            [
                "parent" => "admin",
                "child" => "/autotype/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/autotype/index"
            ],
            [
                "parent" => "admin",
                "child" => "/autotype/update"
            ],
            [
                "parent" => "admin",
                "child" => "/autotype/view"
            ],
            [
                "parent" => "admin",
                "child" => "/bridge-company/*"
            ],
            [
                "parent" => "admin",
                "child" => "/bridge-company/all"
            ],
            [
                "parent" => "admin",
                "child" => "/bridge-company/create"
            ],
            [
                "parent" => "admin",
                "child" => "/bridge-company/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/bridge-company/export"
            ],
            [
                "parent" => "admin",
                "child" => "/bridge-company/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/bridge-company/index"
            ],
            [
                "parent" => "admin",
                "child" => "/bridge-company/update"
            ],
            [
                "parent" => "admin",
                "child" => "/bridge-company/view"
            ],
            [
                "parent" => "admin",
                "child" => "/car-accessory/*"
            ],
            [
                "parent" => "admin",
                "child" => "/car-accessory/all"
            ],
            [
                "parent" => "admin",
                "child" => "/car-accessory/create"
            ],
            [
                "parent" => "admin",
                "child" => "/car-accessory/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/car-accessory/export"
            ],
            [
                "parent" => "admin",
                "child" => "/car-accessory/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/car-accessory/update"
            ],
            [
                "parent" => "admin",
                "child" => "/citizenship/*"
            ],
            [
                "parent" => "admin",
                "child" => "/citizenship/create"
            ],
            [
                "parent" => "admin",
                "child" => "/citizenship/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/citizenship/index"
            ],
            [
                "parent" => "admin",
                "child" => "/citizenship/update"
            ],
            [
                "parent" => "admin",
                "child" => "/citizenship/view"
            ],
            [
                "parent" => "admin",
                "child" => "/country/*"
            ],
            [
                "parent" => "admin",
                "child" => "/country/create"
            ],
            [
                "parent" => "admin",
                "child" => "/country/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/country/index"
            ],
            [
                "parent" => "admin",
                "child" => "/country/update"
            ],
            [
                "parent" => "admin",
                "child" => "/country/upload"
            ],
            [
                "parent" => "admin",
                "child" => "/country/view"
            ],
            [
                "parent" => "admin",
                "child" => "/currency/*"
            ],
            [
                "parent" => "admin",
                "child" => "/currency/create"
            ],
            [
                "parent" => "admin",
                "child" => "/currency/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/currency/index"
            ],
            [
                "parent" => "admin",
                "child" => "/currency/update"
            ],
            [
                "parent" => "admin",
                "child" => "/currency/view"
            ],
            [
                "parent" => "admin",
                "child" => "/debug/*"
            ],
            [
                "parent" => "admin",
                "child" => "/debug/default/*"
            ],
            [
                "parent" => "admin",
                "child" => "/debug/default/db-explain"
            ],
            [
                "parent" => "admin",
                "child" => "/debug/default/download-mail"
            ],
            [
                "parent" => "admin",
                "child" => "/debug/default/index"
            ],
            [
                "parent" => "admin",
                "child" => "/debug/default/toolbar"
            ],
            [
                "parent" => "admin",
                "child" => "/debug/default/view"
            ],
            [
                "parent" => "admin",
                "child" => "/debug/user/*"
            ],
            [
                "parent" => "admin",
                "child" => "/debug/user/reset-identity"
            ],
            [
                "parent" => "admin",
                "child" => "/debug/user/set-identity"
            ],
            [
                "parent" => "admin",
                "child" => "/gii/*"
            ],
            [
                "parent" => "admin",
                "child" => "/gii/default/*"
            ],
            [
                "parent" => "admin",
                "child" => "/gii/default/action"
            ],
            [
                "parent" => "admin",
                "child" => "/gii/default/diff"
            ],
            [
                "parent" => "admin",
                "child" => "/gii/default/index"
            ],
            [
                "parent" => "admin",
                "child" => "/gii/default/preview"
            ],
            [
                "parent" => "admin",
                "child" => "/gii/default/view"
            ],
            [
                "parent" => "admin",
                "child" => "/home/generate-doc"
            ],
            [
                "parent" => "admin",
                "child" => "/home/sales-graph"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk-category/*"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk-category/all"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk-category/create"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk-category/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk-category/index"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk-category/update"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk-category/view"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk/*"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk/all"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk/create"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk/index"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk/kasko-risk-export"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk/update"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-risk/view"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff-risk/*"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff-risk/create"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff-risk/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff-risk/index"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff-risk/update"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff-risk/view"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/*"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/all"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/attach-auto-risk-types"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/attach-car-accessories"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/attach-islomic-amount"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/attach-risks"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/create"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/index"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/kasko-tariff-export"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/update"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko-tariff/view"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko/*"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko/change-status"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko/delete-file"
            ],
            [
                "parent" => "admin",
                "child" => "/kasko/update"
            ],
            [
                "parent" => "admin",
                "child" => "/news/*"
            ],
            [
                "parent" => "admin",
                "child" => "/news/create"
            ],
            [
                "parent" => "admin",
                "child" => "/news/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/news/index"
            ],
            [
                "parent" => "admin",
                "child" => "/news/update"
            ],
            [
                "parent" => "admin",
                "child" => "/news/view"
            ],
            [
                "parent" => "admin",
                "child" => "/number-drivers/*"
            ],
            [
                "parent" => "admin",
                "child" => "/number-drivers/create"
            ],
            [
                "parent" => "admin",
                "child" => "/number-drivers/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/number-drivers/index"
            ],
            [
                "parent" => "admin",
                "child" => "/number-drivers/update"
            ],
            [
                "parent" => "admin",
                "child" => "/number-drivers/view"
            ],
            [
                "parent" => "admin",
                "child" => "/old-osago/*"
            ],
            [
                "parent" => "admin",
                "child" => "/old-osago/import-and-sync-with-gross"
            ],
            [
                "parent" => "admin",
                "child" => "/old-osago/import-file"
            ],
            [
                "parent" => "admin",
                "child" => "/opinion/*"
            ],
            [
                "parent" => "admin",
                "child" => "/opinion/all"
            ],
            [
                "parent" => "admin",
                "child" => "/opinion/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/opinion/export"
            ],
            [
                "parent" => "admin",
                "child" => "/opinion/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/osago-amount/*"
            ],
            [
                "parent" => "admin",
                "child" => "/osago-amount/index"
            ],
            [
                "parent" => "admin",
                "child" => "/osago-amount/update"
            ],
            [
                "parent" => "admin",
                "child" => "/osago-amount/view"
            ],
            [
                "parent" => "admin",
                "child" => "/osago-partner-rating/*"
            ],
            [
                "parent" => "admin",
                "child" => "/osago-partner-rating/create"
            ],
            [
                "parent" => "admin",
                "child" => "/osago-partner-rating/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/osago-partner-rating/index"
            ],
            [
                "parent" => "admin",
                "child" => "/osago-partner-rating/update"
            ],
            [
                "parent" => "admin",
                "child" => "/osago-partner-rating/view"
            ],
            [
                "parent" => "admin",
                "child" => "/osago/*"
            ],
            [
                "parent" => "admin",
                "child" => "/osago/change-status"
            ],
            [
                "parent" => "admin",
                "child" => "/osago/send-request-to-get-policy"
            ],
            [
                "parent" => "admin",
                "child" => "/osago/send-request-to-get-policy-status"
            ],
            [
                "parent" => "admin",
                "child" => "/osago/update"
            ],
            [
                "parent" => "admin",
                "child" => "/page/*"
            ],
            [
                "parent" => "admin",
                "child" => "/page/create"
            ],
            [
                "parent" => "admin",
                "child" => "/page/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/f-user/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/f-user/get-products"
            ],
            [
                "parent" => "admin",
                "child" => "/f-user/*"
            ],
            [
                "parent" => "admin",
                "child" => "/page/index"
            ],
            [
                "parent" => "admin",
                "child" => "/page/update"
            ],
            [
                "parent" => "admin",
                "child" => "/page/view"
            ],
            [
                "parent" => "admin",
                "child" => "/partner-product/*"
            ],
            [
                "parent" => "admin",
                "child" => "/partner-product/create"
            ],
            [
                "parent" => "admin",
                "child" => "/partner-product/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/partner-product/index"
            ],
            [
                "parent" => "admin",
                "child" => "/partner-product/update"
            ],
            [
                "parent" => "admin",
                "child" => "/partner-product/view"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/*"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/all"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/create"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/index"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/kasko-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/kasko-by-subscription"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/kasko-by-subscription-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/kasko-by-subscription-export"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/kaskos"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/kaskos-export"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/osago"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/osago-export"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/products"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/top-products"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/update"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/view"
            ],
            [
                "parent" => "admin",
                "child" => "/period/*"
            ],
            [
                "parent" => "admin",
                "child" => "/period/all"
            ],
            [
                "parent" => "admin",
                "child" => "/period/create"
            ],
            [
                "parent" => "admin",
                "child" => "/period/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/period/export"
            ],
            [
                "parent" => "admin",
                "child" => "/period/index"
            ],
            [
                "parent" => "admin",
                "child" => "/period/update"
            ],
            [
                "parent" => "admin",
                "child" => "/period/view"
            ],
            [
                "parent" => "admin",
                "child" => "/permission/*"
            ],
            [
                "parent" => "admin",
                "child" => "/permission/all"
            ],
            [
                "parent" => "admin",
                "child" => "/permission/assign"
            ],
            [
                "parent" => "admin",
                "child" => "/permission/create"
            ],
            [
                "parent" => "admin",
                "child" => "/permission/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/permission/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/permission/remove"
            ],
            [
                "parent" => "admin",
                "child" => "/permission/update"
            ],
            [
                "parent" => "admin",
                "child" => "/product/*"
            ],
            [
                "parent" => "admin",
                "child" => "/product/create"
            ],
            [
                "parent" => "admin",
                "child" => "/product/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/product/index"
            ],
            [
                "parent" => "admin",
                "child" => "/product/update"
            ],
            [
                "parent" => "admin",
                "child" => "/product/view"
            ],
            [
                "parent" => "admin",
                "child" => "/promo/*"
            ],
            [
                "parent" => "admin",
                "child" => "/promo/create"
            ],
            [
                "parent" => "admin",
                "child" => "/promo/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/promo/index"
            ],
            [
                "parent" => "admin",
                "child" => "/promo/update"
            ],
            [
                "parent" => "admin",
                "child" => "/promo/view"
            ],
            [
                "parent" => "admin",
                "child" => "/qa/*"
            ],
            [
                "parent" => "admin",
                "child" => "/qa/create"
            ],
            [
                "parent" => "admin",
                "child" => "/qa/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/qa/index"
            ],
            [
                "parent" => "admin",
                "child" => "/qa/update"
            ],
            [
                "parent" => "admin",
                "child" => "/qa/view"
            ],
            [
                "parent" => "admin",
                "child" => "/region/*"
            ],
            [
                "parent" => "admin",
                "child" => "/region/all"
            ],
            [
                "parent" => "admin",
                "child" => "/region/create"
            ],
            [
                "parent" => "admin",
                "child" => "/region/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/region/export"
            ],
            [
                "parent" => "admin",
                "child" => "/region/index"
            ],
            [
                "parent" => "admin",
                "child" => "/region/update"
            ],
            [
                "parent" => "admin",
                "child" => "/region/view"
            ],
            [
                "parent" => "admin",
                "child" => "/relationship/*"
            ],
            [
                "parent" => "admin",
                "child" => "/relationship/all"
            ],
            [
                "parent" => "admin",
                "child" => "/relationship/create"
            ],
            [
                "parent" => "admin",
                "child" => "/relationship/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/relationship/export"
            ],
            [
                "parent" => "admin",
                "child" => "/relationship/index"
            ],
            [
                "parent" => "admin",
                "child" => "/relationship/update"
            ],
            [
                "parent" => "admin",
                "child" => "/relationship/view"
            ],
            [
                "parent" => "admin",
                "child" => "/role/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/sale/*"
            ],
            [
                "parent" => "admin",
                "child" => "/sale/export"
            ],
            [
                "parent" => "admin",
                "child" => "/sale/products"
            ],
            [
                "parent" => "admin",
                "child" => "/sale/statistics"
            ],
            [
                "parent" => "admin",
                "child" => "/site/*"
            ],
            [
                "parent" => "admin",
                "child" => "/site/accident"
            ],
            [
                "parent" => "admin",
                "child" => "/site/accident-delete"
            ],
            [
                "parent" => "admin",
                "child" => "/site/accident-view"
            ],
            [
                "parent" => "admin",
                "child" => "/site/error"
            ],
            [
                "parent" => "admin",
                "child" => "/site/index"
            ],
            [
                "parent" => "admin",
                "child" => "/site/kasko"
            ],
            [
                "parent" => "admin",
                "child" => "/site/kasko-delete"
            ],
            [
                "parent" => "admin",
                "child" => "/site/kasko-remove-from-surveyer"
            ],
            [
                "parent" => "admin",
                "child" => "/site/kasko-view"
            ],
            [
                "parent" => "admin",
                "child" => "/site/login"
            ],
            [
                "parent" => "admin",
                "child" => "/site/logout"
            ],
            [
                "parent" => "admin",
                "child" => "/site/osago"
            ],
            [
                "parent" => "admin",
                "child" => "/site/osago-delete"
            ],
            [
                "parent" => "admin",
                "child" => "/site/osago-view"
            ],
            [
                "parent" => "admin",
                "child" => "/site/translate-update"
            ],
            [
                "parent" => "admin",
                "child" => "/site/translates"
            ],
            [
                "parent" => "admin",
                "child" => "/site/travel"
            ],
            [
                "parent" => "admin",
                "child" => "/site/travel-delete"
            ],
            [
                "parent" => "admin",
                "child" => "/site/travel-view"
            ],
            [
                "parent" => "admin",
                "child" => "/sms-template/*"
            ],
            [
                "parent" => "admin",
                "child" => "/sms-template/all"
            ],
            [
                "parent" => "admin",
                "child" => "/sms-template/create"
            ],
            [
                "parent" => "admin",
                "child" => "/sms-template/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/sms-template/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/sms-template/pause"
            ],
            [
                "parent" => "admin",
                "child" => "/sms-template/run"
            ],
            [
                "parent" => "admin",
                "child" => "/sms-template/update"
            ],
            [
                "parent" => "admin",
                "child" => "/surveyer/*"
            ],
            [
                "parent" => "admin",
                "child" => "/surveyer/all"
            ],
            [
                "parent" => "admin",
                "child" => "/surveyer/create"
            ],
            [
                "parent" => "admin",
                "child" => "/surveyer/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/surveyer/export"
            ],
            [
                "parent" => "admin",
                "child" => "/surveyer/get-by-id"
            ],
            [
                "parent" => "admin",
                "child" => "/surveyer/index"
            ],
            [
                "parent" => "admin",
                "child" => "/surveyer/kaskos"
            ],
            [
                "parent" => "admin",
                "child" => "/surveyer/set-service-amount"
            ],
            [
                "parent" => "admin",
                "child" => "/surveyer/statistics"
            ],
            [
                "parent" => "admin",
                "child" => "/surveyer/update"
            ],
            [
                "parent" => "admin",
                "child" => "/surveyer/view"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-age-group/*"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-age-group/create"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-age-group/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-age-group/index"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-age-group/update"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-age-group/view"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-extra-insurance/*"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-extra-insurance/create"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-extra-insurance/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-extra-insurance/index"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-extra-insurance/partner-coeffs"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-extra-insurance/update"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-extra-insurance/view"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-family-koef/*"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-family-koef/create"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-family-koef/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-family-koef/index"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-family-koef/update"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-family-koef/view"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-group-type/*"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-group-type/create"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-group-type/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-group-type/index"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-group-type/partner-coeffs"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-group-type/update"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-group-type/view"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-multiple-period/*"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-multiple-period/create"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-multiple-period/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-multiple-period/index"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-multiple-period/update"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-multiple-period/view"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program-period/*"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program-period/create"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program-period/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program-period/index"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program-period/program-list"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program-period/set-amounts"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program-period/set-travel-info"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program-period/update"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program-period/view"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program/*"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program/create"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program/index"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program/update"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-program/view"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-purpose/*"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-purpose/create"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-purpose/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-purpose/index"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-purpose/partner-coeffs"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-purpose/update"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-purpose/view"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk-category/*"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk-category/create"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk-category/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk-category/index"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk-category/update"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk-category/view"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk/*"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk/create"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk/index"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk/set-amounts"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk/update"
            ],
            [
                "parent" => "admin",
                "child" => "/travel-risk/view"
            ],
            [
                "parent" => "admin",
                "child" => "/url-counter/*"
            ],
            [
                "parent" => "admin",
                "child" => "/url-counter/create"
            ],
            [
                "parent" => "admin",
                "child" => "/url-counter/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/url-counter/index"
            ],
            [
                "parent" => "admin",
                "child" => "/url-counter/update"
            ],
            [
                "parent" => "admin",
                "child" => "/url-counter/view"
            ],
            [
                "parent" => "admin",
                "child" => "/user/*"
            ],
            [
                "parent" => "admin",
                "child" => "/user/all"
            ],
            [
                "parent" => "admin",
                "child" => "/user/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/user/export"
            ],
            [
                "parent" => "admin",
                "child" => "/user/index"
            ],
            [
                "parent" => "admin",
                "child" => "/user/update"
            ],
            [
                "parent" => "admin",
                "child" => "/user/update-password"
            ],
            [
                "parent" => "admin",
                "child" => "/user/update-profile"
            ],
            [
                "parent" => "admin",
                "child" => "/user/view"
            ],
            [
                "parent" => "admin",
                "child" => "/warehouse/*"
            ],
            [
                "parent" => "admin",
                "child" => "/warehouse/create"
            ],
            [
                "parent" => "admin",
                "child" => "/warehouse/delete"
            ],
            [
                "parent" => "admin",
                "child" => "/warehouse/index"
            ],
            [
                "parent" => "admin",
                "child" => "/warehouse/update"
            ],
            [
                "parent" => "admin",
                "child" => "/warehouse/view"
            ],
            [
                "parent" => "admin",
                "child" => "/zood-pay/*"
            ],
            [
                "parent" => "admin",
                "child" => "/zood-pay/transaction-delivery"
            ],
            [
                "parent" => "admin",
                "child" => "/partner/osago-by-id"
            ],
            [
                "parent" => "callcenter",
                "child" => "/partner/osago-by-id"
            ],
            [
                "parent" => "callcenter",
                "child" => "/partner/kasko-by-id"
            ],
            [
                "parent" => "callcenter",
                "child" => "/partner/kasko-by-subscription-by-id"
            ],
            [
                "parent" => "callcenter",
                "child" => "/partner/travel-by-id"
            ],
            [
                "parent" => "callcenter",
                "child" => "/sales-for-call-center/products"
            ],
            [
                "parent" => "callcenter",
                "child" => "/sales-for-call-center/update"
            ],
            [
                "parent" => "callcenter",
                "child" => "/osago/send-request-to-get-policy"
            ],
            [
                "parent" => "callcenter",
                "child" => "/osago/send-request-to-get-policy-status"
            ],
            [
                "parent" => "callcenter",
                "child" => "/sales-for-call-center/*"
            ],
            [
                "parent" => "callcenter",
                "child" => "/sales-for-call-center/call"
            ],
            [
                "parent" => "callcenter",
                "child" => "/sales-for-call-center/export"
            ],
            [
                "parent" => "callcenter",
                "child" => "/sales-for-call-center/statistics"
            ],
            [
                "parent" => "admin",
                "child" => "call-center"
            ],
            [
                "parent" => "admin",
                "child" => "callcenter"
            ],
            [
                "parent" => "callcenter",
                "child" => "call-center"
            ],
            [
                "parent" => "callcenter",
                "child" => "/reason/all"
            ],
            [
                "parent" => "callcenter",
                "child" => "/reason/get-by-id"
            ],
            [
                "parent" => "callcenter",
                "child" => "/reason/create"
            ],
            [
                "parent" => "admin",
                "child" => "control-dostup-menu"
            ],
            [
                "parent" => "admin",
                "child" => "faq-menu"
            ],
            [
                "parent" => "surveyer",
                "child" => "faq-menu"
            ],
            [
                "parent" => "admin",
                "child" => "surveyer-menu"
            ],
            [
                "parent" => "admin",
                "child" => "reports-menu"
            ],
            [
                "parent" => "admin",
                "child" => "agents-menu"
            ],
            [
                "parent" => "admin",
                "child" => "promo-menu"
            ],
            [
                "parent" => "admin",
                "child" => "messages-menu"
            ],
            [
                "parent" => "admin",
                "child" => "opinions-menu"
            ],
            [
                "parent" => "admin",
                "child" => "products-menu"
            ],
            [
                "parent" => "admin",
                "child" => "marketing-menu"
            ],
            [
                "parent" => "admin",
                "child" => "sales-menu"
            ],
            [
                "parent" => "admin",
                "child" => "home-menu"
            ],
            [
                "parent" => "statistic",
                "child" => "home-menu"
            ],
            [
                "parent" => "statistic",
                "child" => "marketing-menu"
            ],
            [
                "parent" => "statistic",
                "child" => "/home/sales-graph"
            ],
            [
                "parent" => "partner",
                "child" => "interface-menu"
            ],
            [
                "parent" => "partner",
                "child" => "/car-inspection-for-partner/*"
            ],
            [
                "parent" => "partner",
                "child" => "/car-inspection-for-partner/all"
            ],
            [
                "parent" => "partner",
                "child" => "/car-inspection-for-partner/create-car-inspection"
            ],
            [
                "parent" => "partner",
                "child" => "/car-inspection-for-partner/export"
            ],
            [
                "parent" => "partner",
                "child" => "/car-inspection-for-partner/get-by-id"
            ],
            [
                "parent" => "partner",
                "child" => "/car-inspection-for-partner/statistics"
            ],
            [
                "parent" => "statistic",
                "child" => "/home/statistics"
            ],
            [
                "parent" => "statistic",
                "child" => "/region/all"
            ],
            [
                "parent" => "statistic",
                "child" => "/sale/statistics"
            ],
            [
                "parent" => "statistic",
                "child" => "/sale/products"
            ],
            [
                "parent" => "admin",
                "child" => "/f-user/statistics"
            ],
            [
                "parent" => "statistic",
                "child" => "/f-user/statistics"
            ],
            [
                "parent" => "admin",
                "child" => "/f-user/all"
            ],
            [
                "parent" => "statistic",
                "child" => "/f-user/all"
            ],
            [
                "parent" => "statistic",
                "child" => "/f-user/get-products"
            ],
            [
                "parent" => "admin",
                "child" => "/f-user/send-sms-or-telegram-message"
            ],
            [
                "parent" => "callcenter",
                "child" => "/f-user/send-sms-or-telegram-message"
            ],
            [
                "parent" => "statistic",
                "child" => "/f-user/product-counts-by-policy-end-date"
            ],
            [
                "parent" => "statistic",
                "child" => "/f-user/users-by-policy-end-date"
            ],
            [
                "parent" => "bridge_company",
                "child" => "/bridge-company-profile/*"
            ],
            [
                "parent" => "bridge_company",
                "child" => "/bridge-company-profile/osago"
            ],
            [
                "parent" => "bridge_company",
                "child" => "/bridge-company-profile/osago-by-id"
            ],
            [
                "parent" => "bridge_company",
                "child" => "/bridge-company-profile/osago-export"
            ],
            [
                "parent" => "bridge_company",
                "child" => "bridge-profile-menu"
            ],
            [
                "parent" => "bridge_company",
                "child" => "/partner/all"
            ]
        ];

        $this->insertData('auth_item_child', ["parent", "child"], $data);
    }
}