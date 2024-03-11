<?php

namespace backapi\models\searchs;

use common\models\Autobrand;
use common\models\Product;
use common\models\Token;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

class FUserSearch extends Autobrand
{
    public $search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search'], 'string'],
        ];
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = (new Query())
            ->andWhere(Product::getPayedWhere())
            ->select([
                "concat(max(f_user.first_name), ' ', max(f_user.last_name)) as name",
                "max(f_user.phone) as phone",
                "max(f_user.gender) as gender",
                "max(f_user.comment) as comment",
                new Expression('EXTRACT(YEAR FROM age(max(f_user.birthday))) as age'),
                'products.f_user_id',
                new Expression("max(to_char(to_timestamp(f_user.created_at), 'mm.dd.YYYY HH24:MI:SS')) as created_at"),
                'count(token.telegram_tokens_count) > 0 as is_telegram',
                'sum(amount_uzs) as total_amount_uzs',
                'count(amount_uzs) as total_products_count',
                new Expression("max(to_char(to_timestamp(policy_generated_date), 'mm.dd.YYYY HH24:MI:SS')) as last_payed_date"),
                new Expression("string_agg(distinct product::text, ', ') as products")
            ])
            ->from(['products' => Product::getProductsQuery()])
            ->leftJoin('f_user', 'f_user.id = f_user_id')
            ->leftJoin([
                "token" => Token::find()->select([
                    "count(telegram_chat_id) as telegram_tokens_count",
                    "f_user_id"
                ])
                    ->where(['not', ['telegram_chat_id' => null]])
                    ->groupBy('f_user_id')
            ], '"token"."f_user_id" = "f_user"."id"')
            ->groupBy('products.f_user_id');

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'f_user_id', 'last_payed_date'
                ],
                'defaultOrder' => ['f_user_id' => SORT_DESC]
            ]
        ];
        if (is_null(\Yii::$app->request->get('page')))
            $providerConfig['pagination'] = false;

        $dataProvider = new ActiveDataProvider($providerConfig);

        $this->setAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions;
        $query->andFilterWhere([
            'or',
            ['ilike', 'phone', $this->search],
            ['ilike', new Expression("concat(first_name, ' ', last_name)"), $this->search]
        ]);

        return $dataProvider;
    }
}
