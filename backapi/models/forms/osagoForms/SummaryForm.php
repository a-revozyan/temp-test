<?php
namespace backapi\models\forms\osagoForms;

use common\models\BridgeCompany;
use common\models\Kasko;
use common\models\KaskoBySubscriptionPolicy;
use common\models\NumberDrivers;
use common\models\Osago;
use common\models\Partner;
use common\models\Product;
use common\models\Travel;
use yii\base\Model;
use yii\db\Expression;


class SummaryForm extends Model
{
    public $begin_date;
    public $end_date;
    public $bridge_company_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['begin_date', 'end_date'], 'date', 'format' => 'Y-m-d'],
            [['bridge_company_id'], 'integer'],
            [['bridge_company_id'], 'exist', 'skipOnError' => true, 'targetClass' => BridgeCompany::className(), 'targetAttribute' => ['bridge_company_id' => 'id']],
        ];
    }

    public function save()
    {
        if (empty($this->begin_date))
            $this->begin_date = date('Y-m-1');
        if (empty($this->end_date))
            $this->end_date = date('Y-m-d');

        $this->begin_date .= " 00:00:00";
        $this->end_date .= " 23:59:59";

        $osagos = Osago::find()->select([
            'partner_id', 'number_drivers_id', 'status', 'count(osago.id)', 'sum(amount_uzs) as amount',
            'sum(round(amount_uzs * discount_percent / (100 + discount_percent))) * -1 as discount_amount',
            'sum(promo_amount) as promo_amount',
            new Expression(Product::products['osago'] . ' as product'),
        ])
            ->leftJoin('unique_code', 'unique_code.id = osago.unique_code_id')
            ->where(['status' => [Osago::STATUS['received_policy'], Osago::STATUS['canceled']]])
            ->andWhere(['>=', 'payed_date', date_create_from_format('Y-m-d H:i:s', $this->begin_date)->getTimestamp()])
            ->andWhere(['<=', 'payed_date', date_create_from_format('Y-m-d H:i:s', $this->end_date)->getTimestamp()])
            ->andFilterWhere(['bridge_company_id' => $this->bridge_company_id])
            ->orderBy(['partner_id' => SORT_ASC, 'number_drivers_id' => SORT_ASC])
            ->groupBy(['partner_id', 'number_drivers_id', 'status'])
            ->asArray()->all();

        $accidents = Osago::find()->select([
            'partner_id', 'status', 'count(osago.id)', 'sum(accident_amount) as amount',
            'sum(round(accident_amount * discount_percent / (100 + discount_percent))) * -1 as discount_amount',
            new Expression('0 as promo_amount'),
            new Expression('NUll as number_drivers_id'),
            new Expression(Product::products['accident'] . ' as product'),
        ])
            ->leftJoin('unique_code', 'unique_code.id = osago.unique_code_id')
            ->where(['status' => [Osago::STATUS['received_policy'], Osago::STATUS['canceled']]])
            ->andWhere(['>', 'accident_amount', 0])
            ->andWhere(['>=', 'payed_date', date_create_from_format('Y-m-d H:i:s', $this->begin_date)->getTimestamp()])
            ->andWhere(['<=', 'payed_date', date_create_from_format('Y-m-d H:i:s', $this->end_date)->getTimestamp()])
            ->andFilterWhere(['bridge_company_id' => $this->bridge_company_id])
            ->orderBy(['partner_id' => SORT_ASC])
            ->groupBy(['partner_id', 'status'])
            ->asArray()->all();

        $kaskos = Kasko::find()->select([
            'partner_id', 'status', 'count(kasko.id)', 'sum(amount_uzs) as amount',
            new Expression('0 as discount_amount'),
            new Expression('sum(promo_amount) as promo_amount'),
            new Expression('NUll as number_drivers_id'),
            new Expression(Product::products['kasko'] . ' as product'),
        ])
            ->where(['status' => [Kasko::STATUS['policy_generated'], Kasko::STATUS['canceled']]])
            ->andWhere(['>=', 'payed_date', date_create_from_format('Y-m-d H:i:s', $this->begin_date)->getTimestamp()])
            ->andWhere(['<=', 'payed_date', date_create_from_format('Y-m-d H:i:s', $this->end_date)->getTimestamp()])
            ->orderBy(['partner_id' => SORT_ASC])
            ->groupBy(['partner_id', 'status'])
            ->asArray()->all();

        $travels = Travel::find()->select([
            'partner_id', 'status', 'count(travel.id)', 'sum(amount_uzs) as amount',
            new Expression('0 as discount_amount'),
            new Expression('sum(promo_amount) as promo_amount'),
            new Expression('NUll as number_drivers_id'),
            new Expression(Product::products['travel'] . ' as product'),
        ])
            ->where(['status' => [Travel::STATUSES['received_policy'], Travel::STATUSES['canceled']]])
            ->andWhere(['>=', 'payed_date', date_create_from_format('Y-m-d H:i:s', $this->begin_date)->getTimestamp()])
            ->andWhere(['<=', 'payed_date', date_create_from_format('Y-m-d H:i:s', $this->end_date)->getTimestamp()])
            ->orderBy(['partner_id' => SORT_ASC])
            ->groupBy(['partner_id', 'status'])
            ->asArray()->all();

        $kbsp = KaskoBySubscriptionPolicy::find()->select([
            'kasko_by_subscription_policy.partner_id', 'kasko_by_subscription_policy.status', 'count(kasko_by_subscription_policy.id)', 'sum(kasko_by_subscription_policy.amount_uzs) as amount',
            new Expression('0 as discount_amount'),
            new Expression('sum(kasko_by_subscription.promo_amount) as promo_amount'),
            new Expression('NUll as number_drivers_id'),
            new Expression(Product::products['kasko-by-subscription'] . ' as product'),
        ])
            ->leftJoin(
                '(SELECT kasko_by_subscription_id, MIN(id) AS kasko_by_subscription_policy_id
                    FROM kasko_by_subscription_policy GROUP BY kasko_by_subscription_id) AS first_kasko_by_subscription_policy',
                'kasko_by_subscription_policy.kasko_by_subscription_id = first_kasko_by_subscription_policy.kasko_by_subscription_id 
                AND kasko_by_subscription_policy.id = first_kasko_by_subscription_policy.kasko_by_subscription_policy_id'
            )
            ->leftJoin('kasko_by_subscription', 'first_kasko_by_subscription_policy.kasko_by_subscription_id = kasko_by_subscription.id')
            ->where(['kasko_by_subscription_policy.status' => [KaskoBySubscriptionPolicy::STATUS['received_policy'], KaskoBySubscriptionPolicy::STATUS['canceled']]])
            ->andWhere(['>=', 'payed_date', $this->begin_date])
            ->andWhere(['<=', 'payed_date', $this->end_date])
            ->orderBy(['kasko_by_subscription_policy.partner_id' => SORT_ASC])
            ->groupBy(['kasko_by_subscription_policy.partner_id', 'kasko_by_subscription_policy.status'])
            ->asArray()->all();

        $partners = Partner::getForIdNameArrCollection(Partner::find()->all());
        $number_drivers = NumberDrivers::getShortArrCollection(NumberDrivers::find()->all());
        $product_types = Product::getIdNameCollection(Product::find()->all());

        $products = array_merge($osagos, $accidents, $kaskos, $travels, $kbsp);

        $_products = [];
        foreach ($products as $product) {
            if ($product['product'] == Product::products['osago'] or $product['product'] == Product::products['accident'])
                $status = $product['status'] == Osago::STATUS['received_policy'] ? 'success' : 'cancel';
            elseif ($product['product'] == Product::products['travel'])
                $status = $product['status'] == Travel::STATUSES['received_policy'] ? 'success' : 'cancel';
            elseif($product['product'] == Product::products['kasko'])
                $status = $product['status'] == Kasko::STATUS['policy_generated'] ? 'success' : 'cancel';
            elseif($product['product'] == Product::products['kasko-by-subscription'])
                $status = $product['status'] == KaskoBySubscriptionPolicy::STATUS['received_policy'] ? 'success' : 'cancel';

            $key = $product['partner_id'] . "_" . $product['product'] . "_" . $product['number_drivers_id'];
            $previous = $_products[$key] ?? [
                "partner" => null,
                "product" => null,
                "number_drivers" => null,
                "cancel_count" => 0,
                "cancel_amount" => 0,
                "cancel_amount_without_discounts" => 0,
                "success_count" => 0,
                "success_amount" => 0,
                "success_amount_without_discounts" => 0,
            ];

            $_products[$key] = array_merge(
                $previous,
                [
                    'partner' => $partners[$product['partner_id']],
                    'product' => $product_types[$product['product']],
                    'number_drivers' => $number_drivers[$product['number_drivers_id']] ?? null,
                    $status . '_count' => $product['count'],
                    $status . '_amount' => $product['amount'],
                    $status . '_amount_without_discounts' => $product['amount'] + $product['promo_amount'] + $product['discount_amount'],
                ]
            );
        }

        return array_values($_products);
    }

}