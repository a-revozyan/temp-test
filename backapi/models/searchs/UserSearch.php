<?php

namespace backapi\models\searchs;

use mdm\admin\models\User;
use yii\data\ActiveDataProvider;

class UserSearch extends User
{
    public $search;
    public $status;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search'], 'string'],
            [['status'], 'integer'],
            ['search', 'filter', 'filter' => 'trim', 'skipOnEmpty' => true]
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
        $query = User::find();

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'username', 'name', 'created_at', 'email', 'status'
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
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'or',
            ['ilike', 'username', $this->search],
            ['ilike', 'concat("first_name", \' \', "last_name")', $this->search],
            ['ilike', 'email', $this->search],
        ]);

        $query->andFilterWhere(['status' => $this->status]);

        return $dataProvider;
    }
}
