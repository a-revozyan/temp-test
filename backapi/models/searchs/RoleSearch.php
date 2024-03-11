<?php

namespace backapi\models\searchs;

use common\models\AuthItem;
use yii\data\ActiveDataProvider;
use yii\rbac\Item;

class RoleSearch extends AuthItem
{
    public $search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search'], 'string'],
            [['search'], 'filter', 'filter' => 'trim', 'skipOnEmpty' => true]
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
        $query = AuthItem::find()->where(['type' => Item::TYPE_ROLE]);

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'created_at', 'name'
                ],
                'defaultOrder' => ['created_at' => SORT_DESC]
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
            'or',
            ['ilike', 'name', $this->search],
            ['ilike', 'description', $this->search],
        ]);

        return $dataProvider;
    }
}
