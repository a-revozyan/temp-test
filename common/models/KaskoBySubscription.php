<?php

namespace common\models;

use common\helpers\DateHelper;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "kasko_by_subscription".
 *
 * @property int $id
 * @property string $uuid
 * @property int|null $f_user_id
 * @property int|null $program_id
 * @property string|null $calc_type
 * @property int|null $count
 * @property int|null $amount_uzs
 * @property int|null $amount_avto
 * @property string|null $autonumber
 * @property string|null $tech_pass_series
 * @property string|null $tech_pass_number
 * @property int|null $status
 * @property string|null $created_at
 * @property int|null $saved_card_id
 * @property string|null $applicant_name
 * @property string|null $applicant_pass_series
 * @property string|null $applicant_pass_number
 * @property string|null $applicant_birthday
 * @property integer|null $job_id
 * @property integer|null $bridge_company_id
 * @property integer|null $partner_id
 * @property string|null $applicant_pinfl
 *
 * @property savedCard|null $savedCard
 * @property User|null $fUser
 * @property KaskoBySubscriptionPolicy|null $lastKaskoBySubscriptionPolicy
 *
 * @property int|null $promo_id
 * @property double|null $promo_amount
 * @property double|null $promo_percent
 * @property Promo $promo
 * @property Partner $partner
 */
class KaskoBySubscription extends \yii\db\ActiveRecord
{
    public $policies_count;
    public const STATUS = [
        'step1' => 1,
        'step2' => 2,
        'step3' => 3,
        'step4' => 4,
        'step5' => 5,
        'step6' => 6,
        'payed' => 7,
        'canceled' => 8,
    ];

    public const DEFAULT_CALC_TYPE = "m";
    public const DEFAULT_COUNT = 1;

    public const NEO_PROGRAMS_BY_GROSS = [
        1 => 0,
        2 => 1,
        3 => 2,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kasko_by_subscription';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['f_user_id', 'program_id', 'count', 'amount_uzs', 'status', 'saved_card_id'], 'default', 'value' => null],
            [['f_user_id', 'program_id', 'count', 'amount_uzs', 'status', 'saved_card_id', 'amount_avto', 'job_id'], 'integer'],
            [['begin_date', 'created_at', 'applicant_name', 'applicant_pass_series', 'applicant_pass_number', 'applicant_birthday'], 'safe'],
            [['calc_type'], 'string', 'max' => 3],
            [['autonumber', 'tech_pass_series', 'tech_pass_number', 'applicant_pinfl'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'f_user_id' => 'F User ID',
            'program_id' => 'Program ID',
            'calc_type' => 'Calc Type',
            'count' => 'Count',
            'amount_uzs' => 'amount_uzs',
            'autonumber' => 'Autonumber',
            'tech_pass_series' => 'Tech Pass Series',
            'tech_pass_number' => 'Tech Pass Number',
            'status' => 'Status',
            'begin_date' => 'Begin Date',
            'created_at' => 'Created At',
            'saved_card_id' => 'Saved Card ID',
            'applicant_name' => 'Applicant Name',
            'applicant_pass_series' => 'Applicant Pass Series',
            'applicant_pass_number' => 'Applicant Pass Number',
            'applicant_birthday' => 'Applicant Birthday',
        ];
    }

    public function beforeSave($insert)
    {
        StatusHistory::create($this);
        return parent::beforeSave($insert);
    }

    public function getSavedCard()
    {
        return $this->hasOne(SavedCard::className(), ['id' => 'saved_card_id']);
    }

    public function getFUser()
    {
        return $this->hasOne(User::className(), ['id' => 'f_user_id']);
    }

    public function getKaskoBySubscriptionPolicy()
    {
        return $this->hasMany(KaskoBySubscriptionPolicy::className(), ['id' => 'kasko_by_subscription_id']);
    }

    public function getLastKaskoBySubscriptionPolicy()
    {
        return $this->hasOne(KaskoBySubscriptionPolicy::className(), ['kasko_by_subscription_id' => 'id'])
            ->where(['in', 'status', [KaskoBySubscriptionPolicy::STATUS['payed'], KaskoBySubscriptionPolicy::STATUS['waiting_for_policy'], KaskoBySubscriptionPolicy::STATUS['received_policy']]])
            ->orderBy('id desc');
    }

    public static function getPrograms()
    {
        return [
            1 => [
                "id" => 1,
                "name" => "AVTO LIMIT 1",
                "amount_avto" => 10000000,
                "amount" => 60000,
                "min_day" => 31,
                "max_day" => 31
            ],
            2 => [
                "id" => 2,
                "name" => "AVTO LIMIT 2",
                "amount_avto" => 20000000,
                "amount" => 90000,
                "min_day" => 31,
                "max_day" => 31
            ],
            3 => [
                "id" => 3,
                "name" => "AVTO LIMIT 3",
                "amount_avto" => 30000000,
                "amount" => 110000,
                "min_day" => 31,
                "max_day" => 31
            ]
        ];

        return OsagoRequest::sendKaskoBySubscriptionPolicyRequest(OsagoRequest::URLS['kasko_by_subscription_programs'], (new KaskoBySubscriptionPolicy()), [])['response'][0];
    }

    public function getPromo()
    {
        return $this->hasOne(Promo::className(), ['id' => 'promo_id']);
    }

    /**
     * Gets query for [[Partner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }

    public function getFullClientArr($programs = [])
    {
        $lastKaskoBySubscriptionPolicy = $this->lastKaskoBySubscriptionPolicy;
        $remaining_days = is_null($lastKaskoBySubscriptionPolicy->end_date ?? null) ? null : round((strtotime($lastKaskoBySubscriptionPolicy->end_date) - strtotime(date('Y-m-d 23:59:59'))) / 86400);

        $arr = [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'program_id' => $this->program_id,
            'amount_uzs' => $this->amount_uzs + $this->promo_amount,
            'amount_avto' => $this->amount_avto,
            'autonumber' => $this->autonumber,
            'tech_pass_series' => $this->tech_pass_series,
            'tech_pass_number' => $this->tech_pass_number,
            'applicant_name' => $this->applicant_name,
            'applicant_pass_series' => $this->applicant_pass_series,
            'applicant_pass_number' => $this->applicant_pass_number,
            'applicant_birthday' => $this->applicant_birthday,
            'status' => $this->status,
            "promo" => [
                "id" => $this->promo_id,
                "promo_code" => is_null($this->promo) ? null : $this->promo->code,
                "promo_percent" => $this->promo_percent,
                "promo_amount" => $this->promo_amount,
            ],
            "partner" => !empty($this->partner_id) ? $this->partner->getForIdNameArr() : null,
            'saved_card' => empty($this->savedCard) ? null : $this->savedCard->getShortArr(),
            'last_kasko_by_subscription_policy' => []
        ];

        if (!empty($lastKaskoBySubscriptionPolicy))
            $arr = array_merge($arr,[
                'last_kasko_by_subscription_policy' => [
                    'remaining_days' => $remaining_days,
                    'id' => $lastKaskoBySubscriptionPolicy->id,
                    'payed_date' => is_null($lastKaskoBySubscriptionPolicy->payed_date ?? null) ? null : DateHelper::date_format($lastKaskoBySubscriptionPolicy->payed_date, 'Y-m-d H:i:s', 'd.m.Y'),
                    'end_date' => is_null($lastKaskoBySubscriptionPolicy->end_date ?? null) ? null : DateHelper::date_format($lastKaskoBySubscriptionPolicy->end_date, 'Y-m-d H:i:s', 'd.m.Y'),
                    'policy_pdf_url' => $lastKaskoBySubscriptionPolicy->policy_pdf_url,
                ]
            ]);

        if (!empty($programs))
            $arr = array_merge($arr, [
                'program_name' => ArrayHelper::map($programs, 'id', 'name')[$this->program_id]
            ]);

        return $arr;
    }

    public static function getShortClientArrCollection($products)
    {
        $programs = KaskoBySubscription::getPrograms();
        $_products = [];
        foreach ($products as $product) {
            $_products[] = $product->getShortClientArr($programs);
        }
        return $_products;
    }

    public function getShortClientArr($programs = [])
    {
        /** @var KaskoBySubscriptionPolicy $lastKaskoBySubscriptionPolicy */
        $lastKaskoBySubscriptionPolicy = $this->lastKaskoBySubscriptionPolicy;
        $remaining_days = is_null($lastKaskoBySubscriptionPolicy->end_date ?? null) ? null : round((strtotime($lastKaskoBySubscriptionPolicy->end_date) - strtotime(date('Y-m-d 23:59:59'))) / 86400);
        $arr = [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'program_id' => $this->id,
            'amount_uzs' => $this->amount_uzs + $this->promo_amount,
            'autonumber' => $this->autonumber,
            'status' => $this->status,
            'last_kasko_by_subscription_policy' => [
                'remaining_days' => $remaining_days
            ]
        ];

        if (!empty($programs))
            $arr = array_merge($arr, [
                'program_name' => ArrayHelper::map($programs, 'id', 'name')[$this->program_id]
            ]);

        return $arr;
    }

    public static function getShortAdminArrCollection($products)
    {
        $_products = [];
        foreach ($products as $product) {
            $_products[] = $product->getShortAdminArr();
        }
        return $_products;
    }

    public function getShortAdminArr()
    {
        /** @var KaskoBySubscriptionPolicy $lastKaskoBySubscriptionPolicy */
        $lastKaskoBySubscriptionPolicy = $this->lastKaskoBySubscriptionPolicy;
        $arr = [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'applicant_name' => $this->applicant_name,
            'phone' => $this->fUser->phone ?? null,
            'amount_uzs' => $this->amount_uzs,
            'autonumber' => $this->autonumber,
            'tech_pass_series' => $this->tech_pass_series,
            'tech_pass_number' => $this->tech_pass_number,
            'payment_type' => "payme",
            'status' => $this->status,
            "promo" => [
                "id" => $this->promo_id,
                "promo_code" => is_null($this->promo) ? null : $this->promo->code,
                "promo_percent" => $this->promo_percent,
                "promo_amount" => $this->promo_amount,
            ],
            'created_at' => DateHelper::date_format($this->created_at, 'Y-m-d H:i:s', 'd.m.Y H:i:s'),
            'policies_count' => $this->policies_count,
            'saved_card' => is_null($this->savedCard) ? null : $this->savedCard->getShortArr(),
            'last_kasko_by_subscription_policy' => []
        ];

        if (!empty($lastKaskoBySubscriptionPolicy))
            $arr = array_merge($arr,[
                'last_kasko_by_subscription_policy' => [
                    'id' => $lastKaskoBySubscriptionPolicy->id,
                    'begin_date' => is_null($lastKaskoBySubscriptionPolicy->begin_date ?? null) ? null : DateHelper::date_format($lastKaskoBySubscriptionPolicy->begin_date, 'Y-m-d H:i:s', 'd.m.Y'),
                    'end_date' => is_null($lastKaskoBySubscriptionPolicy->end_date ?? null) ? null : DateHelper::date_format($lastKaskoBySubscriptionPolicy->end_date, 'Y-m-d H:i:s', 'd.m.Y'),
                    'policy_pdf_url' => $lastKaskoBySubscriptionPolicy->policy_pdf_url,
                ],
                'amount_uzs' => $lastKaskoBySubscriptionPolicy->amount_uzs,
            ]);

        return $arr;
    }
}
