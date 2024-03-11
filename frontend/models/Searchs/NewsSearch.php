<?php

namespace frontend\models\Searchs;

use common\helpers\GeneralHelper;
use common\models\News;
use common\models\Tag;
use yii\data\ActiveDataProvider;

class NewsSearch extends News
{
    public $search;
    public $is_main;
    public $tag_ids;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search'], 'string'],
            [['is_main'], 'integer'],
            [['is_main'], 'in', 'range' => [0,1]],
            [['tag_ids'], 'each', 'rule' => ['integer']],
            [['tag_ids'], 'each', 'rule' => ['exist', 'skipOnError' => true, 'targetClass' => Tag::className(), 'targetAttribute' => ['tag_ids' => 'id']]],
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
        $query = News::find()->with(['tags']);

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'created_at', 'updated_at'
                ],
                'defaultOrder' => ['updated_at' => SORT_DESC]
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
        // grid filtering conditions
        $query->andFilterWhere([
            'or',
            ['ilike', 'title_' . $lang, $this->search],
            ['ilike', 'short_info_' . $lang, $this->search],
            ['ilike', 'body_' . $lang, $this->search],
        ]);
        $query->andFilterWhere(['is_main' => $this->is_main]);
        $query->andFilterWhere(['tags.id' => $this->tag_ids]);

        return $dataProvider;
    }
}
