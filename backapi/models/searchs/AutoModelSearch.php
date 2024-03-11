<?php

namespace backapi\models\searchs;

use common\models\Autobrand;
use common\models\Autocomp;
use common\models\Automodel;
use yii\data\ActiveDataProvider;

class AutoModelSearch extends Autobrand
{
    public $name;
    public $autobrand_id;
    public $status;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['autobrand_id', 'status'], 'integer'],
            [['name'], 'string'],
            [['status'], 'in', 'range' => Automodel::status],
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
        $query = Automodel::find()
            ->leftJoin('autobrand', '"automodel"."autobrand_id" = "autobrand"."id"');

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'name', 'order', 'status'
                ],
                'defaultOrder' => ['order' => SORT_ASC]
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
        $query->andFilterWhere(['autobrand_id' => $this->autobrand_id]);
        $query->andFilterWhere([
            'or',
            ['ilike', 'automodel.name', $this->name],
            ['ilike', 'autobrand.name', $this->name]
        ]);
        $query->andFilterWhere(['automodel.status' => $this->status]);

        return $dataProvider;
    }
}
