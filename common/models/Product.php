<?php

namespace common\models;

use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataFilter;
use yii\data\Sort;
use yii\db\Expression;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property PartnerProduct[] $partnerProducts
 */
class Product extends \yii\db\ActiveRecord
{
    public const products = [
        'osago' => 1,
        'kasko' => 2,
        'travel' => 3,
        'accident' => 4,
        'kasko-by-subscription' => 5,
    ];

    public const models = [
        1 => Osago::class,
        2 => Kasko::class,
        3 => Travel::class,
        4 => Accident::class,
        5 => KaskoBySubscriptionPolicy::class,
    ];

    public const TYPE = [
        'active' => 0,
        'old' => 1,
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'code'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[PartnerProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerProducts()
    {
        return $this->hasMany(PartnerProduct::className(), ['product_id' => 'id']);
    }

    public static function getPayedWhere()
    {
        return [
            'or',
            [
                'and',
                ['in', 'products.status', [Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']]],
                ['product' => Product::products['osago']]
            ],
            [
                'and',
                ['in', 'products.status', [Kasko::STATUS['payed'], Kasko::STATUS['attached'], Kasko::STATUS['processed'], Kasko::STATUS['policy_generated']]],
                ['product' => Product::products['kasko']]
            ],
            [
                'and',
                ['in', 'products.status', [Accident::STATUS['payed'], Accident::STATUS['waiting_for_policy'], Accident::STATUS['received_policy']]],
                ['product' => Product::products['accident']]
            ],
            [
                'and',
                ['in', 'products.status', [KaskoBySubscriptionPolicy::STATUS['payed'], KaskoBySubscriptionPolicy::STATUS['waiting_for_policy'], KaskoBySubscriptionPolicy::STATUS['received_policy']]],
                ['product' => Product::products['kasko-by-subscription']]
            ],
            [
                'and',
                ['in', 'products.status', [Travel::STATUSES['payed'], Travel::STATUSES['waiting_for_policy'], Travel::STATUSES['received_policy']]],
                ['product' => Product::products['travel']]
            ],
        ];
    }

    public static function getProductsQuery()
    {
        $default_autonumber = null;
        //new Expression( "to_char(to_timestamp(processed_date), 'YYYY-MM-DD') as policy_generated_date")
        $kaskos = Kasko::find()->select([
            'kasko.id as product_id', new Expression( 'coalesce(payed_date, 0) as policy_generated_date'),//processed_date
            'kasko.status', new Expression( Product::products['kasko'].' as product'),
            'policy_number', 'amount_uzs' => 'COALESCE(amount_uzs, 0)', 'payment_type' => 'transaction.payment_type', 'partner_name' => 'partner.name',
            'partner_id' => 'partner.id', 'kasko.f_user_id', 'f_user_name' => 'f_user.first_name', 'f_user_phone' => 'f_user.phone',
            'insurer_phone', 'insurer_name',
            new Expression( 'left(autonumber, 2) as region'), new Expression('null::integer as number_drivers_id'),
            'autonumber', 'reason.name as reason', 'reason.id as reason_id', 'kasko.comment', 'promo_id'
        ])
            ->leftJoin('transaction', 'transaction.id = kasko.trans_id')
            ->leftJoin('f_user', 'f_user.id = kasko.f_user_id')
            ->leftJoin('partner', 'partner.id = kasko.partner_id')
            ->leftJoin('reason', 'reason.id = kasko.reason_id');

        $travels = Travel::find()->select([
            'travel.id as product_id',  new Expression( 'coalesce(payed_date, 0) as policy_generated_date'),//processed_date
            'travel.status', new Expression( Product::products['travel'].' as product'),
            'policy_number', 'amount_uzs' => 'COALESCE(amount_uzs, 0)', 'payment_type' => 'transaction.payment_type', 'partner_name' => 'partner.name',
            'partner_id' => 'partner.id', 'travel.f_user_id', 'f_user_name' => 'f_user.first_name', 'f_user_phone' => 'f_user.phone',
            'insurer_phone', 'insurer_name',
            new Expression( "'" . $default_autonumber . "'" . ' as region'), new Expression('null::integer as number_drivers_id'),
            new Expression('null::text as autonumber'), 'reason.name as reason', 'reason.id as reason_id', 'travel.comment', 'promo_id'
        ])
            ->leftJoin('transaction', 'transaction.id = travel.trans_id')
            ->leftJoin('f_user', 'f_user.id = travel.f_user_id')
            ->leftJoin('partner', 'partner.id = travel.partner_id')
            ->leftJoin('reason', 'reason.id = travel.reason_id');

        $osagos = Osago::find()->select([
            'osago.id as product_id',  new Expression( 'coalesce(osago.payed_date, 0) as policy_generated_date'),//processed_date
            'osago.status', new Expression( Product::products['osago'].' as product'),
            'policy_number','amount_uzs' => 'COALESCE(amount_uzs, 0)',
            'payment_type' => 'transaction.payment_type', 'partner_name' => 'partner.name',
            'partner_id' => 'partner.id', 'osago.f_user_id', 'f_user_name' => 'f_user.first_name', 'f_user_phone' => 'f_user.phone',
            'insurer_phone', 'insurer_name',
            new Expression( 'left(autonumber, 2) as region'), 'number_drivers_id', 'autonumber',
            'reason.name as reason', 'reason.id as reason_id', 'osago.comment', 'promo_id'
        ])
            ->leftJoin('transaction', 'transaction.id = osago.trans_id')
            ->leftJoin('f_user', 'f_user.id = osago.f_user_id')
            ->leftJoin('partner', 'partner.id = osago.partner_id')
            ->leftJoin('reason', 'reason.id = osago.reason_id');

        $accidents = Accident::find()->select([
            'accident.id as product_id',  new Expression( "coalesce(extract(epoch from payed_date)::integer-18000, 0) as policy_generated_date"),
            'accident.status', new Expression( Product::products['accident'].' as product'),
            'policy_number','amount_uzs' => 'COALESCE(amount_uzs, 0)',
            'payment_type' => 'transaction.payment_type', 'partner_name' => 'partner.name',
            'partner_id' => 'partner.id', 'accident.f_user_id', 'f_user_name' => 'f_user.first_name', 'f_user_phone' => 'f_user.phone',
            'insurer_phone', 'insurer_name',
            new Expression( '00::TEXT as region'), new Expression('null::integer as number_drivers_id'),
            new Expression('null::text as autonumber'), 'reason.name as reason', 'reason.id as reason_id', 'accident.comment', 'promo_id'
        ])
            ->leftJoin('transaction', 'transaction.id = accident.trans_id')
            ->leftJoin('f_user', 'f_user.id = accident.f_user_id')
            ->leftJoin('partner', 'partner.id = accident.partner_id')
            ->leftJoin('reason', 'reason.id = accident.reason_id');

        $kbsp = KaskoBySubscriptionPolicy::find()->select([
            'kasko_by_subscription_policy.id as product_id',  new Expression( "coalesce(extract(epoch from payed_date)::integer-18000, 0) as policy_generated_date"),
            'kasko_by_subscription_policy.status', new Expression( Product::products['kasko-by-subscription'].' as product'),
            'policy_number','amount_uzs' => 'COALESCE(kasko_by_subscription_policy.amount_uzs, 0)',
            'payment_type' => 'transaction.payment_type', 'partner_name' => 'partner.name',
            'partner_id' => 'partner.id', 'kasko_by_subscription.f_user_id', 'f_user_name' => 'f_user.first_name', 'f_user_phone' => 'f_user.phone',
            new Expression('null::TEXT as insurer_phone'), 'insurer_name' => 'kasko_by_subscription.applicant_name',
            new Expression( 'left(kasko_by_subscription.autonumber, 2) as region'), new Expression('null::integer as number_drivers_id'),
            'kasko_by_subscription.autonumber', 'reason.name as reason', 'reason.id as reason_id', 'kasko_by_subscription_policy.comment',
            new Expression('kasko_by_subscription.promo_id as promo_id'),
        ])
            ->leftJoin('transaction', 'transaction.id = kasko_by_subscription_policy.trans_id')
            ->leftJoin('kasko_by_subscription', 'kasko_by_subscription_policy.kasko_by_subscription_id = kasko_by_subscription.id')
            ->leftJoin('f_user', 'f_user.id = kasko_by_subscription.f_user_id')
            ->leftJoin('partner', 'partner.id = kasko_by_subscription_policy.partner_id')
            ->leftJoin('reason', 'reason.id = kasko_by_subscription_policy.reason_id');

        return (new \yii\db\Query())
            ->select([
                'products.*'
            ])
            ->from([
                'products' => $kaskos->union($osagos)->union($accidents)->union($kbsp)->union($travels)
            ])
            ->leftJoin('agent', 'products.f_user_id = agent.f_user_id');

    }

    public static function products()
    {
       $products = self::getProductsQuery();

        $filter = new ActiveDataFilter([
            'searchModel' => (new DynamicModel(['product_id', 'region', 'policy_generated_date', 'partner_id', 'payment_type', 'agent.id', 'products.f_user_id', 'product', 'status']))
                ->addRule(['product_id'], 'integer')
                ->addRule(['region'], 'string')
                ->addRule(['policy_generated_date'], 'integer')
                ->addRule(['partner_id'], 'integer')
                ->addRule(['agent.id'], 'integer')
                ->addRule(['payment_type'], 'string')
                ->addRule(['products.f_user_id'], 'integer')
                ->addRule(['product'], 'integer')
                ->addRule(['status'], 'integer')
        ]);

        $filterCondition = null;
        if ($filter->load(Yii::$app->request->get())) {
            $filterCondition = $filter->build();
            if ($filterCondition === false) {
                // Serializer would get errors out of it
                return $filter;
            }
        }

        if ($filterCondition !== null) {
            $products->andWhere($filterCondition);
        }

        $sort = new Sort([
            'attributes' => [
                'policy_generated_date','amount_uzs', 'product_id',
            ],
            'defaultOrder' => ['policy_generated_date' => SORT_DESC]
        ]);
        $products->orderBy($sort->orders);

        return $products;
    }

    public static function getShortArrCollection($products)
    {
        $_products = [];
        foreach ($products as $product) {
            $_products[] = $product->getShortArr();
        }
        return $_products;
    }

    public static function getShortArrCollectionForCallCenter($products)
    {
        $_products = [];
        foreach ($products as $product) {
            $_products[] = self::getShortArrForCallCenter($product);
        }
        return $_products;
    }

    public static function getShortArrForCallCenter($product)
    {
        return [
            'product' => $product['product'],
            'product_id' => $product['product_id'],
            'autonumber' => $product['autonumber'],
            'created_at' => $product['policy_generated_date'],
            'f_user_phone' => $product['f_user_phone'],
            'f_user_id' => $product['f_user_id'],
            'status' => $product['status'],
            'reason' => $product['reason'],
            'comment' => $product['comment'],
        ];
    }
    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public static function getIdNameCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[$model->id] = $model->getIdNameArr();
        }

        return $_models;
    }

    public function getIdNameArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public static function getEndDateCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = self::getEndDateArr($model);
        }

        return $_models;
    }

    public static function getEndDateArr($model)
    {
        $user = $model->user ?? null;
        if (is_null($user))
            $user = $model->fUser ?? null;
        if (!is_null($user))
            $user = [
                'id' => $user->id,
                'name' => $user->last_name . " " . $user->first_name,
                'phone' => $user->phone,
            ];

        return [
            'id' => $model->id,
            'autonumber' => $model->autonumber,
            'end_date' => $model->end_date,
            'f_user' => $user,
            'partner' => !is_null($model->partner) ? $model->partner->getForIdNameArr() : null,
        ];
    }
}
