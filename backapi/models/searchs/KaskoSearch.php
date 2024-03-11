<?php

namespace backapi\models\searchs;

use common\models\Kasko;
use yii\data\ActiveDataProvider;

class KaskoSearch extends Kasko
{
    public $region_name;
    public $processed_date_from;
    public $processed_date_to;

    public $from_date;
    public $till_date;
    public $filter_by_created_at;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'status', 'id'], 'integer'],
            [['policy_number', 'region_name'], 'string'],
            [['from_date', 'till_date', 'processed_date_from', 'processed_date_to'], 'date', 'format' => 'php:Y-m-d'],
            [['filter_by_created_at'], 'default', 'value' => 0],
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
        $query = Kasko::find()->joinWith(['partner', 'surveyer.region', 'fUser', 'autocomp.automodel', 'trans']);

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'payed_date','year', 'amount_uzs', 'insurer_name', 'id', 'partner.name', 'status'
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
            'kasko.partner_id' => $this->partner_id,
            'kasko.status' => $this->status,
            'kasko.id' => $this->id,
        ]);

        $query->andFilterWhere([
            'and',
            ['ilike', 'policy_number', $this->policy_number],
            ['ilike', 'region.name_ru', $this->region_name],
        ]);

        if (!is_null($this->processed_date_to))
            $query->andWhere(['<=', 'processed_date', date_create_from_format('Y-m-d H:i:s', date($this->processed_date_to . " 23:59:59"))->getTimestamp()]);
        if (!is_null($this->processed_date_from))
            $query->andWhere(['>=', 'processed_date', date_create_from_format('Y-m-d H:i:s', date($this->processed_date_from . " 00:00:00"))->getTimestamp()]);

        if ($this->filter_by_created_at)
        {
            if (!is_null($this->till_date))
                $query->andWhere(['<=', 'created_at', date_create_from_format('Y-m-d H:i:s', date($this->till_date . " 23:59:59"))->getTimestamp()]);
            if (!is_null($this->from_date))
                $query->andWhere(['>=', 'created_at', date_create_from_format('Y-m-d H:i:s', date($this->from_date . " 00:00:00"))->getTimestamp()]);
        }else{
            if (!is_null($this->till_date))
                $query->andWhere(['<=', 'begin_date', date_create_from_format('Y-m-d H:i:s', date($this->till_date . " 23:59:59"))->format('Y-m-d')]);
            if (!is_null($this->from_date))
                $query->andWhere(['>=', 'begin_date', date_create_from_format('Y-m-d H:i:s', date($this->from_date . " 00:00:00"))->format('Y-m-d')]);
        }

        return $dataProvider;
    }
}
