<?php

namespace console\controllers\seeder;

class UserSeederController extends BaseSeederController
{
    public function actionRun()
    {
        $data = [
            [
                "id" => 112,
                "username" => "998946464400",
                "auth_key" => "KKK5oOYB4aGhbqL_E2oDKCm0GGuUO1pZ",
                "password_hash" => '$2a$12$mjdsjAUnv3bSEWV6rLmBx.R.m59L2PThiBX7D0uATQwP9jyWKyuay', //test
                "password_reset_token" => null,
                "email" => "default@partner.com",
                "status" => 10,
                "created_at" => 1688966010,
                "updated_at" => 1701064330,
                "verification_token" => null,
                "partner_id" => null,
                "access_token" => "lfMXoAEuxW90Z-zV3TYq4LsqqN3HAAbA",
                "phone_number" => "998946464400",
                "last_name" => "Yusupov",
                "first_name" => "Jobir",
                "region_id" => 1,
                "address" => null,
                "service_amount" => null
            ],
            [
                "id" => 117,
                "username" => "900053663",
                "auth_key" => "Gy3xXuiVz7IlEExJ7nQxCm5CKsPt8Swc",
                "password_hash" => '$2y$13$0rO0EDoEaZZXVM4sM2wwZ.h59TyTv1JTCygfCgmJZCcbzf36pp7DO',
                "password_reset_token" => null,
                "email" => "kamol@gmail.com",
                "status" => 10,
                "created_at" => 1703581869,
                "updated_at" => 1703581869,
                "verification_token" => null,
                "partner_id" => null,
                "access_token" => null,
                "phone_number" => "900053663",
                "last_name" => "Kamol",
                "first_name" => "Kamol",
                "region_id" => null,
                "address" => null,
                "service_amount" => null
            ],
            [
                "id" => 114,
                "username" => "998974450544",
                "auth_key" => "LfCWrD-bHYIIqxtwxw-sOnuKs189_OyA",
                "password_hash" => '$2y$13$lDwhKqNsU20AoLGf7i0yyuI5axe7AQVD2rAutBQfzUEq/ZAhVlkuG',
                "password_reset_token" => null,
                "email" => "default@partner.com",
                "status" => 10,
                "created_at" => 1692077869,
                "updated_at" => 1699961761,
                "verification_token" => null,
                "partner_id" => 18,
                "access_token" => "AdB345hJSETHsWSe6SrzVpGVQcGZUzFB",
                "phone_number" => "",
                "last_name" => "",
                "first_name" => "",
                "region_id" => null,
                "address" => null,
                "service_amount" => null
            ],
            [
                "id" => 110,
                "username" => "statistic",
                "auth_key" => "UGcSwf39OsIKnOgnHVoJzWTwBhzfgt9i",
                "password_hash" => '$2y$13$NtJM91x5St98wfSTIOFiSu0uFJtt0Rp7HSM33BiLKkm0.v8GgeWPS',
                "password_reset_token" => null,
                "email" => "default@gmail.com",
                "status" => 10,
                "created_at" => 1684220353,
                "updated_at" => 1700740917,
                "verification_token" => null,
                "partner_id" => null,
                "access_token" => "UvmXOn4-4FwytESGHG00Wb2eaFhihEod",
                "phone_number" => null,
                "last_name" => "statistic",
                "first_name" => "statistic",
                "region_id" => 1,
                "address" => null,
                "service_amount" => null
            ],
            [
                "id" => 109,
                "username" => "callcenter",
                "auth_key" => "fxolXXwvVGiTI5yBg3sxfYZoKv1cPcvd",
                "password_hash" => '$2a$12$7bf8do6pgBFCGboEC/.sBu4mQvZXZaXGCDdl278.2sHPgO8TQvpfe',
                "password_reset_token" => "",
                "email" => "default@gmail.com",
                "status" => 10,
                "created_at" => 1679642476,
                "updated_at" => 1700819404,
                "verification_token" => null,
                "partner_id" => null,
                "access_token" => "WwPIgwqhvebTGhawX3Xk4VuXxEuHjJ8c",
                "phone_number" => null,
                "last_name" => null,
                "first_name" => null,
                "region_id" => null,
                "address" => null,
                "service_amount" => null
            ]
        ];

        $this->insertData('user', ["id","username","auth_key","password_hash","password_reset_token","email","status","created_at","updated_at","verification_token","partner_id","access_token","phone_number","last_name","first_name","region_id","address","service_amount"], $data);
    }
}