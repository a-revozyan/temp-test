<?php

namespace frontend\models\Searchs;

use common\models\KaskoBySubscription;
use yii\data\ActiveDataProvider;

class KaskoBySubscriptionSearch extends KaskoBySubscription
{
    public $f_user_id;
    public $status;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['f_user_id'], 'integer'],
            [['status'], 'each', 'rule' => ['integer']],
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
        $query = KaskoBySubscription::find()->with('lastKaskoBySubscriptionPolicy');

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id'
                ],
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ];

        $dataProvider = new ActiveDataProvider($providerConfig);

        $this->setAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['f_user_id' => $this->f_user_id]);
        $query->andFilterWhere(['in', 'kasko_by_subscription.status', $this->status]);

        return $dataProvider;
    }
}
