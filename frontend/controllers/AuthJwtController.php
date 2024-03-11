<?php

namespace frontend\controllers;

use common\helpers\GeneralHelper;
use common\models\User;
use DateInterval;
use frontend\models\UserRefreshTokens;
use Yii;
use yii\filters\VerbFilter;

class AuthJwtController extends BaseController
{
    public function behaviors() {
        $behaviors = parent::behaviors();

        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'get-jwt-token' => ['GET']
            ]
        ];

        $behaviors['authenticator']['only'] = ["get-jwt-token"];

        return $behaviors;
    }

    private function generateJwt(User $user) {
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getConfiguration()->signer();
        $key = $jwt->getConfiguration()->signingKey();
        $time = new \DateTimeImmutable();

        $jwtParams = GeneralHelper::env('jwt');

        return $jwt->getBuilder()
            ->issuedBy($jwtParams['issuer'])
            ->permittedFor($jwtParams['audience'])
            ->identifiedBy($jwtParams['id'], true)
            ->issuedAt($time)
            ->expiresAt($time->add(new DateInterval($jwtParams['expire'])))
            ->withClaim('uid', $user->id)
            ->getToken($signer, $key);
    }

    private function generateRefreshToken(User $user, User $impersonator = null): UserRefreshTokens {
        $refreshToken = Yii::$app->security->generateRandomString(200);

        if (!$userRefreshToken = UserRefreshTokens::find()->where([
            'urf_userID' => $user->id,
            'urf_ip' => Yii::$app->request->userIP,
            'urf_user_agent' => Yii::$app->request->userAgent
        ])->one())
            $userRefreshToken = new UserRefreshTokens([
                'urf_userID' => $user->id,
                'urf_token' => $refreshToken,
                'urf_ip' => Yii::$app->request->userIP,
                'urf_user_agent' => Yii::$app->request->userAgent,
                'urf_created' => gmdate('Y-m-d H:i:s'),
            ]);
        if (!$userRefreshToken->save()) {
            throw new \yii\web\ServerErrorHttpException('Failed to save the refresh token: '. $userRefreshToken->getErrorSummary(true));
        }

        // Send the refresh-token to the user in a HttpOnly cookie that Javascript can never read and that's limited by path
        Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'refresh-token',
            'value' => $refreshToken,
            'httpOnly' => true,
            'sameSite' => 'none',
            'secure' => true,
            'path' => '/v1/auth/refresh-token',  //endpoint URI for renewing the JWT token using this refresh-token, or deleting refresh-token
        ]));

        return $userRefreshToken;
    }

    public function actionGetJwtToken() {
        $user = Yii::$app->user->identity;

        $token = $this->generateJwt($user);
        $this->generateRefreshToken($user);

        return $token->toString();
    }
}