<?php

namespace frontend\models\Searchs;

use common\helpers\GeneralHelper;
use common\models\Qa;
use yii\data\ActiveDataProvider;

class QaSearch extends Qa
{
    public $search;
    public $page;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search'], 'string'],
            [['page'], 'in', 'range' => Qa::PAGES],
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
        $query = Qa::find()->where(['status' => Qa::STATUSES['active']]);

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [],
                'defaultOrder' => []
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

        $lang = GeneralHelper::lang_of_local();

        $query->andFilterWhere([
            'or',
            ['ilike', 'question_' . $lang, $this->search],
            ['ilike', 'answer_' . $lang, $this->search],
        ]);
        $query->andFilterWhere(['page' => $this->page]);

        return $dataProvider;
    }
}
