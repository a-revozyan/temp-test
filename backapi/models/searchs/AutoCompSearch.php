<?php

namespace backapi\models\searchs;

use common\models\Autocomp;
use common\models\Kasko;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class AutoCompSearch extends Autocomp
{
    public $autobrand_id;
    public $automodel_id;
    public $search;
    public $status;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['autobrand_id', 'automodel_id', 'status'], 'integer'],
            [['search'], 'string'],
            [['status'], 'in', 'range' => Autocomp::status],
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
        $query = Autocomp::find()->joinWith('automodel.autobrand');

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'automodel_id', 'automodel.autobrand_id', 'name', 'production_year', 'price', 'status'
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
            'automodel.autobrand_id' => $this->autobrand_id,
            'automodel_id' => $this->automodel_id,
        ]);

        $query->andFilterWhere([
            'or',
            ['ilike', new Expression( 'autocomp.id::text'), $this->search],
            ['ilike', 'automodel.name', $this->search],
            ['ilike', 'autobrand.name', $this->search],
            ['ilike', 'autocomp.name', $this->search],
            ['ilike', new Expression( 'production_year::text'), $this->search],
            ['ilike', new Expression( 'price::text'), $this->search],
        ]);
        $query->andFilterWhere(['autocomp.status' => $this->status]);

        return $dataProvider;
    }
}
