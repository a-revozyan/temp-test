<?php
namespace backapi\models\forms\fUserForms;

use common\models\Accident;
use common\models\Kasko;
use common\models\KaskoBySubscription;
use common\models\Osago;
use common\models\Product;
use common\models\Token;
use common\models\Travel;
use common\models\User;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\VarDumper;

class Statistics extends Model
{
    public $from_date;
    public $till_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_date', 'till_date'], 'required'],
            [['from_date', 'till_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function save()
    {
        $users_query = User::find()
            ->where(['status' => User::STATUS_ACTIVE])
            ->andWhere(['role' => User::ROLES['user']]);
        $user_telegram_query = Token::find()
            ->where(['not', ['telegram_chat_id' => null]])
            ->andWhere(['token.status' => Token::STATUS['verified']])
            ->rightJoin('f_user', 'f_user.id = token.f_user_id');

        $user_count = $users_query->count();
        $user_telegram_count = $user_telegram_query->count("DISTINCT(f_user_id)");

        $average_age = 0;
        $users_have_birthday = (clone $users_query)->andWhere(['not', ['birthday' => null]])->count();
        if ($users_have_birthday != 0)
            $average_age = (clone $users_query)->andWhere(['not', ['birthday' => null]])->sum("date_part('year', age(birthday))")
                / $users_have_birthday;

        $female = 0;
        $male = 0;
        $users_have_gender = (clone $users_query)->andWhere(['not', ['gender' => null]])->count();
        if ($users_have_gender != 0)
        {
            $female = (clone $users_query)->andWhere(['gender' => 0])->count() / $users_have_gender * 100;
            $male = (clone $users_query)->andWhere(['gender' => 1])->count() / $users_have_gender * 100;
        }

        $travel = Travel::find()->where(['in', 'status', [
            Travel::STATUSES['payed'],
            Travel::STATUSES['waiting_for_policy'],
            Travel::STATUSES['received_policy'],
        ]]);
        $kasko = Travel::find()->where(['in', 'status', [
            Kasko::STATUS['payed'],
            Kasko::STATUS['attached'],
            Kasko::STATUS['processed'],
            Kasko::STATUS['policy_generated'],
        ]]);
        $osago = Osago::find()->where(['in', 'status', [Osago::STATUS['payed']]]);

        $average_policy_amount_uzs = "checksiz";
        $products_count = $travel->count() + $kasko->count() + $osago->count();
        if ($products_count != 0)
            $average_policy_amount_uzs = ($travel->sum('amount_uzs') + $kasko->sum('amount_uzs') + $osago->sum('amount_uzs'))
                / $products_count;

        $time_period_where_for_timestemp = [
            'and',
            ['>', 'created_at', date_create_from_format('Y-m-d H:i:s', date($this->from_date . " 00:00:00"))->getTimestamp()],
            ['<', 'created_at', date_create_from_format('Y-m-d H:i:s', date($this->till_date . " 23:59:59"))->getTimestamp()]
        ];

        $new_add_users = (clone $users_query)
            ->andWhere($time_period_where_for_timestemp)
            ->count();
        $new_add_telegram_users = (clone $user_telegram_query)
            ->andWhere([
                'and',
                ['>', 'f_user.created_at', date_create_from_format('Y-m-d H:i:s', date($this->from_date . " 00:00:00"))->getTimestamp()],
                ['<', 'f_user.created_at', date_create_from_format('Y-m-d H:i:s', date($this->till_date . " 23:59:59"))->getTimestamp()]
            ])
            ->count("DISTINCT(f_user_id)");

        $new_add_and_payed_users = (clone $users_query)
            ->andWhere($time_period_where_for_timestemp)
            ->leftJoin(
                ['products' =>
                    Product::getProductsQuery()
                        ->andWhere(Product::getPayedWhere())
                        ->select(["count('*') as count", "products.f_user_id"])
                    ->groupBy("products.f_user_id")
                ],
                "f_user.id = products.f_user_id"
            )
            ->andWhere(['>', 'products.count', 0])
            ->count();

        $top_regions = (new Query())
            ->andWhere(Product::getPayedWhere())
            ->andWhere(['not', ['region' => ['00', '0', '']]])
            ->select([
                "region, count(product_id)"
            ])
            ->from(['products' => Product::getProductsQuery()])
            ->groupBy('products.region')
            ->orderBy(['count' => SORT_DESC])
            ->all();

        $time_period_where_for_datetime = [
            'and',
            ['>', 'created_at', $this->from_date . " 00:00:00"],
            ['<', 'created_at', $this->till_date . " 23:59:59"]
        ];

        $logged_in_users_count = Token::find()
            ->andWhere(['status' => Token::STATUS['verified']])
            ->andWhere($time_period_where_for_datetime)
            ->count("DISTINCT(f_user_id)");

        $osago_passed_payment_page_statuses = [Osago::STATUS['step3'], Osago::STATUS['step4'], Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy'], Osago::STATUS['canceled']];
        $kasko_passed_payment_page_statuses = [Kasko::STATUS['step3'], Kasko::STATUS['step4'], Kasko::STATUS['payed'], Kasko::STATUS['attached'], Kasko::STATUS['processed'], Kasko::STATUS['policy_generated'], Kasko::STATUS['canceled']];
        $kbs_passed_payment_page_statuses = [KaskoBySubscription::STATUS['step3'], KaskoBySubscription::STATUS['step4'], KaskoBySubscription::STATUS['step5'], KaskoBySubscription::STATUS['step6'], KaskoBySubscription::STATUS['payed'], KaskoBySubscription::STATUS['canceled']];
        $travel_passed_payment_page_statuses = [Travel::STATUSES['step2'], Travel::STATUSES['step3'], Travel::STATUSES['payed'], Travel::STATUSES['waiting_for_policy'], Travel::STATUSES['received_policy'], Travel::STATUSES['canceled']];
        $products_passed_payment_page =
            Osago::find()->where(['in', 'status', $osago_passed_payment_page_statuses])->andWhere($time_period_where_for_timestemp)->count('DISTINCT(autonumber)')
            + Osago::find()->where(['in', 'status', $osago_passed_payment_page_statuses])->andWhere($time_period_where_for_timestemp)->andWhere(['not', ['accident_amount' => [null, 0]]])->count('DISTINCT(autonumber)')
            + Kasko::find()->where(['in', 'status', $kasko_passed_payment_page_statuses])->andWhere($time_period_where_for_timestemp)->count('DISTINCT(autonumber)')
            + KaskoBySubscription::find()->where(['in', 'status', $kbs_passed_payment_page_statuses])->andWhere($time_period_where_for_datetime)->count('DISTINCT(autonumber)')
            + Travel::find()->where(['in', 'status', $travel_passed_payment_page_statuses])->andWhere($time_period_where_for_timestemp)->count();

        $payed_products = Product::getProductsQuery()->andWhere(Product::getPayedWhere())->andWhere([
            'and',
            ['>', 'policy_generated_date', date_create_from_format('Y-m-d H:i:s', date($this->from_date . " 00:00:00"))->getTimestamp()],
            ['<', 'policy_generated_date', date_create_from_format('Y-m-d H:i:s', date($this->till_date . " 23:59:59"))->getTimestamp()]
        ])->count();

        $users_bought_more_than_2 = (new Query())
            ->select(['count(products.f_user_id)', 'f_user_id'])
            ->from(
                ['products' => Product::getProductsQuery()->andWhere(Product::getPayedWhere())->andWhere([
                    'and',
                    ['>', 'policy_generated_date', date_create_from_format('Y-m-d H:i:s', date($this->from_date . " 00:00:00"))->getTimestamp()],
                    ['<', 'policy_generated_date', date_create_from_format('Y-m-d H:i:s', date($this->till_date . " 23:59:59"))->getTimestamp()]
                ])]
            )
            ->groupBy(['products.f_user_id', 'products.product'])
            ->having(['>', 'count(products.f_user_id)', 1])
            ->count('distinct(f_user_id)');

        return [
            'user_count' => $user_count,
            'telegram_users_count' => $user_telegram_count,
            'new_added_telegram_users' => $new_add_telegram_users,
            'average_age' => round($average_age),
            'female' => $female,
            'male' => $male,
            'average_policy_amount_uzs' => is_string($average_policy_amount_uzs) ? $average_policy_amount_uzs : round($average_policy_amount_uzs, -3),
            'new_add_users' => $new_add_users,
            'new_add_and_payed_users' => $new_add_and_payed_users,
            'conversion' => $new_add_users == 0 ? 100 : round(($new_add_and_payed_users / $new_add_users) * 100),
            'top_regions' => $top_regions,
            'logged_in_users_count' => $logged_in_users_count,
            'products_passed_payment_page' => $products_passed_payment_page,
            'payed_products' => $payed_products,
            'users_bought_more_than_2' => $users_bought_more_than_2,
        ];
    }

}