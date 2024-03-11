<?php

namespace console\controllers;

use backapi\models\forms\smsTemplateForms\RunForm;
use common\jobs\PayKaskoBySubscriptionJob;
use common\models\KaskoBySubscription;
use common\models\KaskoBySubscriptionPolicy;
use common\models\Osago;
use common\models\PaymeSubscribeRequest;
use common\models\ShortLink;
use common\models\SmsTemplate;
use common\services\SMSService;
use frontend\controllers\PaymeController;
use Yii;
use yii\base\Controller;
use yii\helpers\VarDumper;

class PayKaskoBySubscriptionPolicyController extends Controller
{
    public function actionRun()
    {
        $kasko_by_subscription = KaskoBySubscription::find()
            ->select(["kasko_by_subscription.*", "last_policy.max_end_date", "saved_card.card_id"])
            ->leftJoin([
                "last_policy" => KaskoBySubscriptionPolicy::find()->select([
                    "max(end_date) as max_end_date",
                    'kasko_by_subscription_id',
                ])
                    ->where(['in', 'status', [
                        KaskoBySubscriptionPolicy::STATUS['payed'],
                        KaskoBySubscriptionPolicy::STATUS['waiting_for_policy'],
                        KaskoBySubscriptionPolicy::STATUS['received_policy'],
                    ]])
                    ->groupBy('kasko_by_subscription_id')
            ],
                '"last_policy"."kasko_by_subscription_id" = "kasko_by_subscription"."id"')
            ->leftJoin('saved_card', 'saved_card.id = kasko_by_subscription.saved_card_id');

        $paying_kasko_by_subscriptions = $kasko_by_subscription
            ->where(['not', ['max_end_date' => null]])
            ->where(['<=', 'max_end_date', date('Y-m-d 23:59:59')])
            ->andWhere(['not', ['kasko_by_subscription.status' => KaskoBySubscription::STATUS['canceled']]])
            ->asArray()->all();

        foreach ($paying_kasko_by_subscriptions as $kbs) {
            if (
                !empty($kbs['job_id'])
                and
                (Yii::$app->queue1->isWaiting($kbs['job_id']) or Yii::$app->queue1->isReserved($kbs['job_id']))
            )
                continue;

            $id = Yii::$app->queue1->push(new PayKaskoBySubscriptionJob(['kbs_id' => $kbs['id'], 'card_id' => $kbs['card_id']]));
            KaskoBySubscription::updateAll(['job_id' => $id], ['id' => $kbs['id']]);
        }
    }
}