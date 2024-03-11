<?php

namespace backapi\models\searchs;

use common\models\Osago;
use common\models\Travel;
use yii\data\ActiveDataProvider;

class TravelSearch extends Osago
{
    public $status;
    public $payment_type;
    public $search;
    public $from_date;
    public $till_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'partner_id'], 'each', 'rule' => ['integer']],
            [['payment_type'], 'each', 'rule' => ['string']],
            [['search', 'autonumber'], 'string'],
            [['from_date', 'till_date'], 'date', 'format' => 'php:Y-m-d'],
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
        $query = Travel::find()->joinWith(['user', 'trans', 'partner', 'travelMembers']);

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'payed_date', 'amount_uzs', 'status', 'user.phone', 'trans.payment_type', 'policy_number', 'partner.name', 'created_at'
                ],
                'defaultOrder' => ['id' => SORT_DESC]
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

        // grid filtering conditions
        $query->andFilterWhere([
            'transaction.payment_type' => $this->payment_type,
            'travel.status' => $this->status,
            'travel.partner_id' => $this->partner_id,
        ]);

        $filter_arr = [
            'or',
            ['ilike', 'f_user.phone', $this->search],
            ['ilike', 'travel.policy_number', $this->search],
        ];
        if (is_numeric($this->search) and (int)$this->search < 2147483647)
            $filter_arr = array_merge($filter_arr, [['=', 'travel.id', (int)$this->search]]);
        
        $query->andFilterWhere($filter_arr);

        if (!is_null($this->till_date))
            $query->andWhere(['<=', 'travel.payed_date', date_create_from_format('Y-m-d H:i:s', date($this->till_date . " 23:59:59"))->getTimestamp()]);
        if (!is_null($this->from_date))
            $query->andWhere(['>=', 'travel.payed_date', date_create_from_format('Y-m-d H:i:s', date($this->from_date . " 00:00:00"))->getTimestamp()]);

        return $dataProvider;
    }
}
