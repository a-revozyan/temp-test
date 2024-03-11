<?php

namespace common\models;

use common\helpers\DateHelper;
use common\jobs\KaskoBySubscriptionPolicyRequestJob;
use common\services\partnerProduct\partnerProductService;
use common\services\SMSService;
use common\services\TelegramService;
use Yii;

/**
 * This is the model class for table "kasko_by_subscription_policy".
 *
 * @property int $id
 * @property int|null $kasko_by_subscription_id
 * @property string|null $policy_number
 * @property string|null $policy_pdf_url
 * @property string|null $begin_date
 * @property string|null $end_date
 * @property int|null $trans_id
 * @property int|null $saved_card_id
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $payed_date
 * @property int|null $order_id_in_gross
 * @property int|null $partner_id
 * @property int|null $amount_uzs
 *
 * @property KaskoBySubscription|null $kaskoBySubscription
 * @property Transaction|null $trans
 * @property Partner|null $partner
 */
class KaskoBySubscriptionPolicy extends \yii\db\ActiveRecord
{
    public const DEFAULT_PARTNER_ID = 1;
    public const STATUS = [
        'created' => 1,
        'payed' => 2,
        'waiting_for_policy' => 3,
        'received_policy' => 4,
        'canceled' => 5,
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kasko_by_subscription_policy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kasko_by_subscription_id', 'trans_id', 'saved_card_id', 'status'], 'default', 'value' => null],
            [['kasko_by_subscription_id', 'trans_id', 'saved_card_id', 'status', 'partner_id', 'reason_id'], 'integer'],
            [['begin_date', 'end_date', 'created_at', 'comment'], 'safe'],
            [['policy_number', 'policy_pdf_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kasko_by_subscription_id' => 'Kasko By Subscription ID',
            'policy_number' => 'Policy Number',
            'policy_pdf_url' => 'Policy Pdf Url',
            'begin_date' => 'Begin Date',
            'end_date' => 'End Date',
            'trans_id' => 'Trans ID',
            'saved_card_id' => 'Saved Card ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'partner_id' => 'Partner id',
        ];
    }

    public function getKaskoBySubscription()
    {
        return $this->hasOne(KaskoBySubscription::className(), ['id' => 'kasko_by_subscription_id']);
    }

    public function getTrans()
    {
        return $this->hasOne(Transaction::className(), ['id' => 'trans_id']);
    }

    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }

    public function getReason()
    {
        return $this->hasOne(Reason::className(), ['id' => 'reason_id']);
    }

    public function send_save_to_partner($message = '')
    {
        $begin_date = date('d.m.Y', strtotime(' +1 day'));

        $order_id = false;
        if (empty($this->order_id_in_gross))
        {
            if (self::find()->where(['id' => $this->id])->andWhere(['in', 'status', [self::STATUS['canceled']]])->exists())
                return false;
            if (self::find()->where(['id' => $this->id])->andWhere(['in', 'status', [self::STATUS['received_policy']]])->exists())
                return true;

            $order_id = partnerProductService::kbsSave($this, $begin_date);
        }

        if ($order_id or !empty($this->order_id_in_gross))
        {
            if (empty($this->order_id_in_gross))
            {
                $this->order_id_in_gross = $order_id;

                $this->begin_date = DateHelper::date_format($begin_date, 'd.m.Y', 'Y-m-d 00:00:00');
                $this->end_date = date('Y-m-d 23:59:59', strtotime($begin_date . ' + 1 month - 1 day'));
                $this->save();
            }

            ['policy_url' => $policy_url, 'policy_number' => $policy_number] = partnerProductService::kbsConfirm($this);
            $this->policy_number = $policy_number;
            $this->policy_pdf_url = $policy_url;

            $this->status = self::STATUS['received_policy'];
            $this->save();

            TelegramService::send($this);
            SMSService::sendMessageAll($this->kaskoBySubscription->fUser->phone, $message .  $this->policy_pdf_url, $this->kaskoBySubscription->fUser->telegram_chat_ids());
        }

        return $order_id;
    }

    public function saveAfterPayed()
    {
        $kaskoBySubscription = $this->kaskoBySubscription;

        if ($kaskoBySubscription->status == KaskoBySubscription::STATUS['canceled'])
            $message = "Sug’urta Bozor. KASKO po podpsike na " . $kaskoBySubscription->autonumber . " aktivirovan na 1 mesyac. Polis: ";
        elseif ($kaskoBySubscription->status == KaskoBySubscription::STATUS['payed'])
            $message = "Sug’urta Bozor. KASKO po podpsike na " . $kaskoBySubscription->autonumber . " prodlen na 1 mesyac. Polis: ";
        elseif ($kaskoBySubscription->status == KaskoBySubscription::STATUS['step6'])
            $message = "Sug'urta Bozor.  Nomer " . $kaskoBySubscription->autonumber . " oformlen KASKO po podpiske. Polis: ";

        $kaskoBySubscription->status = KaskoBySubscription::STATUS['payed'];
        $kaskoBySubscription->save();

        $this->status = self::STATUS['payed'];
        $this->payed_date = date('Y-m-d H:i:s');
        $this->save();

        Yii::$app->queue1->push(new KaskoBySubscriptionPolicyRequestJob(['kasko_by_subscription_policy_id' => $this->id, 'message' => $message]));

        return true;
    }

    public function getFullClientArr()
    {
        return [
//            'id' => $this->id,
//            'program_id' => $this->id,
//            'amount' => $this->amount,
//            'amount_avto' => $this->amount_avto,
//            'autonumber' => $this->autonumber,
//            'tech_pass_series' => $this->tech_pass_series,
//            'tech_pass_number' => $this->tech_pass_number,
            'status' => $this->status,
            'begin_date' => empty($this->begin_date) ? null : DateHelper::date_format($this->begin_date, 'Y-m-d H:i:s', 'd.m.Y'),
//            'saved_card' => empty($this->savedCard) ? null : $this->savedCard->getShortArr(),
        ];
    }
}
