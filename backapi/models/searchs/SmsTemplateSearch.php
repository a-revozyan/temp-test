<?php

namespace backapi\models\searchs;

use common\models\SmsHistory;
use common\models\SmsTemplate;
use yii\data\ActiveDataProvider;

class SmsTemplateSearch extends SmsTemplate
{
    public $search;
    public $status;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['search'], 'string'],
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
        $query = SmsTemplate::find()
            ->select(['sms_template.*', 'COALESCE(sms_history_count.count, 0) as sms_count'])
            ->leftJoin('number_drivers', '"number_drivers"."id" = "sms_template"."number_drivers_id"')
            ->leftJoin(
                ['sms_history_count' => SmsHistory::find()->select(['count(id) as count', 'sms_template_id'])
                    ->where(['not', ['status' => SmsHistory::STATUS['created']]])->groupBy('sms_template_id')],
                '"sms_history_count"."sms_template_id" = "sms_template"."id"'
            );

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'status', 'all_users_count', 'sms_count'
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
        $query->andFilterWhere(['sms_template.status' => $this->status]);
        $query->andFilterWhere([
            'or',
            ['ilike', 'sms_template.text', $this->search],
            ['ilike', 'sms_template.method', $this->search],
            ['ilike', 'sms_template.region_car_numbers', $this->search],
        ]);

        return $dataProvider;
    }
}
