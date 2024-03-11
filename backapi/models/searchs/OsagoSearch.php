<?php

namespace backapi\models\searchs;

use common\models\Osago;
use yii\data\ActiveDataProvider;

class OsagoSearch extends Osago
{
    public $status;
    public $payment_type;
    public $search;
    public $from_date;
    public $till_date;
    public $is_juridic;
    public $with_discount;
    public $filter_by_created_at;
    public $bridge_company_id;
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
            [['with_discount', 'is_juridic', 'filter_by_created_at'], 'in', 'range' => [0, 1]],
            [['filter_by_created_at'], 'default', 'value' => 0],
            [['bridge_company_id'], 'integer']
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
        $query = Osago::find()->joinWith(['user', 'trans', 'partner', 'accident', 'osagoFondData', 'bridgeCompany']);

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'payed_date', 'amount_uzs', 'status', 'user.phone', 'trans.payment_type', 'policy_number', 'partner.name', 'autonumber', 'created_at'
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

        if (is_array($this->partner_id))
            $this->partner_id = array_filter($this->partner_id);
        // grid filtering conditions
        $query->andFilterWhere([
            'transaction.payment_type' => $this->payment_type,
            'osago.status' => $this->status,
            'osago.partner_id' => $this->partner_id,
        ]);

        $filter_arr = [
            'or',
            ['ilike', 'f_user.phone', $this->search],
            ['ilike', 'osago.policy_number', $this->search],
            ['ilike', 'osago.autonumber', $this->search],
        ];
        if (is_numeric($this->search) and (int)$this->search < 2147483647)
            $filter_arr = array_merge($filter_arr, [['=', 'osago.id', (int)$this->search]]);

        $query->andFilterWhere($filter_arr);

        $query->andFilterWhere(['osago.bridge_company_id' => $this->bridge_company_id]);

        $query->andFilterWhere(['ilike', 'autonumber', $this->autonumber]);

        if ($this->is_juridic == 1)
            $query->andFilterWhere(['is_juridic' => $this->is_juridic]);

        if ($this->with_discount == 1)
            $query->andWhere([
                'or',
                ['not', ['unique_code_id' => null]],
                ['not', ['osago.promo_id' => null]],
            ]);

        if ($this->filter_by_created_at)
        {
            if (!is_null($this->till_date))
                $query->andWhere(['<=', 'osago.created_at', date_create_from_format('Y-m-d H:i:s', date($this->till_date . " 23:59:59"))->getTimestamp()]);
            if (!is_null($this->from_date))
                $query->andWhere(['>=', 'osago.created_at', date_create_from_format('Y-m-d H:i:s', date($this->from_date . " 00:00:00"))->getTimestamp()]);
        }else{
            if (!is_null($this->till_date))
                $query->andWhere(['<=', 'osago.begin_date', date_create_from_format('Y-m-d H:i:s', date($this->till_date . " 23:59:59"))->format('Y-m-d')]);
            if (!is_null($this->from_date))
                $query->andWhere(['>=', 'osago.begin_date', date_create_from_format('Y-m-d H:i:s', date($this->from_date . " 00:00:00"))->format('Y-m-d')]);
        }

        return $dataProvider;
    }
}
